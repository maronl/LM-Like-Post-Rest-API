<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 12:14
 */

namespace LM\WPPostLikeRestApi\Utility;


trait LMWPFollowerChaceCounter
{
    // salvo un contatore anche come post meta da usare nel recuperare le liste di post
    // con l'informazione eventuale del numero di like associata
    private function incrementFollowerCounters($followerId, $followingId, $keyFollower, $keyFollowing, $followerRepo)
    {
        $followings = $followerRepo->findFollowingsCount($followerId);
        $followers = $followerRepo->findFollowersCount($followingId);
        update_user_meta($followerId, $keyFollowing, $followings);
        update_user_meta($followingId, $keyFollower, $followers);

        return true;
    }

    private function decrementFollowerCounters($followerId, $followingId, $keyFollower, $keyFollowing, $followerRepo)
    {
        $followings = $followerRepo->findFollowingsCount($followerId);
        $followers = $followerRepo->findFollowersCount($followingId);
        update_user_meta($followerId, $keyFollowing, $followings);
        update_user_meta($followingId, $keyFollower, $followers);

        return true;
    }
}