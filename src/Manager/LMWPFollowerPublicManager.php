<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMFollowerService;

class LMWPFollowerPublicManager
{

    /**
    /**
     * @var LMFollowerService
     */
    private $followerService;

    public function __construct($plugin_slug, $version,  LMFollowerService $followerService)
    {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->namespace = $this->plugin_slug . '/v' . $this->version;
        $this->followerService = $followerService;
    }

    /**
     * Add the endpoints to the API
     */
    public function add_api_routes()
    {
        register_rest_route($this->namespace, 'follower/add', [
            'methods' => 'POST',
            'callback' => array($this, 'addFollower'),
        ]);

        register_rest_route($this->namespace, 'follower/remove', array(
            'methods' => 'POST',
            'callback' => array($this, 'removeFollower'),
        ));

        register_rest_route($this->namespace, 'followers', array(
            'methods' => 'GET',
            'callback' => array($this, 'getFollowers'),
        ));

        register_rest_route($this->namespace, 'followings', array(
            'methods' => 'GET',
            'callback' => array($this, 'getFollowings'),
        ));

        register_rest_route($this->namespace, 'followers/count', array(
            'methods' => 'GET',
            'callback' => array($this, 'getFollowersCount'),
        ));

        register_rest_route($this->namespace, 'followings/count', array(
            'methods' => 'GET',
            'callback' => array($this, 'getFollowingsCount'),
        ));

    }

    public function addFollower($request)
    {
        $followerId = $request->get_param('follower_id');
        $followingId = $request->get_param('following_id');

        if(empty($followerId) || empty($followingId)) {
            return array('status' => false);
        }

        $status = $this->followerService->addFollower($followerId, $followingId);
        return array('status' => $status);
    }

    public function removeFollower($request)
    {
        $followerId = $request->get_param('follower_id');
        $followingId = $request->get_param('following_id');

        if(empty($followerId) || empty($followingId)) {
            return array('status' => false);
        }

        $status = $this->followerService->removeFollower($followerId, $followingId);
        return array('status' => $status);
    }

    public function getFollowers($request)
    {
        $followingId = $request->get_param('following_id');

        if(empty($followingId)) {
            return array('status' => false);
        }

        $followers = $this->followerService->getFollowers($followingId);
        return array('status' => true, 'data' => $followers);
    }

    public function getFollowings($request)
    {
        $followerId = $request->get_param('follower_id');

        if(empty($followerId)) {
            return array('status' => false);
        }

        $followings = $this->followerService->getFollowings($followerId);
        return array('status' => true, 'data' => $followings);
    }

    public function getFollowersCount($request)
    {
        $followingId = $request->get_param('following_id');

        if(empty($followingId)) {
            return array('status' => false);
        }

        $followers = $this->followerService->getFollowersCount($followingId);
        return array('status' => true, 'data' => $followers);
    }

    public function getFollowingsCount($request)
    {
        $followerId = $request->get_param('follower_id');

        if(empty($followerId)) {
            return array('status' => false);
        }

        $followings = $this->followerService->getFollowingsCount($followerId);
        return array('status' => true, 'data' => $followings);
    }
}