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

    function __construct(
        LMHeaderAuthorization $headerAuthorization,
        LMWallPostWordpressRepository $wallPostWordpressRepository,
        LMFollowerService $followerService,
        LMLikePostService $likePostService,
        LMLikePostService $savedPostService,
        LMWallPostInsertRequest $insertRequest,
        LMSharingService $sharingService
    ) {
        $this->headerAuthorization = $headerAuthorization;
        $this->likePostService = $likePostService;
        $this->savedPostService = $savedPostService;
        $this->followerService = $followerService;
        $this->wallPostWordpressRepository = $wallPostWordpressRepository;
        $this->insertRequest = $insertRequest;
        $this->sharingService = $sharingService;
    }

    public function getWall(Array $params)
    {
        $paramsQuery = $this->setWallQueryParameters($params);
        return $this->completePostsInformation(get_posts($paramsQuery));
    }

    public function getPost($postId)
    {
        $post = get_post($postId);

        if(empty($post)) {
            return false;
        }

        if($post->post_status !== 'publish') {
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
        } else {
            $paramsQuery['author'] = implode(',', $this->getDefaultAuthorsPerUser());
        }

        if (array_key_exists('date_query', $params)) {
            $paramsQuery['date_query'] = $params['date_query'];
        }

        if (array_key_exists('tax_query', $params)) {
            $paramsQuery['tax_query'] = $params['tax_query'];
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

}
