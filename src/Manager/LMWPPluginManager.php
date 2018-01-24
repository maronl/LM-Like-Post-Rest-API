<?php

namespace LM\WPPostLikeRestApi\Manager;

/**
 * The Manager is the core plugin responsible for including and
 * instantiating all of the code that composes the plugin.
 *
 * The Manager includes an instance to the Loader which is
 * responsible for coordinating the hooks that exist within the plugin.
 *
 * It also maintains a reference to the plugin slug which can be used in
 * internationalization, and a reference to the current version of the plugin
 * so that we can easily update the version in a single place to provide
 * cache busting functionality when including scripts and styles.
 *
 * @since 1.0.0
 */
class LMWPPluginManager
{

    /**
     * A reference to the loader class that coordinates the hooks and callbacks
     * throughout the plugin.
     *
     * @access protected
     * @var PLUGIN_CLASS_NAME_BASE_Loader $loader Manages hooks between the WordPress hooks and the callback functions.
     */
    protected $loader;

    /**
     * Represents the slug of the plugin that can be used throughout the plugin
     * for internationalization and other purposes.
     *
     * @access protected
     * @var string $plugin_slug The single, hyphenated string used to identify this plugin.
     */
    protected $plugin_slug;

    /**
     * Maintains the current version of the plugin so that we can use it throughout
     * the plugin.
     *
     * @access protected
     * @var string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Store the options set for the plugin (if there are) to be used as context in the admin e public side.
     *
     * @access protected
     * @var string $options The current options set for the plugin.
     */
    protected $options;

    /**
     * Instantiates the plugin by setting up the core properties and loading
     * all necessary dependencies and defining the hooks.
     *
     * The constructor will define both the plugin slug and the verison
     * attributes, but will also use internal functions to import all the
     * plugin dependencies, and will leverage the Single_Post_Meta_Loader for
     * registering the hooks and the callback functions used throughout the
     * plugin.
     * @param LMWPPluginLoader $loader
     * @param $pluginSlug
     * @param $pluginVersion
     * @param $pluginOptions
     */
    public function __construct(LMWPPluginLoader $loader, $pluginSlug, $pluginVersion, $pluginOptions)
    {

        $this->plugin_slug = $pluginSlug;
        $this->version = $pluginVersion;
        $this->options = $pluginOptions;
        $this->loader = $loader;

        $this->define_register_activation_hook();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Defines the hooks and callback functions that are used for setting up the plugin stylesheets, scripts, logic
     * and the plugin's meta box.
     *
     * @access private
     */
    private function define_admin_hooks()
    {
        global $containerLmSfRestAPI;

        $likeAdmin = $containerLmSfRestAPI->get('LMWPLikePostAdminManager');
        $savedAdmin = $containerLmSfRestAPI->get('LMWPSavedPostAdminManager');
        $sharingAdmin = $containerLmSfRestAPI->get('LMWPSharingAdminManager');

        $this->loader->add_filter('manage_lm_wall_posts_columns', $likeAdmin, 'columnHeader');
        $this->loader->add_filter('manage_lm_wall_posts_columns', $savedAdmin, 'columnHeader');
        $this->loader->add_filter('manage_lm_wall_posts_columns', $sharingAdmin, 'columnHeader');
        $this->loader->add_action('manage_lm_wall_posts_custom_column', $likeAdmin, 'columnContent', 10, 2);
        $this->loader->add_action('manage_lm_wall_posts_custom_column', $savedAdmin, 'columnContent', 10, 2);
        $this->loader->add_action('manage_lm_wall_posts_custom_column', $sharingAdmin, 'columnContent', 10, 2);
        $this->loader->add_action('admin_enqueue_scripts', $savedAdmin, 'customCssFile');

        $wallPostModel = $containerLmSfRestAPI->get('LMWallPostModel');
        $this->loader->add_action('init', $wallPostModel, 'defineCustomPostWall', 0);
        $this->loader->add_action('init', $wallPostModel, 'defineCustomPostWallTaxonomy', 0);

    }

    /**
     * Defines the hooks and callback functions that are used for rendering information on the front
     * end of the site.
     *
     * @access private
     */
    private function define_public_hooks()
    {
        global $containerLmSfRestAPI;

        $likePublic = $containerLmSfRestAPI->get('LMWPLikePostPublicManager');
        $savedPublic = $containerLmSfRestAPI->get('LMWPSavedPostPublicManager');
        $hiddenPublic = $containerLmSfRestAPI->get('LMWPHiddenPostPublicManager');
        $followerPublic = $containerLmSfRestAPI->get('LMWPFollowerPublicManager');
        $wallPublic = $containerLmSfRestAPI->get('LMWPWallPublicManager');
        $abusePublic = $containerLmSfRestAPI->get('LMAbuseReportPublicManager');
        $profilePublic = $containerLmSfRestAPI->get('LMWPProfilePublicManager');

        $this->loader->add_action('rest_api_init', $likePublic, 'add_api_routes');
        $this->loader->add_action('rest_api_init', $savedPublic, 'add_api_routes');
        $this->loader->add_action('rest_api_init', $hiddenPublic, 'add_api_routes');
        $this->loader->add_action('rest_api_init', $followerPublic, 'add_api_routes');
        $this->loader->add_action('rest_api_init', $wallPublic, 'add_api_routes');
        $this->loader->add_action('rest_api_init', $abusePublic, 'add_api_routes');
        $this->loader->add_action('rest_api_init', $profilePublic, 'add_api_routes');

        $this->loader->add_action('wp_insert_post', $wallPublic, 'incrementCountSharedPost', 10, 3);
    }

    /**
     * Defines the hooks and callback functions that are used during plugin activation
     *
     * @access private
     */
    private function define_register_activation_hook()
    {
        global $containerLmSfRestAPI;

        $likePostRepository = $containerLmSfRestAPI->get('LMLikePostWordpressRepository');
        $savedPostRepository = $containerLmSfRestAPI->get('LMSavedPostWordpressRepository');
        $hiddenPostRepository = $containerLmSfRestAPI->get('LMHiddenPostWordpressRepository');
        $followerRepository = $containerLmSfRestAPI->get('LMFollowerWordpressRepository');
        $sharingRepository = $containerLmSfRestAPI->get('LMSharingWordpressRepository');
        $abuseRepository = $containerLmSfRestAPI->get('LMAbuseReportWordpressRepository');
        $wallPostModel = $containerLmSfRestAPI->get('LMWallPostModel');

        register_activation_hook(dirname(dirname(dirname(__FILE__))) . '/lm-sf-rest-api.php',
            array($likePostRepository, 'createDBStructure'));
        register_activation_hook(dirname(dirname(dirname(__FILE__))) . '/lm-sf-rest-api.php',
            array($savedPostRepository, 'createDBStructure'));
        register_activation_hook(dirname(dirname(dirname(__FILE__))) . '/lm-sf-rest-api.php',
            array($hiddenPostRepository, 'createDBStructure'));
        register_activation_hook(dirname(dirname(dirname(__FILE__))) . '/lm-sf-rest-api.php',
            array($followerRepository, 'createDBStructure'));
        register_activation_hook(dirname(dirname(dirname(__FILE__))) . '/lm-sf-rest-api.php',
            array($sharingRepository, 'createDBStructure'));
        register_activation_hook(dirname(dirname(dirname(__FILE__))) . '/lm-sf-rest-api.php',
            array($abuseRepository, 'createDBStructure'));
        register_activation_hook(dirname(dirname(dirname(__FILE__))) . '/lm-sf-rest-api.php',
            array($wallPostModel, 'setCustomPostWallCapabilities'));
    }

    /**
     * Sets this class into motion.
     *
     * Executes the plugin by calling the run method of the loader class which will
     * register all of the hooks and callback functions used throughout the plugin
     * with WordPress.
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * Returns the current version of the plugin to the caller.
     *
     * @return string $this->version The current version of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}