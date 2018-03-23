<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:22
 */

namespace LM\WPPostLikeRestApi\Repository;


class LMBlockUserWordpressRepository implements LMBlockUserRepository
{
    private $table;

    private $version;

    private $tableNoPrefix;

    function __construct($tableName, $version)
    {
        global $wpdb;
        $this->table = $wpdb->prefix . $tableName;
        $this->tableNoPrefix = $tableName;
        $this->version = $version;
    }

    public function blockUser($blockingId, $blockedId)
    {
        global $wpdb;

        $data = array(
            'user_id' => $blockingId,
            'blocked_user_id' => $blockedId,
            'created_at' => date('Y-m-d H:i:s')
        );

        $format = array('%d', '%d', '%s');

        return $wpdb->replace($this->table, $data, $format);
    }

    public function unblockUser($blockingId, $blockedId)
    {
        global $wpdb;

        $data = array(
            'user_id' => $blockingId,
            'blocked_user_id' => $blockedId
        );

        $format = array('%d', '%d');

        return $wpdb->delete($this->table, $data, $format);
    }

    public function isBlocked($blockingId, $blockedId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT COUNT(*) FROM $this->table WHERE user_id = %d AND blocked_user_id = %d",
            $blockingId,
            $blockedId);

        return $wpdb->get_var($sql);
    }


    /**
     * by default this function return the list of blocked and blocking users for a specific user id
     * if you need only the list of blocked users use the function getBlockedUsersList()
     * @param $userId
     * @return array
     */
    public function getBlockedUsers($userId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT blocked_user_id FROM $this->table WHERE user_id = %d", $userId);
        $blocked = $wpdb->get_results($sql, ARRAY_A);

        $sql = $wpdb->prepare("SELECT user_id FROM $this->table WHERE blocked_user_id = %d", $userId);
        $blocking = $wpdb->get_results($sql, ARRAY_A);

        $blocked = array_column($blocked, 'blocked_user_id');
        $blocking = array_column($blocking, 'user_id');

        return array_merge($blocked, $blocking);
    }

    public function getBlockedUsersList($userId, $page, $item_per_page, $before = null)
    {
        global $wpdb;

        $offset = ($page - 1) * $item_per_page;
        $limit = $item_per_page;

        $sql = "SELECT u.ID, u.user_login, u.display_name, u.user_email, u.user_registered, u.user_status
            FROM $this->table AS b 
              INNER JOIN " . $wpdb->prefix . "users as u
                ON b.blocked_user_id = u.ID AND b.user_id = %d ";

        if (!is_null($before)) {
            $sql .= " WHERE f.created_at < %s ";
        }

        $sql .= " LIMIT %d, %d;";

        if (!is_null($before)) {
            $sql = $wpdb->prepare($sql, $userId, $before, $offset, $limit);
        } else {
            $sql = $wpdb->prepare($sql, $userId, $offset, $limit);
        }

        $blocked = $wpdb->get_results($sql);

        if (has_filter('lm-sf-rest-api-get-blocked-users')) {
            $blocked = apply_filters('lm-sf-rest-api-get-blocked-users', $blocked);
        }

        return $blocked;

    }

    public function getBlockedUsersCount($userId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT count(*)
            FROM $this->table AS b 
            WHERE b.user_id = %d;", $userId);

        $followers = $wpdb->get_var($sql);

        return $followers;

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
          user_id BIGINT(11) NOT NULL,
          blocked_user_id BIGINT(11) NOT NULL,
          created_at DATETIME NOT NULL,
          PRIMARY KEY (user_id, post_id),
          KEY `" . $this->tableNoPrefix . "_created_at` (`created_at`)
	    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);

        add_option($this->tableNoPrefix . '_db_version', $this->version);
    }

}