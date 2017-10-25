<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 12:14
 */

namespace LM\WPPostLikeRestApi\Utility;

trait LMWPPostWallDetails
{

    private function retrievePostInformation(\WP_Post $post, $latestComments = 3, $likePostService, $savedPostService)
    {
        global $wpdb;

        $post->post_content_rendered = apply_filters('the_content', $post->post_content);

        $post->post_excerpt_rendered = apply_filters('the_excerpt', $post->post_excerpt);

        $post->post_format = get_post_format() ? : 'standard';

        $post->author = $this->retrieveAuthorInformation($post, $wpdb);

        $post->latest_comment = get_comments(array('post_id' => $post->ID, 'number' => $latestComments, 'orderby' => 'comment_date', 'order' => 'DESC'));

        // li ricerco per date DESC ... così da prendere gli ultimi
        // ma poi li visualizzo in data ASC quelli trovati. per questo faccio il reverse
        $post->latest_comment = array_reverse($post->latest_comment );

        $post->categories = get_the_terms($post->ID, 'category');

        $post->lm_like_counter = $this->retrieveLikeCounter($post);

        $post->lm_saved_counter = $this->retrieveSavedCounter($post);

        $post->featured_image = get_the_post_thumbnail_url($post->ID);

        $post->liked = $likePostService->checkUserPostLike(get_current_user_id(), $post->ID);

        $post->saved = $savedPostService->checkUserPostLike(get_current_user_id(), $post->ID);

        // non usiamo il post parent di wordpress ... no buono per nostri scopi!
        $post->post_parent = get_post_meta('lm-shared-post') ? : 0;

        if ($post->post_parent !== 0) {
            $postParent = get_post($post->post_parent);
            $post->parentDetails = $postParent;
            $post->parentDetails->post_content_rendered = apply_filters('the_content', $postParent->post_content);
            $post->parentDetails->featured_image = get_the_post_thumbnail_url($postParent->ID);
        }

        return $post;
    }

    /**
     * @param \WP_Post $post
     * @param $wpdb
     * @return mixed
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

}