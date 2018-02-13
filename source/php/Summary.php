<?php

namespace NotificationCenter;

class Summary
{
    public function __construct()
    {
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
        $entityTypes = \NotificationCenter\Helper\EntityTypes::getEntityTypes();

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
                    AND no.created > NOW() - INTERVAL 24 HOUR
                ORDER BY no.created DESC
            ");

            ob_start();
            include NOTIFICATIONCENTER_TEMPLATE_PATH . 'email-summary.php';
            $emailTemplate = ob_get_clean();
            $emailTemplate = '<html><body style="background:#fff; padding: 10px; font-family: Helvetica, Arial, Verdana, sans-serif;">' . $emailTemplate . '</body></html>';

            $mail = wp_mail(
                $notifier->user_email,
                __('Notification summary', 'notification-center'),
                $emailTemplate,
                array(
                    'From: ' . get_bloginfo() . ' <' . get_option('admin_email') . '>',
                    'Content-Type: text/html; charset=UTF-8'
                )
            );
        }
    }
}
