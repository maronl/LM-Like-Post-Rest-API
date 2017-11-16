<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 18/10/17
 * Time: 16:42
 */

namespace LM\WPPostLikeRestApi\Manager;


use \Firebase\JWT\JWT;
use LM\WPPostLikeRestApi\Utility\LMHeaderAuthorization;

class LMWPJWTFirebaseHeaderAuthorization implements LMHeaderAuthorization
{

    /**
     * @var
     */
    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function getToken()
    {
        /*
         * Looking for the HTTP_AUTHORIZATION header
         */
        $auth = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : false;


        /* Double check for different auth header string (server dependent) */
        if (!$auth) {
            $auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : false;
        }

        if (!$auth) {
            return false;
        }

        /*
         * The HTTP_AUTHORIZATION is present verify the format
         * if the format is wrong return the user.
         */
        list($token) = sscanf($auth, 'Bearer %s');

        if (!$token) {
            return false;
        }

        return $token;
    }

    public function getUser()
    {
        $token = $this->getToken();

        if ($token === false) {
            return false;
        }

        $token = JWT::decode($token, $this->secret, array('HS256'));

        if ($token->iss != get_bloginfo('url')) {
            return false;
        }

        if (!isset($token->data->user->id)) {
            return false;
        }

        return $token->data->user->id;
    }
}