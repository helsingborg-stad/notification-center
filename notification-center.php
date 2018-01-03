<?php

/**
 * Plugin Name:       Notification Center
 * Plugin URI:        (#plugin_url#)
 * Description:       Plugin to give logged in users notifications when others react on comments etc.
 * Version:           1.0.0
 * Author:            Jonatan Hanson
 * Author URI:        (#plugin_author_url#)
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       notification-center
 * Domain Path:       /languages
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

define('NOTIFICATIONCENTER_PATH', plugin_dir_path(__FILE__));
define('NOTIFICATIONCENTER_URL', plugins_url('', __FILE__));
define('NOTIFICATIONCENTER_TEMPLATE_PATH', NOTIFICATIONCENTER_PATH . 'templates/');

load_plugin_textdomain('notification-center', false, plugin_basename(dirname(__FILE__)) . '/languages');

require_once NOTIFICATIONCENTER_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once NOTIFICATIONCENTER_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new NotificationCenter\Vendor\Psr4ClassLoader();
$loader->addPrefix('NotificationCenter', NOTIFICATIONCENTER_PATH);
$loader->addPrefix('NotificationCenter', NOTIFICATIONCENTER_PATH . 'source/php/');
$loader->register();

// Start application
new NotificationCenter\App();
