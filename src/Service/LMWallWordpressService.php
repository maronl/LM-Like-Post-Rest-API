<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:04
 */

namespace LM\WPPostLikeRestApi\Service;


use LM\WPPostLikeRestApi\Repository\LMWallPostWordpressRepository;
use LM\WPPostLikeRestApi\Request\LMWallPostInsertRequest;
use LM\WPPostLikeRestApi\Request\LMWallPostUpdateRequest;
use LM\WPPostLikeRestApi\Utility\LMHeaderAuthorization;
use LM\WPPostLikeRestApi\Utility\LMWPPostWallDetails;

class LMWallWordpressService implements LMWallService
{

    use LMWPPostWallDetails;

    /**
     * @var LMHeaderAuthorization
     */
    private $headerAuthorization;
    /**
     * @var LMLikePostService
     */
    private $likePostService;
    /**
     * @var LMLikePostService
     */
    private $savedPostService;
    /**
     * @var LMFollowerService
     */
    private $followerService;
    /**
     * @var LMWallPostWordpressRepository
     */
    private $wallPostWordpressRepository;
    /**
     * @var LMWallPostInsertRequest
     */
    private $insertRequest;
    /**
     * @var LMSharingService
     */
    private $sharingService;
    /**
     * @var LMBlockUserService
     */
    private $blockUserService;

    /**
     * LMWallWordpressService constructor.
     * @param LMHeaderAuthorization $headerAuthorization
     * @param LMWallPostWordpressRepository $wallPostWordpressRepository
     * @param LMFollowerService $followerService
     * @param LMLikePostService $likePostService
     * @param LMLikePostService $savedPostService
     * @param LMWallPostInsertRequest $insertRequest
     * @param LMSharingService $sharingService
     * @param LMBlockUserService $blockUserService
     */
    function __construct(
        LMHeaderAuthorization $headerAuthorization,
        LMWallPostWordpressRepository $wallPostWordpressRepository,
        LMFollowerService $followerService,
        LMLikePostService $likePostService,
        LMLikePostService $savedPostService,
        LMWallPostInsertRequest $insertRequest,
        LMSharingService $sharingService,
        LMBlockUserService $blockUserService
    ) {
        $this->headerAuthorization = $headerAuthorization;
        $this->likePostService = $likePostService;
        $this->savedPostService = $savedPostService;
        $this->followerService = $followerService;
        $this->wallPostWordpressRepository = $wallPostWordpressRepository;
        $this->insertRequest = $insertRequest;
        $this->sharingService = $sharingService;
        $this->blockUserService = $blockUserService;
    }

    public function getWall(Array $params)
    {
        $paramsQuery = $this->setWallQueryParameters($params);

        $paramsQuery['suppress_filters'] = false;

        // exclude hidden post
        add_filter('posts_join', array($this, 'filterJoinUserHiddenPost'));
        add_filter('posts_where', array($this, 'filterWhereUserHiddenPost'));

        // exclude users blocked
        $paramsQuery = $this->excludeBlockedUsersContent($paramsQuery);

        $posts = get_posts($paramsQuery);

        remove_filter('posts_join', array($this, 'filterJoinUserHiddenPost'));
        remove_filter('posts_where', array($this, 'filterWhereUserHiddenPost'));

        return $this->completePostsInformation($posts);
    }

    public function getPost($postId)
    {
        $post = get_post($postId);

        if (empty($post)) {
            return false;
        }

        if ($post->post_status !== 'publish') {
            return false;
        }

        return $this->retrievePostInformation($post, 3, $this->likePostService, $this->savedPostService,
            $this->sharingService, $this->wallPostWordpressRepository->getLMWallPostsPictureRepository(),
            $this->wallPostWordpressRepository->getLMWallPostsMovieRepository());
    }

    public function createPost($request)
    {
        $postId = $this->wallPostWordpressRepository->createPost($request);

        if (is_wp_error($postId) || is_array($postId)) {
            return $postId;
        }

        $this->setNewPostFormat($request, $postId);

        $this->setNewPostSharingPost($request, $postId);

        return $postId;
    }

    public function updatePostContent($request)
    {
        $postId = $this->wallPostWordpressRepository->updatePostContent($request);

        if (is_wp_error($postId) || is_array($postId)) {
            return $postId;
        }

        $this->setNewPostFormat($request, $postId);

        return $postId;
    }

    private function setWallQueryParameters(Array $params)
    {
        $paramsQuery = array();

        $paramsQuery['post_type'] = 'lm_wall';

        if (array_key_exists('q', $params)) {
            $paramsQuery['s'] = $params['q'];
        }

        if (array_key_exists('item_per_page', $params)) {
            $paramsQuery['posts_per_page'] = $params['item_per_page'];
        } else {
            $paramsQuery['posts_per_page'] = 20;
        }

        if (array_key_exists('page', $params)) {
            $paramsQuery['offset'] = ($params['page'] - 1) * $params['item_per_page'];
        }

        if (array_key_exists('categories', $params)) {
            $paramsQuery['cat'] = $params['categories'];
        }

        if (array_key_exists('authors', $params)) {
            $paramsQuery['author'] = $params['authors'];
        } elseif(!$this->isRedazioneUser()){
            $paramsQuery['author'] = implode(',', $this->getDefaultAuthorsPerUser());
        }

        if (array_key_exists('date_query', $params)) {
            $paramsQuery['date_query'] = $params['date_query'];
        }

        if (array_key_exists('tax_query', $params)) {
            $paramsQuery['tax_query'] = $params['tax_query'];
        }

        if (array_key_exists('saved_by_user', $params)) {
            $posts = $this->savedPostService->getPostIdsLikeByUser($params['saved_by_user']);
            $paramsQuery['post__in'] = $posts;
        }

        return $paramsQuery;
    }

    private function completePostsInformation(Array $posts = array())
    {
        if (empty($posts)) {
            return array();
        }

        $res = array();

        foreach ($posts as $post) {
            $res[] = $this->retrievePostInformation($post, 3, $this->likePostService, $this->savedPostService,
                $this->sharingService, $this->wallPostWordpressRepository->getLMWallPostsPictureRepository(),
                $this->wallPostWordpressRepository->getLMWallPostsMovieRepository());
        }

        return $res;
    }

    private function getDefaultAuthorsPerUser()
    {
        // return redazione todo mettere come paramentro configurabile
        $authors = array(1);

        $userAuthorized = $this->headerAuthorization->getUser();

        $authors = array_merge($authors, array($userAuthorized));

        if ($userAuthorized) {
            $followings = $this->followerService->getFollowingsIds($userAuthorized);
            $authors = array_merge($authors, $followings);
        }

        $authors = array_unique($authors);

        return $authors;
    }

    /**
     * @param $request
     * @param $postId
     * @return bool
     */
    private function setNewPostFormat($request, $postId)
    {
        $dataRequest = $this->insertRequest->getDataFromRequest($request);

        if (array_key_exists('format', $dataRequest)) {
            set_post_format($postId, $dataRequest['format']);
        }

        return true;
    }

    private function setNewPostSharingPost($request, $postId)
    {
        $dataRequest = $this->insertRequest->getDataFromRequest($request);

        if (array_key_exists('shared_post', $dataRequest) && !empty($dataRequest['shared_post'])) {
            $this->sharingService->saveSharing($dataRequest['shared_post'], $postId);
        }

        return true;
    }

    public function filterJoinUserHiddenPost($join)
    {
        // todo rimuove riferienti alla tabella codificati
        $userQuerying = $this->headerAuthorization->getUser();
        $join .= " LEFT JOIN pld_lm_post_hidden as ph ON ph.post_id = pld_posts.ID AND ph.user_id = $userQuerying ";
        return $join;
    }

    public function filterWhereUserHiddenPost($where)
    {
        $where .= " AND ph.created_at IS NULL ";
        return $where;
    }

    private function excludeBlockedUsersContent($paramsQuery)
    {
        if (array_key_exists('author', $paramsQuery)) {
            $authors = $paramsQuery['author'];
            $authors = explode(',', $authors);
            $blockedUsers = $this->blockUserService->getBlockedUsers($this->headerAuthorization->getUser());
            $authors = array_diff($authors, $blockedUsers);

            if(empty($authors)) {
                // imposto id utente che non esisterà mai (si spera) per non avere risultati
                $authors[] = 999999999999999999999;
            }

            $paramsQuery['author'] = implode(',',$authors);
            return $paramsQuery;
        }
    }

    private function isRedazioneUser()
    {
        // todo id della redazione dovrebbe essere sempre una configurazione esterna come già segnato in altri punti
        $userAuthorized = $this->headerAuthorization->getUser();

        $redazioneUsers = array(1);
        if(in_array($userAuthorized, $redazioneUsers)) {
            return true;
        }

        return false;
    }

}
