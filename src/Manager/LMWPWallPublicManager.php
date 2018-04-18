<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMSharingWordpressService;
use LM\WPPostLikeRestApi\Service\LMWallService;
use LM\WPPostLikeRestApi\Utility\LMWPJWTFirebaseHeaderAuthorization;

class LMWPWallPublicManager
{
    /**
     * @var LMWallService
     */
    private $wallService;
    /**
     * @var LMSharingWordpressService
     */
    private $sharingWordpressService;
    /**
     * @var LMWPJWTFirebaseHeaderAuthorization
     */
    private $headerAuthorization;

    /**
     * LMWPWallPublicManager constructor.
     * @param $plugin_slug
     * @param $version
     * @param LMWallService $wallService
     * @param LMSharingWordpressService $sharingWordpressService
     */
    public function __construct(
        $plugin_slug,
        $version,
        LMWallService $wallService,
        LMSharingWordpressService $sharingWordpressService,
        LMWPJWTFirebaseHeaderAuthorization $headerAuthorization

    ) {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->namespace = $this->plugin_slug . '/v' . $this->version;
        $this->wallService = $wallService;
        $this->sharingWordpressService = $sharingWordpressService;
        $this->headerAuthorization = $headerAuthorization;
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

        register_rest_route($this->namespace, 'wall/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => array($this, 'deletePost'),
        ]);

        register_rest_route($this->namespace, 'posts/(?P<id>\d+)/shared/users', [
            'methods' => 'GET',
            'callback' => array($this, 'listUsersSharedPost'),
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
        $savedByUser = $request->get_param('saved_by_user');
        if (!empty($savedByUser)) {
            $params['saved_by_user'] = $savedByUser;
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

        if (defined('LM_REST_API_FORCE_EXCLUDE_WALL_CATEGORIES') && $request->get_param('disable_force_exclude_wall_cat') !== 'true') {
            $params['tax_query'] = array(
                array(
                    'taxonomy' => 'lm_wall_category',
                    'field' => 'id',
                    'terms' => explode(',', LM_REST_API_FORCE_EXCLUDE_WALL_CATEGORIES),
                    'operator' => 'NOT IN',
                ),
            );
        }

        if (has_filter('lm-sf-rest-api-wall-query-params')) {
            $params = apply_filters('lm-sf-rest-api-wall-query-params', $params);
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

    public function deletePost($request)
    {
        $userId = $this->headerAuthorization->getUser();

        $postId = $request->get_param('id', $userId);

        $post = $this->wallService->getPost($postId);

        if (empty($post)) {
            return new \WP_REST_Response(array('status' => false, 'msg' => 'post not found'), 404);
        }

        if ($userId != $post->post_author) {
            return new \WP_REST_Response(array('status' => false, 'msg' => 'action forbidden'), 403);
        }

        if (wp_delete_post($postId, true) === false) {
            return new \WP_REST_Response(array('status' => true, 'msg' => 'Error deleting the post'), 500);
        }

        return new \WP_REST_Response(array('status' => true), 200);
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

    public function listUsersSharedPost($request)
    {
        $postId = $request->get_param('id');

        $users = $this->sharingWordpressService->getUsersSharedPost($postId);

        $res = array('status' => true, 'data' => $users);

        return $res;
    }

}