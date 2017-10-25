<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMProfileService;
use LM\WPPostLikeRestApi\Service\LMWallService;

class LMWPProfilePublicManager
{
    /**
     * @var LMWallService
     */
    private $profileService;

    public function __construct($plugin_slug, $version,  LMProfileService $profileService)
    {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->namespace = $this->plugin_slug . '/v' . $this->version;
        $this->profileService = $profileService;
    }

    /**
     * Add the endpoints to the API
     */
    public function add_api_routes()
    {
        register_rest_route($this->namespace, 'me', [
            'methods' => 'GET',
            'callback' => array($this, 'getLoggedUserProfile'),
        ]);

        register_rest_route($this->namespace, 'profile/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => array($this, 'getLoggedUserProfile'),
        ]);
    }

    public function getLoggedUserProfile($request)
    {
        $profile = $this->profileService->getLoggedUserProfile();
        return array('status' => true, 'data' => $profile);
    }

}