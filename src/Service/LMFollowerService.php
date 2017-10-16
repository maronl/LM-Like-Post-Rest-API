<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 09:58
 */

namespace LM\WPPostLikeRestApi\Service;


interface LMFollowerService
{
    public function addFollower($followerId, $followingId);
    public function removeFollower($followerId, $followingId);
    public function getFollowers($followingId, $page, $item_per_page);
    public function getFollowings($followerId, $page, $item_per_page);
    public function getFollowersCount($followingId);
    public function getFollowingsCount($followerId);
}