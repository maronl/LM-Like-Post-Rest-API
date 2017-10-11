<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 12:14
 */

namespace LM\WPPostLikeRestApi\Manager;


trait LMWPPostChaceCounter
{
    // salvo un contatore anche come post meta da usare nel recuperare le liste di post
    // con l'informazione eventuale del numero di like associata
    private function incrementPostLikeCounter($postId, $key) {
        $count = get_post_meta($postId, $key, true);
        if(!is_numeric($count)) {
            return update_post_meta($postId, $key, 1);
        }
        return update_post_meta($postId, $key, ($count+1));
    }

    private function decrementPostLikeCounter($postId, $key) {
        $count = get_post_meta($postId, $key, true);
        if(!is_numeric($count) || $count <= 1) {
            return update_post_meta($postId, $key, 0);
        }
        return update_post_meta($postId, $key, ($count-1));
    }
}