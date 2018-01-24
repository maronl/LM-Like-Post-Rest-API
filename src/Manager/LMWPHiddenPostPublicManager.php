<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMHiddenPostService;

class LMWPHiddenPostPublicManager
{

    /**
     * @var LMHiddenPostService
     */
    private $hiddenPostService;

    /**
     * LMWPLikePostPublicManager constructor.
     * @param $plugin_slug
     * @param $version
     * @param LMHiddenPostService $hiddenPostService
     */
    public function __construct($plugin_slug, $version, LMHiddenPostService $hiddenPostService)
    {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->namespace = $this->plugin_slug . '/v' . $this->version;
        $this->hiddenPostService = $hiddenPostService;
    }

    /**
     * Add the endpoints to the API
     */
    public function add_api_routes()
    {
        register_rest_route($this->namespace, 'posts/hide', [
            'methods' => 'POST',
            'callback' => array($this, 'hidePost'),
        ]);

        register_rest_route($this->namespace, 'posts/show', array(
            'methods' => 'POST',
            'callback' => array($this, 'showPost'),
        ));
    }


    public function hidePost($request)
    {
        $userId = $request->get_param('user_id');
        $postId = $request->get_param('post_id');

        if (empty($userId) || empty($postId)) {
            return array('status' => false);
        }

        $status = $this->hiddenPostService->hidePost($userId, $postId);

        do_action('lm-sf-hidden-post', $userId, $postId);

        return array('status' => $status);
    }

    public function showPost($request)
    {
        $userId = $request->get_param('user_id');
        $postId = $request->get_param('post_id');

        if (empty($userId) || empty($postId)) {
            return array('status' => false);
        }

        $status = $this->hiddenPostService->showPost($userId, $postId);

        do_action('lm-sf-unhidden-post', $userId, $postId);

        return array('status' => $status);
    }

}