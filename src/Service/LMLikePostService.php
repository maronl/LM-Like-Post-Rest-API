<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 09:58
 */

namespace LM\WPPostLikeRestApi\Service;


interface LMLikePostService
{
    public function addLike($userId, $postId);

    public function removeLike($userId, $postId);

    public function getPostLikeCount($postId);

    public function checkUserPostLike($userId, $postId);

    public function getUsersLikePost($postId);

    public function getPostIdsLikeByUser($userId);
}