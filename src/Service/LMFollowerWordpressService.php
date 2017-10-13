<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:04
 */

namespace LM\WPPostLikeRestApi\Service;


use LM\WPPostLikeRestApi\Manager\LMWPFollowerChaceCounter;
use LM\WPPostLikeRestApi\Repository\LMFollowerRepository;

class LMFollowerWordpressService implements LMFollowerService
{

    use LMWPFollowerChaceCounter;

    private $table;
    /**
     * @var LMFollowerRepository
     */
    private $repository;

    private $cacheFollowerCounter;

    private $cacheFollowingCounter;

    function __construct(LMFollowerRepository $repository)
    {
        $this->table = $repository->getTableName();
        $this->repository = $repository;
        $this->cacheFollowerCounter = 'lm-follower-counter';
        $this->cacheFollowingCounter = 'lm-following-counter';
    }

    public function addFollower($followerId, $followingId)
    {

        if($this->repository->findFollower($followerId, $followingId)) {
            return true;
        }

        if($this->repository->saveFollower($followerId, $followingId)) {
            $this->incrementFollowerCounters($followerId, $followingId, $this->cacheFollowerCounter, $this->cacheFollowingCounter);
            return true;
        }

        return false;
    }

    public function removeFollower($followerId, $followingId)
    {
        if($this->repository->deleteFollower($followerId, $followingId)) {
            $this->decrementFollowerCounters($followerId, $followingId, $this->cacheFollowerCounter, $this->cacheFollowingCounter);
            return true;
        }
        return false;
    }

    public function getFollowersCount($followingId)
    {
        $count = get_post_meta($followingId, $this->cacheFollowingCounter, true);

        if(!is_numeric($count)) {
            return 0;
        }

        return $count;
    }

    public function getFollowingsCount($followerId)
    {
        $count = get_post_meta($followerId, $this->cacheFollowerCounter, true);

        if(!is_numeric($count)) {
            return 0;
        }

        return $count;
    }
    
    public function getFollowers($followingId)
    {
        // TODO: Implement getFollowers() method.
    }

    public function getFollowings($followerId)
    {
        // TODO: Implement getFollowings() method.
    }
}
