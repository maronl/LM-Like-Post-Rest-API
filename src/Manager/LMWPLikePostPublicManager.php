<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMLikePostService;

class LMWPLikePostPublicManager
{

    /**
     * @var LMLikePostService
     */
    private $likePostService;

    public function __construct($plugin_slug, $version, LMLikePostService $likePostService)
    {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->namespace = $this->plugin_slug . '/v' . $this->version;
        $this->likePostService = $likePostService;
    }

    /**
     * Add the endpoints to the API
     */
    public function add_api_routes()
    {
        register_rest_route($this->namespace, 'like/add', [
            'methods' => 'POST',
            'callback' => array($this, 'addLike'),
        ]);

        register_rest_route($this->namespace, 'like/remove', array(
            'methods' => 'POST',
            'callback' => array($this, 'removeLike'),
        ));

        register_rest_route($this->namespace, 'like/toggle', [
            'methods' => 'POST',
            'callback' => array($this, 'toggleLike'),
        ]);

        register_rest_route($this->namespace, 'posts/(?P<id>\d+)/likes', [
            'methods' => 'GET',
            'callback' => array($this, 'listUserLikePost'),
        ]);
    }


    public function addLike($request)
    {
        $userId = $request->get_param('user_id');
        $postId = $request->get_param('post_id');

        if (empty($userId) || empty($postId)) {
            return array('status' => false);
        }

        $status = $this->likePostService->addLike($userId, $postId);

        do_action('lm-sf-added-like', $userId, $postId);

        return array('status' => $status);
    }

    public function removeLike($request)
    {
        $userId = $request->get_param('user_id');
        $postId = $request->get_param('post_id');

        if (empty($userId) || empty($postId)) {
            return array('status' => false);
        }

        $status = $this->likePostService->removeLike($userId, $postId);

        do_action('lm-sf-removed-like', $userId, $postId);

        return array('status' => $status);
    }

    public function toggleLike($request)
    {
        $userId = $request->get_param('user_id');
        $postId = $request->get_param('post_id');

        if (empty($userId) || empty($postId)) {
            return array('status' => false);
        }

        $dataLike = 0;
        if ($this->likePostService->checkUserPostLike($userId, $postId)) {
            $this->removeLike($request);
        } else {
            $this->addLike($request);
            $dataLike = 1;
        }

        $res = array('status' => true, 'data' => $dataLike);

        return $res;
    }

    public function listUserLikePost($request)
    {
        $postId = $request->get_param('id');

        $users = $this->likePostService->getUsersLikePost($postId);

        $res = array('status' => true, 'data' => $users);

        return $res;
    }


}