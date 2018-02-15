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

        $unseen = $wpdb->get_results("
            SELECT COUNT(*) count
            FROM {$wpdb->prefix}notifications n
            INNER JOIN {$wpdb->prefix}notification_objects no
                ON n.notification_object_id = no.ID
            WHERE n.notifier_id = {$user->ID}
                AND n.status = 0
                AND no.created > NOW() - INTERVAL 30 DAY
            GROUP BY CASE
                WHEN no.post_id IS NOT NULL
                THEN 1
                ELSE 0
            END, no.post_id, no.entity_type
        ");
        $unseen = $wpdb->num_rows;

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
            SELECT *, COUNT(*) count,
                GROUP_CONCAT(DISTINCT n.ID SEPARATOR ',') AS id_list
            FROM {$wpdb->prefix}notifications n
            INNER JOIN {$wpdb->prefix}notification_objects no
                ON n.notification_object_id = no.ID
            WHERE n.notifier_id = {$userId}
                AND no.created > NOW() - INTERVAL 30 DAY
            GROUP BY CASE
                        WHEN no.post_id IS NOT NULL
                        THEN 1
                        ELSE 0
                    END, no.post_id, no.entity_type, n.status
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

        if (empty($_POST['notificationIds'])) {
            wp_die();
        }

        global $wpdb;
        $user   = wp_get_current_user();
        $userId = $user->ID;
        $ids    = $_POST['notificationIds'];

        $wpdb->query("
            UPDATE {$wpdb->prefix}notifications
            SET status = 1
            WHERE ID IN ({$ids})
                AND notifier_id = $userId
        ");

        echo 'success';
        wp_die();
    }
}
