<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:04
 */

namespace LM\WPPostLikeRestApi\Service;


use LM\WPPostLikeRestApi\Repository\LMSharingRepository;

class LMSharingWordpressService implements LMSharingService
{

    function __construct(LMSharingRepository $repository)
    {
        $this->table = $repository->getTableName();
        $this->repository = $repository;
    }

    public function findSharedPost($sharingId)
    {
        $shared = $this->repository->findSharedId($sharingId);
        return (!empty($shared)) ? $shared : 0;
    }

    public function getSharedCount($sharedId)
    {
        return $this->repository->findSharingsCount($sharedId);
    }

    public function saveSharing($sharedId, $sharingId)
    {
        return $this->repository->saveSharing($sharedId, $sharingId);
    }

    public function deleteSharing($sharedId, $sharingId)
    {
        return $this->repository->deleteSharing($sharedId, $sharingId);
    }

    public function getUsersSharedPost($postId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT distinct u.ID, u.user_login, u.display_name, u.user_email, u.user_registered, u.user_status
            FROM " . $this->repository->getTableName() . " AS l 
              INNER JOIN " . $wpdb->prefix . "posts as p
                ON p.ID = l.sharing_post_id and l.shared_post_id = %d
              INNER JOIN " . $wpdb->prefix . "users as u
                ON p.post_author = u.ID;", $postId);

        //no shared join posts join users
        $users = $wpdb->get_results($sql);

        if (has_filter('lm-sf-rest-api-get-user-like-post')) {
            $users = apply_filters('lm-sf-rest-api-get-user-like-post', $users);
        }

        return $users;
    }
}
