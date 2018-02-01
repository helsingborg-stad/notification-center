<?php

namespace NotificationCenter;

class Notification
{
    protected $entityTypes;

    public function __construct()
    {
        $this->entityTypes = include(NOTIFICATIONCENTER_PATH . 'source/php/config/EntityTypes.php');

        add_action('wp_insert_comment', array($this, 'newComment'), 99, 2);

        // Cron event
        add_action('send_notification_email', array($this, 'emailNotifiers'), 10, 1);
    }

    public function testing()
    {
        $this->emailNotifiers(26);
    }

    /**
     * Do notifications on insert comment hook
     * @param  int $commentId  The new comment's ID
     * @param  obj $commentObj WP_Comment object
     * @return void
     */
    public function newComment($commentId, $commentObj)
    {
        // Entity: New post comment
        $notifier = get_post_field('post_author', $commentObj->comment_post_ID);
        $this->insertNotifications(0, $commentObj->comment_post_ID, array((int) $notifier), $commentObj->user_id);

        if ($commentObj->comment_parent > 0) {
            // Entity: New comment reply
            $parentComment = get_comment($commentObj->comment_parent);
            $this->insertNotifications(1, $commentObj->comment_post_ID, array((int) $parentComment->user_id), $commentObj->user_id);

            // Entity: New thread contribution
            $contributors = get_comments(array(
                                'parent' => $commentObj->comment_parent,
                                'author__not_in' => array($commentObj->user_id)
                            ));

            $notifiers = array();
            if (!empty($contributors)) {
                foreach ($contributors as $key => &$contributor) {
                    // Continue if user does not exist
                    if (!$contributor->user_id) {
                        continue;
                    }

                    $notifiers[] = (int) $contributor->user_id;
                }

                $this->insertNotifications(2, $commentObj->comment_post_ID, $notifiers, $commentObj->user_id);
            }
        }
    }

    /**
     * Insert notifications to db
     * @param  int      $entityType Entity type
     * @param  int      $entityId   Entity ID, eg. Post ID
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
        wp_schedule_single_event(time() + 20, 'send_notification_email', array($notificationId));

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
        /**
         * TODO
         * Skip if user has "Entity type email setting = Off"
         * Error handling
         * Style email message and fix the text
         * Change sender email
         */

        global $wpdb;

        // Get notificaiton data
        $notificationData = $wpdb->get_row("
            SELECT no.ID, no.entity_type, no.entity_id, no.created, no.sender_id, u.display_name
            FROM {$wpdb->prefix}notification_objects no
            INNER JOIN {$wpdb->prefix}notifications n
                ON no.ID = n.notification_object_id
            LEFT JOIN {$wpdb->users} u
                ON u.ID = no.sender_id
            WHERE no.ID = {$notificationId}
            GROUP BY no.ID"
        );

        // Build notification message
        $message = $this->buildMessage(
                                $notificationData->entity_type,
                                $notificationData->entity_id,
                                $notificationData->display_name
                            );
        $message .= '<br><br>---<br>' . sprintf(__('This message was sent via %s', 'notification-center'), get_site_url());

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

    /**
     * Build the notification message
     * @param  int    $entityType   Entity type ID
     * @param  int    $entityId     Entity ID
     * @param  string $senderName   Sender name
     * @return string               The notification message
     */
    public function buildMessage($entityType, $entityId, $senderName)
    {
        // Set sender name to 'Someone' if missing
        $senderName = $senderName ? $senderName : __('Someone', 'notification-center');
        $message  = '<strong>' . $senderName . '</strong> ' . $this->entityTypes[$entityType]['message'];
        $message .= ' <a href="' . get_the_permalink($entityId) . '">"' . get_the_title($entityId) . '"</a>';

        return $message;
    }
}
