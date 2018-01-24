<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 09:58
 */

namespace LM\WPPostLikeRestApi\Service;


use Axenso\AXEGamification\Model\LMAbuseReport;
use LM\WPPostLikeRestApi\Repository\LMAbuseReportRepository;
use LM\WPPostLikeRestApi\Request\LMAbuseReportInsertRequest;

class LMAbuseReportWordpressService implements LMAbuseReportService
{
    /**
     * @var LMAbuseReportRepository
     */
    private $abuseReportRepository;
    /**
     * @var LMAbuseReportInsertRequest
     */
    private $insertRequestValidator;

    /**
     * LMAbuseReportWordpressService constructor.
     * @param LMAbuseReportRepository $abuseReportRepository
     * @param LMAbuseReportInsertRequest $insertRequest
     */
    public function __construct(
        LMAbuseReportRepository $abuseReportRepository,
        LMAbuseReportInsertRequest $insertRequest
    ) {

        $this->abuseReportRepository = $abuseReportRepository;
        $this->insertRequestValidator = $insertRequest;
    }

    public function addAbuseReport(LMAbuseReport $abuseReport)
    {

//        $validate = $this->insertRequestValidator->validateRequest($request);
//
//        if ($validate !== true) {
//            return $validate;
//        }

//        $abuseReport = $this->getAbuseReportObjectFromRequest($request);
//
//        $alreadyReport = $this->isUserAbuseReportAlreadyOpen($abuseReport);
//
//        if($alreadyReport) {
//            $abuseReport->setId($alreadyReport);
//            return $abuseReport;
//        }
//
//
//        if ($this->abuseReportRepository->add($abuseReport) !== false) {
//            return $abuseReport;
//        }
//
//        return false;

        return $this->abuseReportRepository->add($abuseReport);
    }

    public function getAbuseReport($abuseReportid)
    {
        return $this->abuseReportRepository->get($abuseReportid);
    }

    public function isUserAbuseReportAlreadyOpen(LMAbuseReport $abuseReport)
    {
        global $wpdb;

        $tableName =$this->abuseReportRepository->getTableName();

        $sql = $wpdb->prepare("SELECT id FROM $tableName 
        WHERE user_id = %d 
        AND reference_type = %s
        AND reference_id = %d
        AND closed_at IS NULL
        LIMIT 1", $abuseReport->getUserId(), $abuseReport->getReferenceType(),
            $abuseReport->getReferenceId());

        return $wpdb->get_var($sql);
    }

    public function validateInsertRequest($request)
    {
        return $this->insertRequestValidator->validateRequest($request);
    }

    public function getAbuseReportObjectFromRequest($request)
    {
        $data = $this->insertRequestValidator->getDataFromRequest($request);
        $abuseReport = new LMAbuseReport();
        $abuseReport->setUserId($data['user_id']);
        $abuseReport->setReferenceType($data['reference_type']);
        $abuseReport->setReferenceId($data['reference_id']);
        $abuseReport->setIssue($data['issue']);
        $abuseReport->setOperatorId(null);
        $abuseReport->setClosedStatus(null);
        $abuseReport->setClosedAt(null);
        $abuseReport->setCreatedAt(date('Y-m-d H:i:s'));
        $abuseReport->setUpdatedAt(date('Y-m-d H:i:s'));
        return $abuseReport;
    }


}