<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


interface LMSharingRepository
{
    public function saveSharing($followerId, $followingId);
    public function deleteSharing($followerId, $followingId);
    public function findSharings($userId);
    public function findSharingsIds($userId);
    public function findShared($userId);
    public function findSharedIds($userId);
    public function getTableName();
    public function createDBStructure();
}