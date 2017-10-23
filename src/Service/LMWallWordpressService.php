<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:04
 */

namespace LM\WPPostLikeRestApi\Service;


use LM\WPPostLikeRestApi\Utility\LMHeaderAuthorization;

class LMWallWordpressService implements LMWallService
{
    /**
     * @var LMHeaderAuthorization
     */
    private $headerAuthorization;
    /**
     * @var LMLikePostService
     */
    private $likePostService;
    /**
     * @var LMLikePostService
     */
    private $savedPostService;
    /**
     * @var LMFollowerService
     */
    private $followerService;

    function __construct(LMHeaderAuthorization $headerAuthorization, LMFollowerService $followerService, LMLikePostService $likePostService, LMLikePostService $savedPostService)
    {
        $this->headerAuthorization = $headerAuthorization;
        $this->likePostService = $likePostService;
        $this->savedPostService = $savedPostService;
        $this->followerService = $followerService;
    }

    public function getWall(Array $params)
    {
        $paramsQuery = $this->setWallQueryParameters($params);
        return $this->completePostsInformation(get_posts($paramsQuery));
    }

    public function getPost($postId) {
        return $this->retrievePostInformation(get_post($postId), null);
    }

    private function setWallQueryParameters(Array $params)
    {
        $paramsQuery = array();

        if(array_key_exists('item_per_page', $params)) {
            $paramsQuery['posts_per_page'] = $params['item_per_page'];
        } else {
            $paramsQuery['posts_per_page'] = 20;
        }

        if(array_key_exists('page', $params)) {
            $paramsQuery['offset'] = ($params['page'] - 1) * $params['item_per_page'];
        }

        if(array_key_exists('categories', $params)) {
            $paramsQuery['cat'] = $params['categories'];
        } else {
            $paramsQuery['author'] = $this->getDefaultCategoriesPerUser();
        }

        if(array_key_exists('authors', $params)) {
            $paramsQuery['author'] = $params['authors'];
        } else {
            $paramsQuery['author'] = implode(',', $this->getDefaultAuthorsPerUser());
        }

        return $paramsQuery;
    }

    private function completePostsInformation(Array $posts = array())
    {
        if(empty($posts)) {
            return array();
        }

        $res = array();

        foreach ($posts as $post) {
            $res[] = $this->retrievePostInformation($post);
        }

        return $res;
    }

    private function retrievePostInformation(\WP_Post $post, $latestComments = 3)
    {
        global $wpdb;

        $post->post_content_rendered = apply_filters('the_content', $post->post_content);

        $post->post_excerpt_rendered = apply_filters('the_excerpt', $post->post_excerpt);

        $post->author = $this->retrieveAuthorInformation($post, $wpdb);

        $post->latest_comment = get_comments(array('post_id' => $post->ID, 'number' => $latestComments, 'orderby' => 'comment_date', 'order' => 'DESC'));

        // li ricerco per date DESC ... così da prendere gli ultimi
        // ma poi li visualizzo in data ASC quelli trovati. per questo faccio il reverse
        $post->latest_comment = array_reverse($post->latest_comment );

        $post->categories = get_the_terms($post->ID, 'category');

        $post->lm_like_counter = $this->retrieveLikeCounter($post);

        $post->lm_saved_counter = $this->retrieveSavedCounter($post);

        $post->featured_image = get_the_post_thumbnail_url($post->ID);

        $post->liked = $this->likePostService->checkUserPostLike(get_current_user_id(), $post->ID);

        $post->saved = $this->savedPostService->checkUserPostLike(get_current_user_id(), $post->ID);

        return $post;
    }

    /**
     * @param \WP_Post $post
     * @param $wpdb
     */
    private function retrieveAuthorInformation(\WP_Post $post, $wpdb)
    {
        $sql = $wpdb->prepare("SELECT u.ID, u.user_login, u.display_name, u.user_email, u.user_registered, u.user_status
            FROM pld_users as u
            WHERE u.ID = %d;", $post->post_author);

        return $wpdb->get_row($sql);
    }

    /**
     * @param \WP_Post $post
     * @return int
     */
    private function retrieveLikeCounter(\WP_Post $post)
    {
        $like_counter = get_post_meta($post->ID, 'lm-like-counter', true);
        if (empty($like_counter)) {
            $like_counter = 0;
        }
        return $like_counter;
    }

    /**
     * @param \WP_Post $post
     * @return int
     */
    private function retrieveSavedCounter(\WP_Post $post)
    {
        $saved_counter = get_post_meta($post->ID, 'lm-saved-counter', true);
        if (empty($saved_counter)) {
            $saved_counter = 0;
        }
        return $saved_counter;
    }

    private function getDefaultCategoriesPerUser()
    {
        // todo mettere come parametro configurabile
        return array(2);
    }

    private function getDefaultAuthorsPerUser()
    {
        // return redazione todo mettere come paramentro configurabile
        $authors = array(1);


        $userAuthorized = $this->headerAuthorization->getUser();

        if ($userAuthorized) {
            $followings = $this->followerService->getFollowingsIds($userAuthorized);
            $authors = array_merge($authors, $followings);
        }

        return $authors;
    }

}
