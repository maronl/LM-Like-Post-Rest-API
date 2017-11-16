<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMLikePostService;

class LMWPSavedPostPublicManager
{

    /**
     * /**
     * @var LMLikePostService
     */
    private $savedPostService;

    public function __construct($plugin_slug, $version, LMLikePostService $savedPostService)
    {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->namespace = $this->plugin_slug . '/v' . $this->version;
        $this->savedPostService = $savedPostService;
    }

    /**
     * Add the endpoints to the API
     */
    public function add_api_routes()
    {
        register_rest_route($this->namespace, 'saved/add', [
            'methods' => 'POST',
            'callback' => array($this, 'addSaved'),
        ]);

        register_rest_route($this->namespace, 'saved/remove', array(
            'methods' => 'POST',
            'callback' => array($this, 'removeSaved'),
        ));

        register_rest_route($this->namespace, 'saved/toggle', array(
            'methods' => 'POST',
            'callback' => array($this, 'toggleSaved'),
        ));
    }

    public function addSaved($request)
    {
        $userId = $request->get_param('user_id');
        $postId = $request->get_param('post_id');

        if (empty($userId) || empty($postId)) {
            return array('status' => false);
        }

        $status = $this->savedPostService->addLike($userId, $postId);
        return array('status' => $status);
    }

    public function removeSaved($request)
    {
        $userId = $request->get_param('user_id');
        $postId = $request->get_param('post_id');

        if (empty($userId) || empty($postId)) {
            return array('status' => false);
        }

        $status = $this->savedPostService->removeLike($userId, $postId);
        return array('status' => $status);
    }

    public function toggleSaved($request)
    {
        $userId = $request->get_param('user_id');
        $postId = $request->get_param('post_id');

        if (empty($userId) || empty($postId)) {
            return array('status' => false);
        }

        $dataLike = 0;
        if ($this->savedPostService->checkUserPostLike($userId, $postId)) {
            $this->removeSaved($request);
        } else {
            $this->addSaved($request);
            $dataLike = 1;
        }

        $res = array('status' => true, 'data' => $dataLike);

        return $res;
    }

}