<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


use Axenso\AXEGamification\Model\LMAbuseReport;

interface LMAbuseReportRepository
{
    public function add(LMAbuseReport $abuseReport);

    public function get($abuseReportId);

    public function getTableName();

    public function createDBStructure();
}