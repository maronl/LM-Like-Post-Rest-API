<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


interface LMWallPostsPictureRepository
{
    public function setUserId($userId);

    public function setPostId($postId);

    public function updatePicture($request);

    public function deletePicture();

    public function getPicturePath();

    public function getPictureURL();

    public function initUploadDirectory();
}