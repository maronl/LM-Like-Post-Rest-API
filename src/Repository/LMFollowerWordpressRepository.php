<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:22
 */

namespace LM\WPPostLikeRestApi\Repository;


class LMFollowerWordpressRepository implements LMFollowerRepository
{
    private $table;
    /**
     * @var
     */
    private $version;

    private $tableNoPrefix;

    function __construct($tableName, $version)
    {
        global $wpdb;
        $this->table = $wpdb->prefix . $tableName;
        $this->tableNoPrefix = $tableName;
        $this->version = $version;
    }

    public function saveFollower($followerId, $followingId)
    {
        global $wpdb;

        $data = array(
            'follower_id' => $followerId,
            'following_id' => $followingId,
            'created_at' => date('Y-m-d H:i:s')
        );

        $format = array('%d', '%d', '%s');

        return $wpdb->replace($this->table, $data, $format);
    }

    public function deleteFollower($followerId, $followingId)
    {
        global $wpdb;

        $data = array(
            'follower_id' => $followerId,
            'following_id' => $followingId
        );

        $format = array('%d', '%d');

        return $wpdb->delete($this->table, $data, $format);
    }

    public function findFollowers($userId, $page = 1, $item_per_page = 20, $before = null)
    {
        global $wpdb;

        $offset = ($page - 1) * $item_per_page;
        $limit = $item_per_page;

        $sql = "SELECT u.ID, u.user_login, u.display_name, u.user_email, u.user_registered, u.user_status
            FROM " . $wpdb->prefix . "lm_followers AS f 
              INNER JOIN " . $wpdb->prefix . "users as u
                ON f.follower_id = u.ID AND f.following_id = %d ";

        if (!is_null($before)) {
            $sql .= " WHERE f.created_at < %s ";
        }

        $sql .= " LIMIT %d, %d;";

        if (!is_null($before)) {
            $sql = $wpdb->prepare($sql, $userId, $before, $offset, $limit);
        } else {
            $sql = $wpdb->prepare($sql, $userId, $offset, $limit);
        }

        $followers = $wpdb->get_results($sql);

        if (has_filter('lm-sf-rest-api-get-followers')) {
            $followers = apply_filters('lm-sf-rest-api-get-followers', $followers);
        }

        return $followers;
    }

    public function findFollowersCount($userId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT count(*)
            FROM " . $wpdb->prefix . "lm_followers AS f 
            WHERE f.following_id = %d;", $userId);

        $followers = $wpdb->get_var($sql);

        return $followers;
    }

    public function findFollowings($userId, $page = 1, $item_per_page = 20, $before = null)
    {
        global $wpdb;

        $offset = ($page - 1) * $item_per_page;
        $limit = $item_per_page;

        $sql = "SELECT u.ID, u.user_login, u.display_name, u.user_email, u.user_registered, u.user_status
            FROM " . $wpdb->prefix . "lm_followers AS f 
              INNER JOIN " . $wpdb->prefix . "users as u
                ON f.following_id = u.ID AND f.follower_id = %d ";

        if (!is_null($before)) {
            $sql .= " WHERE f.created_at < %s ";
        }

        $sql .= " LIMIT %d, %d;";

        if (!is_null($before)) {
            $sql = $wpdb->prepare($sql, $userId, $before, $offset, $limit);
        } else {
            $sql = $wpdb->prepare($sql, $userId, $offset, $limit);
        }

        $followings = $wpdb->get_results($sql);

        if (has_filter('lm-sf-rest-api-get-followings')) {
            $followings = apply_filters('lm-sf-rest-api-get-followings', $followings);
        }

        return $followings;
    }

    public function findFollowingsCount($userId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT count(*)
            FROM " . $wpdb->prefix . "lm_followers AS f 
            WHERE f.follower_id = %d;", $userId);

        $followers = $wpdb->get_var($sql);

        return $followers;
    }


    public function findFollowingsIds($userId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT f.following_id
            FROM " . $wpdb->prefix . "lm_followers AS f 
            WHERE f.follower_id = %d;", $userId);

        $res = $wpdb->get_results($sql, ARRAY_N);

        $resClean = [];
        foreach ($res as $singleRes) {
            $resClean[] = $singleRes[0];
        }

        return $resClean;
    }

    public function findFollower($followerId, $followingId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT COUNT(*) FROM $this->table WHERE follower_id = %d AND following_id = %d",
            $followerId, $followingId);

        return $wpdb->get_var($sql);
    }

    public function getTableName()
    {
        return $this->table;
    }

    public function createDBStructure()
    {
        global $wpdb;

        $tableName = $this->table;

        $charset_collate = '';

        if (!empty($wpdb->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if (!empty($wpdb->collate)) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
          follower_id BIGINT(11) NOT NULL,
          following_id BIGINT(11) NOT NULL,
          created_at DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
          PRIMARY KEY (follower_id, following_id),
          KEY `".$this->tableNoPrefix."_created_at` (`created_at`)
	    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);

        add_option($this->tableNoPrefix . '_db_version', $this->version);
    }

}