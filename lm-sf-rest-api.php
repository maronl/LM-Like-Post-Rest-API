<?php

use LM\WPPostLikeRestApi\Manager\LMWPPluginManager;
use LM\WPPostLikeRestApi\Manager\LMWPPluginLoader;

/**
 * The file responsible for starting the LM Social Function Rest API plugin
 *
 * plugin to enable post like e dislike throught rest-api
 * *
 * @wordpress-plugin
 * Plugin Name: LM Social Functions Rest API
 * Plugin URI: http://maronl.it
 * Description: Wordpress plugin to enable basic social function throught rest-api: like e dislike. save post as favourite, follower and following. just rest-api no front-end :). backoffice is improved showing like and saved counter in the post list. if you want to protect your api think about a JWT layer or modify access to rest-api only for logged users.
 * Version: 1.0.0
 * Author: Luca Maroni
 * Author URI: http://maronl.it
 * Text Domain: lm-sf-rest-api
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, then abort execution.
if (!defined('WPINC')) {
    die;
}

/**
 * Include the core class responsible for loading all necessary components of the plugin.
 */
require_once plugin_dir_path(__FILE__) . 'src/autoloader.php';

/**
 * Instantiates the LM Social Function Rest API Manager class and then
 * calls its run method officially starting up the plugin.
 */
function run_LMWPPluginManager()
{
    $pluginSlug = 'lm-sf-rest-api';
    $pluginVersion = '1.0.0';
    $pluginOptions = array('jwt-secret' => (defined('JWT_AUTH_SECRET_KEY')) ? JWT_AUTH_SECRET_KEY : '');

    $loader = new LMWPPluginLoader();
    $manager = new LMWPPluginManager($loader, $pluginSlug, $pluginVersion, $pluginOptions);
    $manager->run();

}

// Call the above function to begin execution of the plugin.
run_LMWPPluginManager();
