<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMLikePostService;
use LM\WPPostLikeRestApi\Service\LMSharingWordpressService;

class LMWPSharingAdminManager
{
    /**
     * @var LMLikePostService
     */
    private $sharingService;
    private $version;

    function __construct(LMSharingWordpressService $sharingService, $version)
    {
        $this->sharingService = $sharingService;
        $this->version = $version;
    }

    function columnHeader($defaults) {
        $defaults['lm-sf-sharing'] = '<span class="dashicons dashicons-share" title="Number of sharing posts"><span class="screen-reader-text">Sharing</span></span>';
        return $defaults;
    }

    function columnContent($column_name, $post_ID) {
        if ($column_name == 'lm-sf-sharing') {
            echo $this->sharingService->getSharedCount($post_ID);
        }
    }

    function customCssFile()
    {
        wp_register_style( 'lm-sf-rest-api-styles',  plugin_dir_url( dirname( dirname(__FILE__))) . 'assets/'.$this->version.'/lm-sf-rest-api.css' );
        wp_enqueue_style( 'lm-sf-rest-api-styles' );
    }

}
