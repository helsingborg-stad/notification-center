<?php

namespace NotificationCenter;

class Notification
{
    public $entityTypes = array();

    public function __construct()
    {
        $this->entityTypes = \NotificationCenter\Helper\EntityTypes::getEntityTypes();

        // Cron event
        add_action('send_notification_email', array($this, 'emailNotifiers'), 10, 1);

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

        // Schedule cron event to notify by email
        wp_schedule_single_event(time() + 10, 'send_notification_email', array($notificationId));

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

    /**
     * Send email to all notifiers
     * @param  int $notificationId Notification ID
     * @return void
     */
    public function emailNotifiers($notificationId)
    {
        global $wpdb;

        // Get notificaiton data
        $notificationData = $wpdb->get_row("
            SELECT no.ID, no.entity_type, no.entity_id, no.created, no.sender_id
            FROM {$wpdb->prefix}notification_objects no
            INNER JOIN {$wpdb->prefix}notifications n
                ON no.ID = n.notification_object_id
            WHERE no.ID = {$notificationId}
            GROUP BY no.ID"
        );

        // Build the email message
        $text = \NotificationCenter\Helper\Message::buildMessage($notificationData->entity_type, $notificationData->entity_id, $notificationData->sender_id);
        $url = \NotificationCenter\Helper\Message::notificationUrl($notificationData->entity_type, $notificationData->entity_id);
        $message = sprintf('%s <br><a href="%s">%s %s</a> <br><br>---<br> %s %s',
            $text,
            $url,
            __('Show', 'notification-center'),
            strtolower($this->entityTypes[$notificationData->entity_type]['label']),
            __('This message was sent via', 'notification-center'),
            get_site_url()
        );

        // Get all notifiers
        $notifiers = $wpdb->get_results("
            SELECT u.user_email
            FROM {$wpdb->prefix}notifications n
            LEFT JOIN {$wpdb->users} u
                ON n.notifier_id = u.ID
            WHERE n.notification_object_id = {$notificationId}"
        );

        if (is_array($notifiers) && !empty($notifiers)) {
            foreach ($notifiers as $key => $notifier) {
                //Send the email
                $mail = wp_mail(
                    $notifier->user_email,
                    sprintf(__('New notification on %s', 'notification-center'), get_bloginfo()),
                    $message,
                    array(
                        'From: ' . get_bloginfo() . ' <' . get_option('admin_email') . '>',
                        'Content-Type: text/html; charset=UTF-8'
                    )
                );
            }
        }
    }
}
