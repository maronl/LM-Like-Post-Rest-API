<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 09:58
 */

namespace LM\WPPostLikeRestApi\Service;


use Axenso\AXEGamification\Model\LMAbuseReport;

interface LMAbuseReportService
{
    public function addAbuseReport(LMAbuseReport $abuseReport);

    public function isUserAbuseReportAlreadyOpen(LMAbuseReport $abuseReport);

    public function validateInsertRequest($request);

    public function getAbuseReportObjectFromRequest($request);

}