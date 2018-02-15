<?php

namespace NotificationCenter;

class Dropdown
{
    public function __construct()
    {
        add_action('init', array($this, 'registerShortcodes'));
        add_action('wp_ajax_change_status', array($this, 'changeStatus'));
        add_action('wp_ajax_load_more', array($this, 'loadMore'));
    }

    /**
     * Register shortcodes
     */
    public function registerShortcodes()
    {
        add_shortcode('notification-center', array($this, 'notificationCenter'));
    }

    /**
     * Renders the dropdown with notifications
     */
    public function notificationCenter()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $data = array();
        $data['notifications']  = $this->getUserNotifications();
        $data['unseen']         = $this->getUnseen();
        $data['entityTypes']    = \NotificationCenter\Helper\EntityTypes::getEntityTypes();

        echo Helper\Display::blade('dropdown', $data);
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

    /**
     * Get users notifications
     * @param  integer|null  $userId Users id
     * @param  integer       $offset Row offset
     * @return array         List with notifications
     */
    public function getUserNotifications($userId = null, $offset = 0)
    {
        global $wpdb;
        $user = wp_get_current_user();
        $userId = $userId ? $userId : $user->ID;

        $notifications = $wpdb->get_results("
            SELECT *
            FROM {$wpdb->prefix}notifications n
            INNER JOIN {$wpdb->prefix}notification_objects no
                ON n.notification_object_id = no.ID
            WHERE n.notifier_id = {$userId}
                AND no.created > NOW() - INTERVAL 30 DAY
            ORDER BY no.created DESC
            LIMIT $offset, 15"
        );

        return $notifications;
    }

    /**
     * Returns additional user notifications
     */
    public function loadMore()
    {
        if (!isset($_POST['offset'])) {
            wp_send_json_error('Missing offset');
        }

        $offset = (int)$_POST['offset'];
        $notifications = $this->getUserNotifications(null, $offset);
        $data = array(
                'notifications' => $notifications,
                'entityTypes' => \NotificationCenter\Helper\EntityTypes::getEntityTypes()
                );
        $markup = (!empty($notifications)) ? Helper\Display::blade('partials.dropdown-items', $data) : '';

        echo($markup);
        wp_die();
    }

    /**
     * Change notification status to 'seen'
     */
    public function changeStatus()
    {
        ignore_user_abort(true);

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
