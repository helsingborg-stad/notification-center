<?php

namespace NotificationCenter;

class Notification
{
    public function __construct()
    {
        $this->init();
    }

    /**
     * Insert notifications to DB
     * @param  int      $entityType Entity type, eg. 'post' or 'comment'
     * @param  int      $entityId   Entity ID, eg. Post or Comment ID
     * @param  array    $notifier   List of notifier IDs
     * @param  int      $sender     Sender ID
     * @return void
     */
    public function insertNotifications(int $entityType, int $entityId, $notifiers = array(), int $sender)
    {
        global $wpdb;

        // Remove duplicates and sender from notifier array, return if notifiers is empty
        $notifiers = array_diff(array_unique($notifiers), array($sender));
        if (!$notifiers) {
            return false;
        }

        // Insert the notification data in 'notification_objects' table
        $tableName = $wpdb->prefix . 'notification_objects';
        $wpdb->insert(
            $tableName,
            array(
                'sender_id' => $sender != 0 ? $sender : null,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'created' => current_time('mysql')
            )
        );
        // Get the notification_object_id
        $notificationId = $wpdb->insert_id;

        // Insert notification recipients in 'notification' table
        $values = array();
        $placeHolders = array();
        $tableName = $wpdb->prefix . 'notifications';
        $query = "INSERT INTO $tableName (notification_object_id, notifier_id) VALUES ";
        // Loop through all notifiers to insert them as multiple rows
        foreach ($notifiers as $notifier) {
            array_push($values, $notificationId, $notifier);
            $placeHolders[] = "(%d, %d)";
        }
        $query .= implode(', ', $placeHolders);
        $wpdb->query($wpdb->prepare("$query ", $values));
    }
}
