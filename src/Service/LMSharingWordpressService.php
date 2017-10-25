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

    public function getSharingCount($sharedId)
    {
        return $this->repository->findShared($sharedId);
    }
}
