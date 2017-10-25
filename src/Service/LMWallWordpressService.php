<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:04
 */

namespace LM\WPPostLikeRestApi\Service;


use LM\WPPostLikeRestApi\Repository\LMWallPostWordpressRepository;
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

    function __construct(LMHeaderAuthorization $headerAuthorization, LMWallPostWordpressRepository $wallPostWordpressRepository, LMFollowerService $followerService, LMLikePostService $likePostService, LMLikePostService $savedPostService)
    {
        $this->headerAuthorization = $headerAuthorization;
        $this->likePostService = $likePostService;
        $this->savedPostService = $savedPostService;
        $this->followerService = $followerService;
        $this->wallPostWordpressRepository = $wallPostWordpressRepository;
    }

    public function getWall(Array $params)
    {
        $paramsQuery = $this->setWallQueryParameters($params);
        return $this->completePostsInformation(get_posts($paramsQuery));
    }

    public function getPost($postId) {
        return $this->retrievePostInformation(get_post($postId), null, $this->likePostService, $this->savedPostService);
    }

    public function createPost($request)
    {
        return $this->wallPostWordpressRepository->createPost($request);
    }

    private function setWallQueryParameters(Array $params)
    {
        $paramsQuery = array();

        $paramsQuery['post_type'] = 'lm_wall';

        if(array_key_exists('item_per_page', $params)) {
            $paramsQuery['posts_per_page'] = $params['item_per_page'];
        } else {
            $paramsQuery['posts_per_page'] = 20;
        }

        if(array_key_exists('page', $params)) {
            $paramsQuery['offset'] = ($params['page'] - 1) * $params['item_per_page'];
        }

        if(array_key_exists('categories', $params)) {
            $paramsQuery['cat'] = $params['categories'];
        }

        if(array_key_exists('authors', $params)) {
            $paramsQuery['author'] = $params['authors'];
        } else {
            $paramsQuery['author'] = implode(',', $this->getDefaultAuthorsPerUser());
        }

        return $paramsQuery;
    }

    private function completePostsInformation(Array $posts = array())
    {
        if(empty($posts)) {
            return array();
        }

        $res = array();

        foreach ($posts as $post) {
            $res[] = $this->retrievePostInformation($post, 3, $this->likePostService, $this->savedPostService);
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

}
