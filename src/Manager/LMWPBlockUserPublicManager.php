<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMBlockUserService;

class LMWPBlockUserPublicManager
{

    /**
     * @var LMBlockUserService
     */
    private $blockUserService;
    private $version;
    private $namespace;
    private $plugin_slug;

    /**
     * LMWPLikePostPublicManager constructor.
     * @param $plugin_slug
     * @param $version
     * @param LMBlockUserService $blockUserService
     */
    public function __construct($plugin_slug, $version, LMBlockUserService $blockUserService)
    {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->namespace = $this->plugin_slug . '/v' . $this->version;
        $this->blockUserService = $blockUserService;
    }

    /**
     * Add the endpoints to the API
     */
    public function add_api_routes()
    {
        register_rest_route($this->namespace, 'users/block', [
            'methods' => 'POST',
            'callback' => array($this, 'blockUser'),
        ]);

        register_rest_route($this->namespace, 'users/unblock', array(
            'methods' => 'POST',
            'callback' => array($this, 'unblockUser'),
        ));
    }


    public function blockUser($request)
    {
        $blockingId = $request->get_param('user_id');
        $blockedId = $request->get_param('blocked_user_id');

        if (empty($blockingId) || empty($blockedId)) {
            return array('status' => false);
        }

        $status = $this->blockUserService->blockUser($blockingId, $blockedId);

        do_action('lm-sf-blocked-user', $blockingId, $blockedId);

        return array('status' => $status);
    }

    public function unblockUser($request)
    {
        $blockingId = $request->get_param('user_id');
        $blockedId = $request->get_param('blocked_user_id');

        if (empty($blockingId) || empty($blockedId)) {
            return array('status' => false);
        }

        $status = $this->blockUserService->unblockUser($blockingId, $blockedId);

        do_action('lm-sf-unblocked-user', $blockingId, $blockedId);

        return array('status' => $status);
    }

}