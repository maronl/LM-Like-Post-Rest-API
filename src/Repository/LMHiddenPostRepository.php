<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


interface LMHiddenPostRepository
{
    public function hidePost($userId, $postId);

    public function showPost($userId, $postId);

    public function isHidden($userId, $postId);

    public function getTableName();

    public function createDBStructure();
}