<?php

namespace NotificationCenter;

class Summary
{
    public $entityTypes = array();

    public function __construct()
    {
        $this->entityTypes = \NotificationCenter\Helper\EntityTypes::getEntityTypes();

        /* Register cron event */
        add_action('email_notification_summary', array($this, 'emailDailySummary'));
    }

    public static function addCronJob()
    {
        wp_schedule_event(strtotime('tomorrow +15 hours'), 'daily', 'email_notification_summary');
    }

    public static function removeCronJob()
    {
        wp_clear_scheduled_hook('email_notification_summary');
    }

    /**
     * Send daily summary of notifications as email
     * @param  int $notificationId Notification ID
     * @return void
     */
    public function emailDailySummary()
    {
        if (get_field('notification_email_summary', 'option') == false) {
            return;
        }

        global $wpdb;

        // Get notifiers
        $notifiers = $wpdb->get_results("
            SELECT u.ID, u.user_email
            FROM {$wpdb->users} u
            JOIN {$wpdb->prefix}notifications n
                ON n.notifier_id = u.ID
            JOIN {$wpdb->prefix}notification_objects no
                ON no.ID = n.notification_object_id
            WHERE no.created > NOW() - INTERVAL 24 HOUR
            AND n.status = 0
            GROUP BY u.ID
        ");

        foreach ($notifiers as $key => $notifier) {
            // Get users notifications
            $notifications = $wpdb->get_results("
                SELECT *
                FROM {$wpdb->prefix}notifications n
                LEFT JOIN {$wpdb->prefix}notification_objects no
                    ON n.notification_object_id = no.ID
                WHERE n.notifier_id = {$notifier->ID}
                AND n.status = 0
                AND no.created > NOW() - INTERVAL 24 HOUR"
            );

            // Build the email message
            $message = '';
            foreach ($notifications as $key => $notification) {
                $text = \NotificationCenter\Helper\Message::buildMessage($notification->entity_type, $notification->entity_id, $notification->sender_id);
                $url = \NotificationCenter\Helper\Message::notificationUrl($notification->entity_type, $notification->entity_id);
                $message .= sprintf('%s <br><a href="%s">%s %s</a><br><br>',
                    $text,
                    $url,
                    __('Show', 'notification-center'),
                    strtolower($this->entityTypes[$notification->entity_type]['label'])
                );
            }

            $message .= sprintf('---<br> %s %s',
                __('This message was sent via', 'notification-center'),
                get_site_url()
            );

            $mail = wp_mail(
                $notifier->user_email,
                sprintf(__('Summary of notifications on %s', 'notification-center'), get_bloginfo()),
                $message,
                array(
                    'From: ' . get_bloginfo() . ' <' . get_option('admin_email') . '>',
                    'Content-Type: text/html; charset=UTF-8'
                )
            );
        }
    }
}
