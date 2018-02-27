<?php

namespace NotificationCenter;

global $notificationVersion;
$notificationVersion = '1.2';

class Install
{
    public function __construct()
    {
        // Use 'updateDbCheck' method when db table needs to be updated
        //add_action('plugins_loaded', array($this, 'updateDbCheck'));
    }

    /**
     * Creates the notifications db tables
     * @return void
     */
    public static function createTables()
    {
        global $wpdb;
        global $notificationVersion;

        $charsetCollate = $wpdb->get_charset_collate();
        $basePrefix = $wpdb->base_prefix;

        /**
         * Notification object
         * This table contain details about the notification entity and entity type.
        */
        $sql = "CREATE TABLE {$basePrefix}notification_objects (
          ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          sender_id bigint(20) UNSIGNED,
          entity_type bigint(20) UNSIGNED NOT NULL,
          entity_id bigint(20) UNSIGNED NOT NULL,
          post_id bigint(20) UNSIGNED,
          blog_id smallint(5) DEFAULT 1 NOT NULL,
          created datetime NOT NULL,
          PRIMARY KEY  (ID),
          KEY (sender_id ASC),
          FOREIGN KEY (sender_id) REFERENCES {$basePrefix}users (ID) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) $charsetCollate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        /**
         * Notification
         * This table holds the information regarding the notifiers, the users to whom the notification has to be sent.
        */
        $sql = "CREATE TABLE {$basePrefix}notifications (
          ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          notification_object_id bigint(20) UNSIGNED NOT NULL,
          notifier_id bigint(20) UNSIGNED NOT NULL,
          status tinyint(1) DEFAULT 0 NOT NULL,
          PRIMARY KEY  (ID),
          KEY (notification_object_id ASC),
          KEY (notifier_id ASC),
          FOREIGN KEY (notification_object_id) REFERENCES {$basePrefix}notification_objects (ID) ON DELETE CASCADE ON UPDATE NO ACTION,
          FOREIGN KEY (notifier_id) REFERENCES {$basePrefix}users (ID) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) $charsetCollate;";
        dbDelta($sql);

        update_site_option('notification_center_version', $notificationVersion);
    }

    /**
     * Check if db needs to be updated
     * @return void
     */
    public function updateDbCheck()
    {
        global $notificationVersion;
        if (version_compare(get_site_option('notification_center_version'), $notificationVersion) < 0) {
            $this->createTables();
        }
    }
}
