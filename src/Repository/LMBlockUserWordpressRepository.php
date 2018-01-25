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

        $sql = $wpdb->prepare("SELECT COUNT(*) FROM $this->table WHERE user_id = %d AND blocked_user_id = %d", $blockingId,
            $blockedId);

        return $wpdb->get_var($sql);
    }

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
          KEY `".$this->tableNoPrefix."_created_at` (`created_at`)
	    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);

        add_option($this->tableNoPrefix . '_db_version', $this->version);
    }

}