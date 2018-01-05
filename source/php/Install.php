<?php

namespace NotificationCenter;

global $notificationVersion;
$notificationVersion = '1.0';

class Install
{
    public function __construct()
    {
        // Use 'updateDbCheck' method when db table needs to be updated
        // add_action('plugins_loaded', array($this, 'updateDbCheck'));
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

        /**
         * Notification object
         * This table contain details about the notification entity and entity type.
        */
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}notification_objects (
          ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          entity_type bigint(20) UNSIGNED NOT NULL,
          entity_id bigint(20) UNSIGNED NOT NULL,
          created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          status tinyint(1) DEFAULT 0 NOT NULL,
          PRIMARY KEY (ID)
        ) $charsetCollate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        /**
         * Notification
         * This table holds the information regarding the notifiers, the users to whom the notification has to be sent.
        */
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}notifications (
          ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          notification_object_id bigint(20) UNSIGNED NOT NULL,
          notifier_id bigint(20) UNSIGNED NOT NULL,
          sender_id bigint(20) UNSIGNED,
          status tinyint(1) DEFAULT 0 NOT NULL,
          PRIMARY KEY (ID),
          INDEX fk_notification_object_idx (notification_object_id ASC),
          INDEX fk_notification_notifier_id_idx (notifier_id ASC),
          INDEX fk_notification_sender_id_idx (sender_id ASC),
          CONSTRAINT fk_notification_object
            FOREIGN KEY (notification_object_id)
            REFERENCES {$wpdb->prefix}notification_objects (ID)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT fk_notification_notifier_id
            FOREIGN KEY (notifier_id)
            REFERENCES {$wpdb->prefix}users (ID)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT fk_notification_sender_id
            FOREIGN KEY (sender_id)
            REFERENCES {$wpdb->prefix}users (ID)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION
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
            $this->createTables();
        }
    }
}
