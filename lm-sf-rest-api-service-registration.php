<?php

use Interop\Container\ContainerInterface;
use LM\WPPostLikeRestApi\Manager\LMWPFollowerPublicManager;
use LM\WPPostLikeRestApi\Manager\LMWPJWTFirebaseHeaderAuthorization;
use LM\WPPostLikeRestApi\Manager\LMWPLikePostAdminManager;
use LM\WPPostLikeRestApi\Manager\LMWPLikePostPublicManager;
use LM\WPPostLikeRestApi\Manager\LMWPProfilePublicManager;
use LM\WPPostLikeRestApi\Manager\LMWPSavedPostAdminManager;
use LM\WPPostLikeRestApi\Manager\LMWPSavedPostPublicManager;
use LM\WPPostLikeRestApi\Manager\LMWPSharingAdminManager;
use LM\WPPostLikeRestApi\Manager\LMWPWallPublicManager;
use LM\WPPostLikeRestApi\Model\LMWallPostModel;
use LM\WPPostLikeRestApi\Repository\LMFollowerWordpressRepository;
use LM\WPPostLikeRestApi\Repository\LMLikePostWordpressRepository;
use LM\WPPostLikeRestApi\Repository\LMSharingWordpressRepository;
use LM\WPPostLikeRestApi\Repository\LMWallPostWordpressRepository;
use LM\WPPostLikeRestApi\Request\LMWallPostInsertRequest;
use LM\WPPostLikeRestApi\Service\LMFollowerWordpressService;
use LM\WPPostLikeRestApi\Service\LMLikePostWordpressService;
use LM\WPPostLikeRestApi\Service\LMProfileWordpressService;
use LM\WPPostLikeRestApi\Service\LMSavedPostWordpressService;
use LM\WPPostLikeRestApi\Service\LMSharingWordpressService;
use LM\WPPostLikeRestApi\Service\LMWallWordpressService;

$builder = new \DI\ContainerBuilder();
//$builder->setDefinitionCache(new Doctrine\Common\Cache\ApcCache());
//$builder->writeProxiesToFile(true, 'tmp/proxies');

$builder->addDefinitions([

    // parameters
    'post-like-table' => 'lm_post_like',
    'post-saved-table' => 'lm_post_saved',
    'post-shared-table' => 'lm_post_shared',
    'follower-table' => 'lm_followers',
    'plugin-slug' => 'lm-sf-rest-api',
    'plugin-version' => '1.0.0',
    'jwt-secret' => (defined('JWT_AUTH_SECRET_KEY')) ? JWT_AUTH_SECRET_KEY : '',

    // request
    'LMWallPostInsertRequest' => function () {
        return new LMWallPostInsertRequest();
    },

    // model
    'LMWallPostModel' => function () {
        return new LMWallPostModel();
    },

    // repositories
    'LMLikePostWordpressRepository' => function (ContainerInterface $c) {
        return new LMLikePostWordpressRepository('lm_post_like', '1.0.0');
    },
    'LMSavedPostWordpressRepository' => function (ContainerInterface $c) {
        return new LMLikePostWordpressRepository($c->get('post-saved-table'), $c->get('plugin-version'));
    },
    'LMSharingWordpressRepository' => function (ContainerInterface $c) {
        return new LMSharingWordpressRepository($c->get('post-shared-table'), $c->get('plugin-version'));
    },
    'LMFollowerWordpressRepository' => function (ContainerInterface $c) {
        return new LMFollowerWordpressRepository($c->get('follower-table'), $c->get('plugin-version'));
    },
    'LMWallPostWordpressRepository' => function (ContainerInterface $c) {
        $insertRequest = $c->get('LMWallPostInsertRequest');
        return new LMWallPostWordpressRepository($insertRequest);
    },

    // services
    'LMLikePostWordpressService' => function (ContainerInterface $c) {
        $repo = $c->get('LMLikePostWordpressRepository');
        return new LMLikePostWordpressService($repo);
    },
    'LMSavedPostWordpressService' => function (ContainerInterface $c) {
        $repo = $c->get('LMLikePostWordpressRepository');
        return new LMSavedPostWordpressService($repo);
    },
    'LMSharingWordpressService' => function (ContainerInterface $c) {
        $repo = $c->get('LMSharingWordpressRepository');
        return new LMSharingWordpressService($repo);
    },
    'LMFollowerWordpressService' => function (ContainerInterface $c) {
        $repo = $c->get('LMFollowerWordpressRepository');
        return new LMFollowerWordpressService($repo);
    },
    'LMWallWordpressService' => function (ContainerInterface $c) {
        $header = $c->get('LMWPJWTFirebaseHeaderAuthorization');
        $wallRepo = $c->get('LMWallPostWordpressRepository');
        $followerService = $c->get('LMFollowerWordpressService');
        $likePostService = $c->get('LMLikePostWordpressService');
        $savedPostService = $c->get('LMSavedPostWordpressService');
        $sharingService = $c->get('LMSharingWordpressService');
        $insertRequest = $c->get('LMWallPostInsertRequest');
        return new LMWallWordpressService($header, $wallRepo, $followerService, $likePostService, $savedPostService,
            $insertRequest, $sharingService);
    },
    'LMProfileWordpressService' => function (ContainerInterface $c) {
        $header = $c->get('LMWPJWTFirebaseHeaderAuthorization');
        $followerService = $c->get('LMFollowerWordpressService');
        $likePostService = $c->get('LMLikePostWordpressService');
        $savedPostService = $c->get('LMSavedPostWordpressService');
        return new LMProfileWordpressService($header, $followerService, $likePostService, $savedPostService);
    },


    // managers
    'LMWPLikePostAdminManager' => function (ContainerInterface $c) {
        $service = $c->get('LMLikePostWordpressService');
        return new LMWPLikePostAdminManager($service, $c->get('plugin-version'));
    },
    'LMWPSavedPostAdminManager' => function (ContainerInterface $c) {
        $service = $c->get('LMSavedPostWordpressService');
        return new LMWPSavedPostAdminManager($service, $c->get('plugin-version'));
    },
    'LMWPSharingAdminManager' => function (ContainerInterface $c) {
        $service = $c->get('LMSharingWordpressService');
        return new LMWPSharingAdminManager($service, $c->get('plugin-version'));
    },

    'LMWPLikePostPublicManager' => function (ContainerInterface $c) {
        $service = $c->get('LMLikePostWordpressService');
        return new LMWPLikePostPublicManager($c->get('plugin-slug'), $c->get('plugin-version'), $service);
    },
    'LMWPSavedPostPublicManager' => function (ContainerInterface $c) {
        $service = $c->get('LMSavedPostWordpressService');
        return new LMWPSavedPostPublicManager($c->get('plugin-slug'), $c->get('plugin-version'), $service);
    },
    'LMWPFollowerPublicManager' => function (ContainerInterface $c) {
        $service = $c->get('LMFollowerWordpressService');
        return new LMWPFollowerPublicManager($c->get('plugin-slug'), $c->get('plugin-version'), $service);
    },
    'LMWPWallPublicManager' => function (ContainerInterface $c) {
        $service = $c->get('LMWallWordpressService');
        return new LMWPWallPublicManager($c->get('plugin-slug'), $c->get('plugin-version'), $service);
    },
    'LMWPProfilePublicManager' => function (ContainerInterface $c) {
        $service = $c->get('LMProfileWordpressService');
        return new LMWPProfilePublicManager($c->get('plugin-slug'), $c->get('plugin-version'), $service);
    },

    // utility
    'LMWPJWTFirebaseHeaderAuthorization' => function (ContainerInterface $c) {
        return new LMWPJWTFirebaseHeaderAuthorization($c->get('jwt-secret'));
    },

]);

global $containerLmSfRestAPI;
$containerLmSfRestAPI = $builder->build();

