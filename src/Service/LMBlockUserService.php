<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 09:58
 */

namespace LM\WPPostLikeRestApi\Service;


interface LMBlockUserService
{
    public function blockUser($blockingId, $blockedId);

    public function unblockUser($blockingId, $blockedId);

    public function getBlockedUsers($userId);
}