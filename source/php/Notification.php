<?php

namespace NotificationCenter;

class Notification
{
    protected $entityTypes;

    public function __construct()
    {
        $this->entityTypes = include(NOTIFICATIONCENTER_PATH . 'source/php/config/EntityTypes.php');

        add_action('wp_insert_comment', array($this, 'commentCreated'), 99, 2);
    }

    public function commentCreated($commentId, $commentObj)
    {
        // New post comment
        $notifier = get_post_field('post_author', $commentObj->comment_post_ID);
        $this->insertNotifications(0, $commentObj->comment_post_ID, array((int) $notifier), $commentObj->user_id);

        // New comment reply

        // New thread contribution
    }

    /**
     * Insert notifications to db
     * @param  int    $entityType Entity type
     * @param  int    $entityId   Entity ID, eg. Post ID
     * @param  array  $notifier   List of notifier IDs
     * @param  int    $sender     Sender ID
     * @return void
     */
    public function insertNotifications(int $entityType, int $entityId, $notifiers = array(), int $sender)
    {
        global $wpdb;

        // Remove sender from notifier array, return if notifiers is empty
        $notifiers = array_diff($notifiers, array($sender));
        if (!$notifiers) {
            return false;
        }

        // Insert the notification data in 'notification_objects' table
        $tableName = $wpdb->prefix . 'notification_objects';
        $wpdb->insert(
            $tableName,
            array(
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'created' => current_time('mysql')
            )
        );
        // Get the notification_object_id
        $notificationId = $wpdb->insert_id;

        // Insert notification recipients and sender in 'notification' table
        $values = array();
        $placeHolders = array();
        $tableName = $wpdb->prefix . 'notifications';
        $query = "INSERT INTO $tableName (notification_object_id, notifier_id, sender_id) VALUES ";
        // Loop through notifiers to insert multiple rows
        foreach ($notifiers as $notifier) {
            array_push($values, $notificationId, $notifier, $sender);
            $placeHolders[] = "(%d, %d, %d)";
        }
        $query .= implode(', ', $placeHolders);

        $wpdb->query($wpdb->prepare("$query ", $values));
    }
}
