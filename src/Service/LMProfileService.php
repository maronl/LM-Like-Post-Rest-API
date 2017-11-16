<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 09:58
 */

namespace LM\WPPostLikeRestApi\Service;


interface LMProfileService
{
    public function getLoggedUserProfile();

    public function getUserProfile($userId);
}