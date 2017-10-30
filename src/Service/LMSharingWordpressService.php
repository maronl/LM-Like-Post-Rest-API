<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:04
 */

namespace LM\WPPostLikeRestApi\Service;


use LM\WPPostLikeRestApi\Repository\LMSharingRepository;

class LMSharingWordpressService implements LMSharingService
{

    function __construct(LMSharingRepository $repository)
    {
        $this->table = $repository->getTableName();
        $this->repository = $repository;
    }

    public function findSharedPost($sharingId)
    {
        $shared = $this->repository->findSharedId($sharingId);
        return (!empty($shared)) ? $shared : 0;
    }

    public function getSharedCount($sharedId)
    {
        return $this->repository->findSharingsCount($sharedId);
    }

    public function saveSharing($sharedId, $sharingId)
    {
        return $this->repository->saveSharing($sharedId, $sharingId);
    }

    public function deleteSharing($sharedId, $sharingId)
    {
        return $this->repository->deleteSharing($sharedId, $sharingId);
    }
}
