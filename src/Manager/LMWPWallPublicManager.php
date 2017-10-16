<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMWallService;

class LMWPWallPublicManager
{
    /**
     * @var LMWallService
     */
    private $wallService;

    public function __construct($plugin_slug, $version,  LMWallService $wallService)
    {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->namespace = $this->plugin_slug . '/v' . $this->version;
        $this->wallService = $wallService;
    }

    /**
     * Add the endpoints to the API
     */
    public function add_api_routes()
    {
        register_rest_route($this->namespace, 'wall', [
            'methods' => 'GET',
            'callback' => array($this, 'getWall'),
        ]);

    }

    public function getWall($request)
    {
        $params = array();
        $item_per_page = $request->get_param('item_per_page');
        if(!empty($item_per_page)) {
            $params['item_per_page'] = $item_per_page;
        }
        $page = $request->get_param('page');
        if(!empty($page)) {
            $params['page'] = $page;
        }
        $categories = $request->get_param('categories');
        if(!empty($categories)) {
            $params['categories'] = $categories;
        }
        $authors = $request->get_param('authors');
        if(!empty($authors)) {
            $params['authors'] = $authors;
        }

        $posts = $this->wallService->getWall($params);
        return array('status' => true, 'data' => $posts);
    }
}