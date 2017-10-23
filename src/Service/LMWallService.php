<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 09:58
 */

namespace LM\WPPostLikeRestApi\Service;


interface LMWallService
{
    public function getWall(Array $params);
    public function getPost($postId);
}