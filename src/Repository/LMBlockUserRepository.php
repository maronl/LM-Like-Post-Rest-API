<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


interface LMBlockUserRepository
{
    public function blockUser($blockingId, $blockedId);

    public function unblockUser($blockingId, $blockedId);

    public function isBlocked($blockingId, $blockedId);

    public function getTableName();

    public function createDBStructure();
}