<?php

use Interop\Container\ContainerInterface;
use LM\WPPostLikeRestApi\Manager\LMAbuseReportPublicManager;
use LM\WPPostLikeRestApi\Manager\LMWPFollowerPublicManager;
use LM\WPPostLikeRestApi\Manager\LMWPHiddenPostPublicManager;
use LM\WPPostLikeRestApi\Repository\LMAbuseReportWordpressRepository;
use LM\WPPostLikeRestApi\Repository\LMHiddenPostWordpressRepository;
use LM\WPPostLikeRestApi\Repository\LMWallPostsMovieWordpressRepository;
use LM\WPPostLikeRestApi\Request\LMAbuseReportInsertRequest;
use LM\WPPostLikeRestApi\Request\LMWallPostsMovieUpdateRequest;
use LM\WPPostLikeRestApi\Service\LMAbuseReportWordpressService;
use LM\WPPostLikeRestApi\Service\LMHiddenPostWordpressService;
use LM\WPPostLikeRestApi\Utility\LMWPJWTFirebaseHeaderAuthorization;
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
use LM\WPPostLikeRestApi\Repository\LMWallPostsPictureWordpressRepository;
use LM\WPPostLikeRestApi\Repository\LMWallPostWordpressRepository;
use LM\WPPostLikeRestApi\Request\LMWallPostInsertRequest;
use LM\WPPostLikeRestApi\Request\LMWallPostsPictureUpdateRequest;
use LM\WPPostLikeRestApi\Request\LMWallPostUpdateRequest;
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
    'post-picture-folder' => 'image',
    'post-movie-folder' => 'movie',
    'post-like-table' => 'lm_post_like',
    'post-saved-table' => 'lm_post_saved',
    'post-shared-table' => 'lm_post_shared',
    'post-hidden-table' => 'lm_post_hidden',
    'abuse-report-table' => 'lm_abuse_reports',
    'follower-table' => 'lm_followers',
    'plugin-slug' => 'lm-sf-rest-api',
    'plugin-version' => '1.0.0',
    'jwt-secret' => (defined('JWT_AUTH_SECRET_KEY')) ? JWT_AUTH_SECRET_KEY : '',

    // request
    'LMWallPostInsertRequest' => function () {
        return new LMWallPostInsertRequest();
    },
    'LMWallPostUpdateRequest' => function () {
        return new LMWallPostUpdateRequest();
    },
    'LMWallPostsPictureUpdateRequest' => function (ContainerInterface $c) {
        return new LMWallPostsPictureUpdateRequest();
    },
    'LMWallPostsMovieUpdateRequest' => function (ContainerInterface $c) {
        return new LMWallPostsMovieUpdateRequest();
    },
    'LMAbuseReportInsertRequest' => function () {
        return new LMAbuseReportInsertRequest();
    },

    // model
    'LMWallPostModel' => function () {
        return new LMWallPostModel();
    },

    // repositories
    'LMLikePostWordpressRepository' => function (ContainerInterface $c) {
        return new LMLikePostWordpressRepository($c->get('post-like-table'),  $c->get('plugin-version'));
    },
    'LMSavedPostWordpressRepository' => function (ContainerInterface $c) {
        return new LMLikePostWordpressRepository($c->get('post-saved-table'), $c->get('plugin-version'));
    },
    'LMSharingWordpressRepository' => function (ContainerInterface $c) {
        return new LMSharingWordpressRepository($c->get('post-shared-table'), $c->get('plugin-version'));
    },
    'LMHiddenPostWordpressRepository' => function (ContainerInterface $c) {
        return new LMHiddenPostWordpressRepository($c->get('post-hidden-table'), $c->get('plugin-version'));
    },
    'LMFollowerWordpressRepository' => function (ContainerInterface $c) {
        return new LMFollowerWordpressRepository($c->get('follower-table'), $c->get('plugin-version'));
    },
    'LMWallPostsPictureWordpressRepository' => function (ContainerInterface $c) {
        $updateRequest = $c->get('LMWallPostsPictureUpdateRequest');

        return new LMWallPostsPictureWordpressRepository($updateRequest, $c->get('post-picture-folder'));
    },
    'LMWallPostsMovieWordpressRepository' => function (ContainerInterface $c) {
        $updateRequest = $c->get('LMWallPostsMovieUpdateRequest');

        return new LMWallPostsMovieWordpressRepository($updateRequest, $c->get('post-movie-folder'));
    },
    'LMAbuseReportWordpressRepository' => function (ContainerInterface $c) {
        return new LMAbuseReportWordpressRepository($c->get('abuse-report-table'), $c->get('plugin-version'));
    },

    'LMWallPostWordpressRepository' => function (ContainerInterface $c) {
        $insertRequest = $c->get('LMWallPostInsertRequest');
        $updateRequest = $c->get('LMWallPostUpdateRequest');
        $header = $c->get('LMWPJWTFirebaseHeaderAuthorization');
        $pictureRepository = $c->get('LMWallPostsPictureWordpressRepository');
        $movieRepository = $c->get('LMWallPostsMovieWordpressRepository');

        return new LMWallPostWordpressRepository($header, $insertRequest, $updateRequest, $pictureRepository, $movieRepository);
    },


    // services
    'LMLikePostWordpressService' => function (ContainerInterface $c) {
        $repo = $c->get('LMLikePostWordpressRepository');
        return new LMLikePostWordpressService($repo);
    },
    'LMSavedPostWordpressService' => function (ContainerInterface $c) {
        $repo = $c->get('LMSavedPostWordpressRepository');
        return new LMSavedPostWordpressService($repo);
    },
    'LMSharingWordpressService' => function (ContainerInterface $c) {
        $repo = $c->get('LMSharingWordpressRepository');
        return new LMSharingWordpressService($repo);
    },
    'LMHiddenPostWordpressService' => function (ContainerInterface $c) {
        $repo = $c->get('LMHiddenPostWordpressRepository');
        return new LMHiddenPostWordpressService($repo);
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
    'LMAbuseReportWordpressService' => function (ContainerInterface $c) {
        $repo = $c->get('LMAbuseReportWordpressRepository');
        $validator = $c->get('LMAbuseReportInsertRequest');
        return new LMAbuseReportWordpressService($repo, $validator);
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
    'LMWPHiddenPostPublicManager' => function (ContainerInterface $c) {
        $service = $c->get('LMHiddenPostWordpressService');
        return new LMWPHiddenPostPublicManager($c->get('plugin-slug'), $c->get('plugin-version'), $service);
    },

    'LMWPFollowerPublicManager' => function (ContainerInterface $c) {
        $service = $c->get('LMFollowerWordpressService');
        return new LMWPFollowerPublicManager($c->get('plugin-slug'), $c->get('plugin-version'), $service);
    },
    'LMWPWallPublicManager' => function (ContainerInterface $c) {
        $wallService = $c->get('LMWallWordpressService');
        $sharingService = $c->get('LMSharingWordpressService');
        $header = $c->get('LMWPJWTFirebaseHeaderAuthorization');

        return new LMWPWallPublicManager($c->get('plugin-slug'), $c->get('plugin-version'), $wallService, $sharingService, $header);
    },
    'LMWPProfilePublicManager' => function (ContainerInterface $c) {
        $service = $c->get('LMProfileWordpressService');

        return new LMWPProfilePublicManager($c->get('plugin-slug'), $c->get('plugin-version'), $service);
    },
    'LMAbuseReportPublicManager' => function (ContainerInterface $c) {
        $service = $c->get('LMAbuseReportWordpressService');
        $header = $c->get('LMWPJWTFirebaseHeaderAuthorization');

        return new LMAbuseReportPublicManager($c->get('plugin-slug'), $c->get('plugin-version'), $service, $header);
    },

    // utility
    'LMWPJWTFirebaseHeaderAuthorization' => function (ContainerInterface $c) {
        return new LMWPJWTFirebaseHeaderAuthorization($c->get('jwt-secret'));
    },

]);

global $containerLmSfRestAPI;
$containerLmSfRestAPI = $builder->build();

