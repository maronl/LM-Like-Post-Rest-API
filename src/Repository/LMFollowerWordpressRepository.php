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

    public function findFollowers($userId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT u.ID, u.user_login, u.display_name, u.user_email, u.user_registered, u.user_status
            FROM pld_lm_followers AS f 
              INNER JOIN pld_users as u
                ON f.follower_id = u.ID AND f.following_id = %d;", $userId);

        return $wpdb->get_results($sql);
    }

    public function findFollowings($userId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT u.ID, u.user_login, u.display_name, u.user_email, u.user_registered, u.user_status
            FROM pld_lm_followers AS f 
              INNER JOIN pld_users as u
                ON f.following_id = u.ID AND f.follower_id = %d;", $userId);

        return $wpdb->get_results($sql);    }

    public function getTableName()
    {
        return $this->table;
    }

    public function createDBStructure()
    {
        global $wpdb;

        $tableName = $this->table;

        $charset_collate = '';

        if ( ! empty( $wpdb->charset ) ) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if ( ! empty( $wpdb->collate ) ) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
          follower_id BIGINT(11) NOT NULL,
          following_id BIGINT(11) NOT NULL,
          created_at DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
          PRIMARY KEY (user_id, post_id)
	    ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta( $sql );

        add_option( $this->tableNoPrefix . '_db_version', $this->version );
    }

    public function findFollower($followerId, $followingId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT COUNT(*) FROM $this->table WHERE follower_id = %d AND following_id = %d", $followerId, $followingId);

        return $wpdb->get_var($sql);
    }
}