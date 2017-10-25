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

        register_rest_route($this->namespace, 'wall', [
            'methods' => 'POST',
            'callback' => array($this, 'createPost'),
        ]);

        register_rest_route($this->namespace, 'wall/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => array($this, 'getPost'),
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

    public function getPost($request)
    {
        $postId = $request->get_param('id');
        return array('status' => true, 'data' => $this->wallService->getPost($postId));
    }

    public function createPost($request)
    {
        $postId = $this->wallService->createPost($request);
        if(is_wp_error($postId)) {
            return array('status' => false, 'data' => $postId->errors);
        }
        if(is_array($postId)) {
            return array('status' => false, 'data' => $postId);
        }
        return array('status' => true, 'data' => $this->wallService->getPost($postId));
    }

    public function incrementCountSharedPost( $post_id, $post, $update ) {

        global $wpdb;

        if($post->post_parent === 0) {
            return;
        }

        $sql = $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "posts WHERE post_parent = %d AND post_status = 'publish'", $post->post_parent);
        $count = $wpdb->get_var($sql);
        return update_post_meta($post_id, 'lm-shared-post-counter', $count);
    }

}