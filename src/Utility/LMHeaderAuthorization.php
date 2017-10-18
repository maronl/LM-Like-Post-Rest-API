<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 18/10/17
 * Time: 16:38
 */

namespace LM\WPPostLikeRestApi\Utility;


interface LMHeaderAuthorization
{
    public function getToken();
    public function getUser();
}