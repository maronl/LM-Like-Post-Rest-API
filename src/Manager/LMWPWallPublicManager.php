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

    public function __construct($plugin_slug, $version, LMWallService $wallService)
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

        register_rest_route($this->namespace, 'wall/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => array($this, 'updatePost'),
        ]);
    }

    public function getWall($request)
    {
        $params = array();

        $search = $request->get_param('q');
        if (!empty($search)) {
            $params['q'] = $search;
        }

        $item_per_page = $request->get_param('item_per_page');
        if (!empty($item_per_page)) {
            $params['item_per_page'] = $item_per_page;
        }
        $page = $request->get_param('page');
        if (!empty($page)) {
            $params['page'] = $page;
        }
        $categories = $request->get_param('categories');
        if (!empty($categories)) {
            $params['categories'] = $categories;
        }
        $authors = $request->get_param('authors');
        if (!empty($authors)) {
            $params['authors'] = $authors;
        }
        $before = $request->get_param('before');
        if (!empty($before)) {
            // aggiungo un secondo così da poter conteggiare anche il post con la data indicata
            $before = \DateTime::createFromFormat('Y-m-d H:i:s', $before);
            $before->add(new \DateInterval('PT1S'));
            $before = $before->format('Y-m-d H:i:s');
            $params['date_query'] = array(
                array('before' => $before)
            );
        }


        $posts = $this->wallService->getWall($params);

        if (has_filter('lm-sf-rest-api-get-wall')) {
            $posts = apply_filters('lm-sf-rest-api-get-wall', $posts);
        }

        return new \WP_REST_Response(array('status' => true, 'data' => $posts), 200);
    }

    public function getPost($request)
    {
        $postId = $request->get_param('id');
        $post = $this->wallService->getPost($postId);
        if ($post === false) {
            return new \WP_REST_Response(array('msg' => 'post not found'), 404);

        }
        return new \WP_REST_Response(array('status' => true, 'data' => $this->wallService->getPost($postId)), 200);
    }

    public function createPost($request)
    {
        $postId = $this->wallService->createPost($request);

        if (is_wp_error($postId)) {
            return new \WP_REST_Response(array('status' => false, 'data' => $postId->errors), 422);
        }

        if (is_array($postId)) {
            return new \WP_REST_Response(array('status' => false, 'data' => $postId), 422);
        }

        $post = $this->wallService->getPost($postId);

        do_action('lm-sf-created-post', $post);

        return new \WP_REST_Response(array('status' => true, 'data' => $post), 200);
    }

    public function updatePost($request)
    {
        $postId = $this->wallService->updatePostContent($request);

        if (is_wp_error($postId)) {
            return new \WP_REST_Response(array('status' => false, 'data' => $postId->errors), 422);
        }

        if (is_array($postId)) {
            return new \WP_REST_Response(array('status' => false, 'data' => $postId), 422);
        }

        $post = $this->wallService->getPost($postId);

        do_action('lm-sf-updated-post', $post);

        return new \WP_REST_Response(array('status' => true, 'data' => $post), 200);
    }

    public function incrementCountSharedPost($post_id, $post, $update)
    {

        global $wpdb;

        if ($post->post_parent === 0) {
            return;
        }

        $sql = $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "posts WHERE post_parent = %d AND post_status = 'publish'",
            $post->post_parent);
        $count = $wpdb->get_var($sql);
        return update_post_meta($post_id, 'lm-shared-post-counter', $count);
    }

}