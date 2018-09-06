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
        if (get_field('notification_email_summary', 'option') == false || get_current_blog_id() != 1) {
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
            WHERE no.created BETWEEN NOW() - INTERVAL 48 HOUR AND NOW() - INTERVAL 24 HOUR
                AND n.status = 0
            GROUP BY u.ID
        ");

        foreach ($notifiers as $key => $notifier) {
            // Skip if user have disabled emails
            $disabled = get_user_meta($notifier->ID, 'disable_notification_email', true);
            if ($disabled == true) {
                continue;
            }

            // Get users notifications
            $notifications = $wpdb->get_results("
                SELECT *, COUNT(*) count
                FROM {$wpdb->prefix}notifications n
                INNER JOIN {$wpdb->prefix}notification_objects no
                    ON n.notification_object_id = no.ID
                WHERE n.notifier_id = {$notifier->ID}
                    AND n.status = 0
                    AND no.created BETWEEN NOW() - INTERVAL 48 HOUR AND NOW()
                GROUP BY CASE
                            WHEN no.post_id IS NOT NULL
                            THEN 1
                            ELSE 0
                        END, no.post_id, no.entity_type, no.blog_id, n.status
                ORDER BY no.created DESC
            ");

            if (empty($notifications)) {
                continue;
            }

            $data = array();
            $data['heading'] = (!empty(get_field('notification_email_heading', 'option'))) ? get_field('notification_email_heading', 'option') : __('Your latest notifications', 'notification-center');
            $data['footer'] = apply_filters('notification_center/email_footer', sprintf('%s <a href="%s">%s</a>', __('This message was sent via', 'notification-center'), get_option('home'), get_option('home')));
            $data['entityTypes'] = Helper\EntityTypes::getEntityTypes();
            $data['notifications'] = $notifications;

            $emailTemplate = Helper\Display::blade('email-summary', $data);
            $emailTemplate = '<html><body style="background:#fff; padding: 10px; font-family: Helvetica, Arial, Verdana, sans-serif;">' . $emailTemplate . '</body></html>';

            $senderEmail = (!empty(get_field('notification_sender_email', 'option'))) ? get_field('notification_sender_email', 'option') : get_option('admin_email');
            $senderName = (!empty(get_field('notification_sender_name', 'option'))) ? get_field('notification_sender_name', 'option') : get_bloginfo();
            $subject = (!empty(get_field('notification_email_subject', 'option'))) ? get_field('notification_email_subject', 'option') : __('New notifications', 'notification-center');

            $mail = wp_mail(
                $notifier->user_email,
                $subject,
                $emailTemplate,
                array(
                    'From: ' . $senderName . ' <' . $senderEmail . '>',
                    'Content-Type: text/html; charset=UTF-8'
                )
            );
        }
    }
}
