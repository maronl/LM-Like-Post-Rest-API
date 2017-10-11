<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Service\LMLikePostService;

class LMWPSavedPostAdminManager
{

    /**
     * @var LMLikePostService
     */
    private $savedPostService;
    private $version;

    function __construct(LMLikePostService $savedPostService, $version)
    {
        $this->savedPostService = $savedPostService;
        $this->version = $version;
    }

    function columnHeader($defaults)
    {
        $defaults['lm-sf-saved'] = '<span class="dashicons dashicons-heart" title="User saving this post"><span class="screen-reader-text">Saved</span></span>';
        return $defaults;
    }

    function columnContent($column_name, $post_ID)
    {
        if ($column_name == 'lm-sf-saved') {
            echo $this->savedPostService->getPostLikeCount($post_ID);
        }
    }

    function customCssFile()
    {
        wp_register_style( 'lm-sf-rest-api-styles',  plugin_dir_url( dirname( dirname(__FILE__))) . 'assets/'.$this->version.'/lm-sf-rest-api.css' );
        wp_enqueue_style( 'lm-sf-rest-api-styles' );
    }

}