<?php

namespace NotificationCenter;

class Dropdown
{
    public function __construct()
    {
        add_action('init', array($this, 'registerShortcodes'));
        add_action('wp_ajax_change_status', array($this,'changeStatus'));
    }

    public function registerShortcodes()
    {
       add_shortcode('notification-center', array($this, 'notificationCenter'));
    }

    public function notificationCenter() {
        if (!is_user_logged_in()) {
            return;
        }

        $toggleIcon     = apply_filters('notification_center/markup/icon', '<i class="pricon pricon-bell notification-toggle__icon"></i>');
        $notifications  = $this->getUserNotifications();
        $unseen         = $this->getUnseen();

        include NOTIFICATIONCENTER_TEMPLATE_PATH . 'dropdown.php';
    }

    /**
     * Get number of unseen notifications
     * @return string   Number of notifications
     */
    public function getUnseen()
    {
        global $wpdb;
        $user = wp_get_current_user();

        $unseen = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->prefix}notifications n
            LEFT JOIN {$wpdb->prefix}notification_objects no
                ON n.notification_object_id = no.ID
            WHERE n.notifier_id = {$user->ID}
            AND n.status = 0
            AND no.created > NOW() - INTERVAL 30 DAY"
        );

        return $unseen;
    }

    public function getUserNotifications($userId = null, $offset = 0)
    {
        global $wpdb;
        $user = wp_get_current_user();
        $userId = $userId ? $userId : $user->ID;
        $maxOffset = (int) $offset + 7;

        $notifications = $wpdb->get_results("
            SELECT *
            FROM {$wpdb->prefix}notifications n
            INNER JOIN {$wpdb->prefix}notification_objects no
                ON n.notification_object_id = no.ID
            WHERE n.notifier_id = {$userId}
            ORDER BY no.created DESC
            LIMIT {$offset}, {$maxOffset};"
        );

        return $notifications;
    }

    public function changeStatus()
    {
        if (empty($_POST['notificationId'])) {
            wp_die();
        }

        global $wpdb;
        $id = (int)$_POST['notificationId'];

        $wpdb->update(
            $wpdb->prefix . 'notifications',
            array('status' => 1),
            array('ID' => $id),
            array('%d'),
            array('%d')
        );

        echo 'success';
        wp_die();
    }
}
