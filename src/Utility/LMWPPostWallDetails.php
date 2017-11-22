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

    /**
     * @param \WP_Post $post
     * @param int $latestComments
     * @param $likePostService
     * @param $savedPostService
     * @param $sharingPostService
     * @return \WP_Post
     */
    private function retrievePostInformation(
        \WP_Post $post,
        $latestComments = 3,
        $likePostService,
        $savedPostService,
        $sharingPostService
    ) {
        global $wpdb;

        $post->post_content_rendered = apply_filters('the_content', $post->post_content);

        $post->post_excerpt_rendered = apply_filters('the_excerpt', $post->post_excerpt);

        $post->post_format = get_post_format() ?: 'standard';

        $post->author = $this->retrieveAuthorPostInformation($post, $wpdb);

        $post = $this->retrieveLatestComments($post, $latestComments);

        // li ricerco per date DESC ... così da prendere gli ultimi
        // ma poi li visualizzo in data ASC quelli trovati. per questo faccio il reverse
        $post->latest_comment = array_reverse($post->latest_comment);

        $post->categories = get_the_terms($post->ID, 'category');

        $post->wall_categories = get_the_terms($post->ID, 'lm_wall_category');

        $post->lm_like_counter = $this->retrieveLikeCounter($post);

        $post->lm_saved_counter = $this->retrieveSavedCounter($post);

        $post->lm_shared_counter = $sharingPostService->getSharedCount($post->ID);

        $post->featured_image = get_the_post_thumbnail_url($post->ID);

        $post->liked = $likePostService->checkUserPostLike(get_current_user_id(), $post->ID);

        $post->saved = $savedPostService->checkUserPostLike(get_current_user_id(), $post->ID);

        $post->shared_post = $sharingPostService->findSharedPost($post->ID);

        if ($post->shared_post !== 0) {
            $postShared = get_post($post->shared_post);
            if (!empty($postShared)) {
                $post->sharedPostDetails = $postShared;
                $post->sharedPostDetails->post_content_rendered = apply_filters('the_content',
                    $postShared->post_content);
                $post->sharedPostDetails->featured_image = get_the_post_thumbnail_url($postShared->ID);
            }
        }

        if (has_filter('lm-sf-rest-api-wall-details')) {
            $post = apply_filters('lm-sf-rest-api-wall-details', $post);
        }

        return $post;
    }

    /**
     * @param \WP_Post $post
     * @param $wpdb
     * @return mixed
     */
    private function retrieveAuthorPostInformation(\WP_Post $post, $wpdb)
    {
        return $this->retrieveAuthorInformation($post->post_author, $wpdb);
    }

    /**
     * @param \WP_Comment $comment
     * @param $wpdb
     * @return mixed
     */
    private function retrieveAuthorCommentInformation(\WP_Comment $comment, $wpdb)
    {
        return $this->retrieveAuthorInformation($comment->user_id, $wpdb);
    }

    /**
     * @param $authorId
     * @param $wpdb
     * @return mixed
     */
    private function retrieveAuthorInformation($authorId, $wpdb)
    {
        $sql = $wpdb->prepare("SELECT u.ID, u.user_login, u.display_name, u.user_email, u.user_registered, u.user_status
            FROM pld_users as u
            WHERE u.ID = %d;", $authorId);

        $userInfo = $wpdb->get_row($sql);
        if (!empty($userInfo)) {
            if (has_filter('lm-sf-rest-api-get-wall-author-info')) {
                $userInfo = apply_filters('lm-sf-rest-api-get-wall-author-info', $userInfo);
            }
        }

        return $userInfo;
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

    /**
     * @param \WP_Post $post
     * @param $latestComments
     * @return \WP_Post
     */
    private function retrieveLatestComments(\WP_Post $post, $latestComments)
    {
        global $wpdb;

        $post->latest_comment = get_comments(array(
            'post_id' => $post->ID,
            'number' => $latestComments,
            'orderby' => 'comment_date',
            'order' => 'DESC'
        ));

        foreach ($post->latest_comment as $comment) {
            $comment->author = $this->retrieveAuthorCommentInformation($comment, $wpdb);
        }

        return $post;

    }

}