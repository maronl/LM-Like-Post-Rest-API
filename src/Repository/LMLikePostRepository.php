<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


interface LMLikePostRepository
{
    public function saveLike($userId, $postId);
    public function deleteLike($userId, $postId);
    public function findLike($userId, $postId);
    public function getTableName();
    public function createDBStructure();
}