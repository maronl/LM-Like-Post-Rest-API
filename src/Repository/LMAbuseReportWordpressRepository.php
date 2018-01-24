<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


use Axenso\AXEGamification\Model\LMAbuseReport;

class LMAbuseReportWordpressRepository implements LMAbuseReportRepository
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

    public function add(LMAbuseReport $abuseReport)
    {
        global $wpdb;

        $data = array(
            'user_id' => $abuseReport->getUserId(),
            'reference_type' => $abuseReport->getReferenceType(),
            'reference_id' => $abuseReport->getReferenceId(),
            'issue' => $abuseReport->getIssue(),
            'operator_id' => $abuseReport->getOperatorId(),
            'closed_status' => $abuseReport->getClosedStatus(),
            'closed_at' => $abuseReport->getClosedAt(),
            'created_at' => $abuseReport->getCreatedAt(),
            'updated_at' => $abuseReport->getUpdatedAt()
        );

        $format = array('%d', '%s', '%d', '%s', '%d', '%d', '%s', '%s', '%s');

        $abuse = $wpdb->insert($this->table, $data, $format);

        if ($abuse !== false) {
            return $wpdb->insert_id;
        }

        return $abuse;
    }

    public function get($abuseReportId)
    {
        global $wpdb;

        $abuseReportDB = $wpdb->get_row("SELECT * FROM $this->table WHERE id = $abuseReportId", ARRAY_A);

        $abuseReport = new LMAbuseReport();
        if (empty($abuseReportDB)) {
            return false;
        } else {
            $abuseReport->setId($abuseReportDB['id']);
            $abuseReport->setUserId($abuseReportDB['user_id']);
            $abuseReport->setReferenceId($abuseReportDB['reference_id']);
            $abuseReport->setReferenceType($abuseReportDB['reference_type']);
            $abuseReport->setIssue($abuseReportDB['issue']);
            $abuseReport->setOperatorId($abuseReportDB['operator_id']);
            $abuseReport->setClosedStatus($abuseReportDB['closed_status']);
            $abuseReport->setClosedAt($abuseReportDB['closed_at']);
            $abuseReport->setCreatedAt($abuseReportDB['created_at']);
            $abuseReport->setUpdatedAt($abuseReportDB['updated_at']);
        }

        return $abuseReport;
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
          id BIGINT(11) NOT NULL,
          user_id BIGINT(11) NOT NULL,
          reference_type VARCHAR (10) NOT NULL,
          reference_id BIGINT(11) NOT NULL,
          issue VARCHAR (255) NOT NULL,
          operator_id BIGINT(11) NULL,
          closed_status TINYINT NULL,
          closed_at DATETIME NULL,
          created_at DATETIME NOT NULL,
          updated_at DATETIME NOT NULL,
          PRIMARY KEY (id),
          INDEX `" . $this->tableNoPrefix . "_created_at` (`created_at`),
          INDEX `" . $this->tableNoPrefix . "_user_id` (`user_id`),
          INDEX `" . $this->tableNoPrefix . "_reference_type` (`reference_type`),
          INDEX `" . $this->tableNoPrefix . "_reference_id` (`reference_id`),
          INDEX `" . $this->tableNoPrefix . "_operator_id` (`operator_id`)
	    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);

        add_option($this->tableNoPrefix . '_db_version', $this->version);
    }
}