<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:04
 */

namespace LM\WPPostLikeRestApi\Service;


use LM\WPPostLikeRestApi\Utility\LMHeaderAuthorization;
use LM\WPPostLikeRestApi\Utility\LMWPPostWallDetails;

class LMProfileWordpressService implements LMProfileService
{

    use LMWPPostWallDetails;

    /**
     * @var LMHeaderAuthorization
     */
    private $headerAuthorization;
    /**
     * @var LMFollowerService
     */
    private $followerService;

    function __construct(LMHeaderAuthorization $headerAuthorization, LMFollowerService $followerService)
    {
        $this->headerAuthorization = $headerAuthorization;
        $this->followerService = $followerService;
    }

    public function getLoggedUserProfile()
    {
        $loggedUser = $this->headerAuthorization->getUser();

        $user = get_user_by('ID', $loggedUser);

        $res = array();
        $res['ID'] = $user->ID;
        $res['user_email'] = $user->user_email;
        $res['display_name'] = $user->display_name;
        $res['user_picture'] = 'http://0.gravatar.com/avatar/c06f9a7686481ac171d46f2ed0835ca6?s=154&d=mm&r=g';
        $res['user_registered'] = $user->user_registered;
        $res['profession'] = 'TO BE DONE';
        $res['location'] = 'TO BE DONE';
        $res['followers'] = $this->followerService->getFollowersCount($user->ID);
        $res['followings'] = $this->followerService->getFollowingsCount($user->ID);
        $res['points'] = 0;

        return $res;
    }

    public function getUserProfile($userId)
    {
        $user = get_user_by('ID', $userId);

        $res = array();
        $res['ID'] = $user->ID;
        $res['user_email'] = $user->user_email;
        $res['display_name'] = $user->display_name;
        $res['user_picture'] = 'http://0.gravatar.com/avatar/c06f9a7686481ac171d46f2ed0835ca6?s=154&d=mm&r=g';
        $res['user_registered'] = $user->user_registered;
        $res['profession'] = 'TO BE DONE';
        $res['location'] = 'TO BE DONE';
        $res['followers'] = $this->followerService->getFollowersCount($user->ID);
        $res['followings'] = $this->followerService->getFollowingsCount($user->ID);
        $res['points'] = 0;

        return $res;
    }

}
