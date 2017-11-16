<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 09:58
 */

namespace LM\WPPostLikeRestApi\Service;


interface LMSharingService
{
    public function saveSharing($sharedId, $sharingId);

    public function deleteSharing($sharedId, $sharingId);

    public function findSharedPost($sharingId);

    public function getSharedCount($sharedId);
}