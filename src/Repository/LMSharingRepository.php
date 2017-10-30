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
    public function saveSharing($sharedId, $sharingId);
    public function deleteSharing($sharedId, $sharingId);
    public function findSharedId($sharingId);
    public function findSharingsCount($sharedId);
    public function findSharingsIds($sharedId);
    public function findSharedsCount($sharingId);
    public function findSharedsIds($sharingId);
    public function getTableName();
    public function createDBStructure();
}