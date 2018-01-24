<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMAbuseReportService;
use LM\WPPostLikeRestApi\Utility\LMWPJWTFirebaseHeaderAuthorization;

class LMAbuseReportPublicManager
{
    /**
     * @var LMWPJWTFirebaseHeaderAuthorization
     */
    private $headerAuthorization;
    /**
     * @var LMAbuseReportService
     */
    private $abuseReportService;

    private $version;

    private $plugin_slug;

    private $namespace;

    /**
     * LMWPWallPublicManager constructor.
     * @param $plugin_slug
     * @param $version
     * @param LMAbuseReportService $abuseReportService
     * @param LMWPJWTFirebaseHeaderAuthorization $headerAuthorization
     */
    public function __construct(
        $plugin_slug,
        $version,
        LMAbuseReportService $abuseReportService,
        LMWPJWTFirebaseHeaderAuthorization $headerAuthorization

    ) {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->namespace = $this->plugin_slug . '/v' . $this->version;
        $this->headerAuthorization = $headerAuthorization;
        $this->abuseReportService = $abuseReportService;
    }

    /**
     * Add the endpoints to the API
     */
    public function add_api_routes()
    {
        register_rest_route($this->namespace, 'abuse', [
            'methods' => 'POST',
            'callback' => array($this, 'createAbuseReport'),
        ]);

    }

    public function createAbuseReport($request)
    {
        $validate = $this->abuseReportService->validateInsertRequest($request);

        if (is_wp_error($validate)) {
            return new \WP_REST_Response(array('status' => false, 'data' => $validate->errors), 422);
        }

        if (is_array($validate)) {
            return new \WP_REST_Response(array('status' => false, 'data' => $validate), 422);
        }

        $abuseReport = $this->abuseReportService->getAbuseReportObjectFromRequest($request);

        $alreadyReport = $this->abuseReportService->isUserAbuseReportAlreadyOpen($abuseReport);

        if ($alreadyReport) {
            return new \WP_REST_Response(array(
                'status' => false,
                'data' => $this->abuseReportService->getAbuseReport($alreadyReport)->toArray()
            ), 409);
        }


        $insertId = $this->abuseReportService->addAbuseReport($abuseReport);

        if ($insertId === false) {
            return new \WP_REST_Response(array(
                'status' => false,
                'msg' => 'Non è stato possibile inserire la segnalazion di Abuso'
            ), 500);
        }

        $abuseReport->setId($insertId);

        do_action('lm-sf-add-abuse-report', $abuseReport);

        return new \WP_REST_Response(array('status' => true, 'data' => $abuseReport->toArray()), 200);
    }

}