<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 24/10/17
 * Time: 18:02
 */

namespace LM\WPPostLikeRestApi\Repository;


use LM\WPPostLikeRestApi\Utility\LMWPPostWallDetails;

class LMSharingWordpressRepository implements LMSharingRepository
{

    use LMWPPostWallDetails;

    private $table;

    private $version;

    private $tableNoPrefix;

//    private $likePostService;
//
//    private $savedPostService;

    function __construct($tableName, $version)
    {
        global $wpdb;
        $this->table = $wpdb->prefix . $tableName;
        $this->tableNoPrefix = $tableName;
        $this->version = $version;
    }

    /*function __construct($tableName, $version,  LMLikePostService $likePostService, LMLikePostService $savedPostService)
    {
        global $wpdb;
        $this->table = $wpdb->prefix . $tableName;
        $this->tableNoPrefix = $tableName;
        $this->version = $version;
        $this->likePostService = $likePostService;
        $this->savedPostService = $savedPostService;
    }*/

    public function saveSharing($sharedId, $sharingId)
    {
        global $wpdb;

        $data = array(
            'shared_post_id' => $sharedId,
            'sharing_post_id' => $sharingId,
            'created_at' => date('Y-m-d H:i:s')
        );

        $format = array('%d', '%d', '%s');

        return $wpdb->replace($this->table, $data, $format);
    }

    public function deleteSharing($sharedId, $sharingId)
    {
        global $wpdb;

        $data = array(
            'shared_post_id' => $sharedId,
            'sharing_post_id' => $sharingId
        );

        $format = array('%d', '%d');

        return $wpdb->delete($this->table, $data, $format);
    }


    /**
     * Return the posts that are sharing another post
     * @param $userId
     * @param int $page
     * @param int $item_per_page
     * @return mixed
     */
    public function findSharingsCount($sharedId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT count(sharing_post_id) FROM $this->table WHERE shared_post_id = %d", $sharedId);

        return $wpdb->get_var($sql);
    }

    /*public function findSharingsDetails($sharedId, $page = 1, $item_per_page = 20)
    {
        global $wpdb;

        $offset = ($page - 1) * $item_per_page;
        $limit = $item_per_page;

        $sql = $wpdb->prepare("SELECT id FROM $this->table WHERE shared_id = %d ORDER BY created_at DESC LIMIT %d, %d", $sharedId, $offset, $limit);

        $postIds = array_column($wpdb->get_results($sql), 0);

        $posts = get_posts(array('include' => $postIds));

        return $this->retrievePostInformation($posts, null, $this->likePostService, $this->savedPostService);

    }*/

    public function findSharingsIds($sharedId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT sharing_post_id FROM $this->table WHERE shared_post_id = %d ORDER BY created_at DESC", $sharedId);

        return array_column($wpdb->get_results($sql), 0);
    }

    public function findSharedsCount($sharingId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT count(shared_post_id) FROM $this->table WHERE sharing_post_id = %d", $sharingId);

        return $wpdb->get_var($sql);
    }

    /*public function findSharedDetails($sharingId, $page = 1, $item_per_page = 20)
    {
        global $wpdb;

        $offset = ($page - 1) * $item_per_page;
        $limit = $item_per_page;

        $sql = $wpdb->prepare("SELECT id FROM $this->table WHERE shared_id = %d ORDER BY created_at DESC LIMIT %d, %d", $sharingId, $offset, $limit);

        $postIds = array_column($wpdb->get_results($sql), 0);

        $posts = get_posts(array('include' => $postIds));

        return $this->retrievePostInformation($posts, null, $this->likePostService, $this->savedPostService);
    }*/

    public function findSharedsIds($sharingId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT shared_post_id FROM $this->table WHERE sharing_post_id = %d ORDER BY created_at DESC", $sharingId);

        return array_column($wpdb->get_results($sql), 0);
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

        if ( ! empty( $wpdb->charset ) ) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if ( ! empty( $wpdb->collate ) ) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
          shared_post_id BIGINT(11) NOT NULL,
          sharing_post_id BIGINT(11) NOT NULL,
          created_at DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
          PRIMARY KEY (shared_post_id, sharing_post_id)
	    ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta( $sql );

        add_option( $this->tableNoPrefix . '_db_version', $this->version );    }

    public function findSharedId($sharingId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT shared_post_id FROM $this->table WHERE sharing_post_id = %d", $sharingId);

        return $wpdb->get_var($sql);
    }

}