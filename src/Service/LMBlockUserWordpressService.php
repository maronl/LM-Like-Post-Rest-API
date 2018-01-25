<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:04
 */

namespace LM\WPPostLikeRestApi\Service;


use LM\WPPostLikeRestApi\Repository\LMBlockUserRepository;

class LMBlockUserWordpressService implements LMBlockUserService
{
    private $table;
    /**
     * @var LMBlockUserRepository
     */
    private $repository;

    /**
     * LMBlockUserWordpressService constructor.
     * @param LMBlockUserRepository $repository
     */
    function __construct(LMBlockUserRepository $repository)
    {
        $this->table = $repository->getTableName();
        $this->repository = $repository;
    }

    public function blockUser($blockingId, $blockedId)
    {
        if ($this->repository->isBlocked($blockingId, $blockedId)) {
            return true;
        }

        if ($this->repository->blockUser($blockingId, $blockedId)) {
            return true;
        }

        return false;
    }

    public function unblockUser($blockingId, $blockedId)
    {
        if ($this->repository->unblockUser($blockingId, $blockedId)) {
            return true;
        }
        return false;
    }

    public function getBlockedUsers($userId)
    {
        return $this->repository->getBlockedUsers($userId);
    }

}
