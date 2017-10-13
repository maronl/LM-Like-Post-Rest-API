<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 12:14
 */

namespace LM\WPPostLikeRestApi\Manager;


trait LMWPFollowerChaceCounter
{
    // salvo un contatore anche come post meta da usare nel recuperare le liste di post
    // con l'informazione eventuale del numero di like associata
    private function incrementFollowerCounters($followerId, $followingId, $keyFollower, $keyFollowing) {
        $count = get_user_meta($followerId, $keyFollowing, true);
        if(!is_numeric($count)) {
            update_user_meta($followerId, $keyFollowing, 1);
        }
        update_user_meta($followerId, $keyFollowing, ($count+1));

        $count = get_user_meta($followingId, $keyFollower, true);
        if(!is_numeric($count)) {
            update_user_meta($followingId, $keyFollower, 1);
        }
        update_user_meta($followingId, $keyFollower, ($count+1));

        return true;
    }

    private function decrementFollowerCounters($followerId, $followingId, $keyFollower, $keyFollowing) {
        $count = get_user_meta($followerId, $keyFollowing, true);
        if(!is_numeric($count) || $count <= 1) {
            update_user_meta($followerId, $keyFollowing, 0);
        }
        update_user_meta($followerId, $keyFollowing, ($count-1));

        $count = get_user_meta($followingId, $keyFollower, true);
        if(!is_numeric($count) || $count <= 1) {
            update_user_meta($followingId, $keyFollower, 0);
        }

        update_user_meta($followingId, $keyFollower, ($count-1));

        return true;
    }
}