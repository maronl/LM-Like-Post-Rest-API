<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:04
 */

namespace LM\WPPostLikeRestApi\Service;


use LM\WPPostLikeRestApi\Repository\LMHiddenPostRepository;

class LMHiddenPostWordpressService implements LMHiddenPostService
{
    private $table;
    /**
     * @var LMHiddenPostRepository
     */
    private $repository;

    /**
     * LMHiddenPostWordpressService constructor.
     * @param LMHiddenPostRepository $repository
     */
    function __construct(LMHiddenPostRepository $repository)
    {
        $this->table = $repository->getTableName();
        $this->repository = $repository;
    }

    public function hidePost($userId, $postId)
    {
        if ($this->repository->isHidden($userId, $postId)) {
            return true;
        }

        if ($this->repository->hidePost($userId, $postId)) {
            return true;
        }

        return false;
    }

    public function showPost($userId, $postId)
    {
        if ($this->repository->showPost($userId, $postId)) {
            return true;
        }
        return false;
    }

}
