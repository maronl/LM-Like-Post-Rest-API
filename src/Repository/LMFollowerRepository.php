<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


interface LMFollowerRepository
{
    public function saveFollower($followerId, $followingId);
    public function deleteFollower($followerId, $followingId);
    public function findFollowers($userId, $page, $item_per_page);
    public function findFollowings($userId, $page, $item_per_page);
    public function findFollower($followerId, $followingId);
    public function getTableName();
    public function createDBStructure();
}