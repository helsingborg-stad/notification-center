<?php

namespace NotificationCenter;

global $notificationVersion;
$notificationVersion = '1.0';

class App
{
    public function __construct()
    {
        // Use 'updateDbCheck' method when db table is updated
        // add_action('plugins_loaded', array($this, 'updateDbCheck'));
        // add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        // add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    /**
     * Creates the notifications db table
     * @return void
     */
    public static function install()
    {
        global $wpdb;
        global $notificationVersion;

        $tableName      = $wpdb->prefix . 'notifications';
        $charsetCollate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $tableName (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name tinytext NOT NULL,
            text text NOT NULL,
            url varchar(55) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charsetCollate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option('notification_center_version', $notificationVersion);
    }

    /**
     * Check if db needs to be updated
     * @return void
     */
    public function updateDbCheck()
    {
        global $notificationVersion;
        if (version_compare(get_site_option('notification_center_version'), $notificationVersion) < 0) {
            $this->install();
        }
    }


    /**
     * Enqueue required style
     * @return void
     */
    public function enqueueStyles()
    {

    }

    /**
     * Enqueue required scripts
     * @return void
     */
    public function enqueueScripts()
    {

    }
}
