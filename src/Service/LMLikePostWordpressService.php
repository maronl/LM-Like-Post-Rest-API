<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:04
 */

namespace LM\WPPostLikeRestApi\Service;


use LM\WPPostLikeRestApi\Utility\LMWPPostChaceCounter;
use LM\WPPostLikeRestApi\Repository\LMLikePostRepository;

class LMLikePostWordpressService implements LMLikePostService
{

    use LMWPPostChaceCounter;

    private $table;
    /**
     * @var LMLikePostRepository
     */
    private $repository;

    private $cacheCounter;

    function __construct(LMLikePostRepository $repository)
    {
        $this->table = $repository->getTableName();
        $this->repository = $repository;
        $this->cacheCounter = 'lm-like-counter';
    }

    public function addLike($userId, $postId)
    {
        if ($this->repository->findLike($userId, $postId)) {
            return true;
        }

        if ($this->repository->saveLike($userId, $postId)) {
            $this->incrementPostLikeCounter($postId, $this->cacheCounter);
            return true;
        }

        return false;
    }

    public function removeLike($userId, $postId)
    {
        if ($this->repository->deleteLike($userId, $postId)) {
            $this->decrementPostLikeCounter($postId, $this->cacheCounter);
            return true;
        }
        return false;
    }

    public function getPostLikeCount($postId)
    {
        $count = get_post_meta($postId, $this->cacheCounter, true);

        if (!is_numeric($count)) {
            return 0;
        }

        return $count;
    }

    public function checkUserPostLike($userId, $postId)
    {
        if ($this->repository->findLike($userId, $postId)) {
            return true;
        }
        return false;
    }

}
