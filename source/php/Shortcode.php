<?php

namespace NotificationCenter;

class Shortcode
{
    public function __construct()
    {
        add_action('init', array($this, 'registerShortcodes'));
    }

    public function registerShortcodes()
    {
       add_shortcode('notification-center', array($this, 'notificationCenter'));
    }

    public function notificationCenter($attr) {
        if (!is_user_logged_in()) {
            return;
        }

        $attributes = shortcode_atts(array(
            'icon-color' => '#000'
        ), $attr);

        $notifications = $this->getUserNotifications();

        $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15.137 3.945c-.644-.374-1.042-1.07-1.041-1.82v-.003c.001-1.172-.938-2.122-2.096-2.122s-2.097.95-2.097 2.122v.003c.001.751-.396 1.446-1.041 1.82-4.667 2.712-1.985 11.715-6.862 13.306v1.749h20v-1.749c-4.877-1.591-2.195-10.594-6.863-13.306zm-3.137-2.945c.552 0 1 .449 1 1 0 .552-.448 1-1 1s-1-.448-1-1c0-.551.448-1 1-1zm3 20c0 1.598-1.392 3-2.971 3s-3.029-1.402-3.029-3h6z"/></svg>';

        $markup  = '<div class="notification-center"><a href="#" class="notification-center__toggle">';
        $markup .= $icon;
        $markup .= '</a></div>';

        return $markup;
    }

    public function getUserNotifications($userId = null, $offset = 0)
    {
        global $wpdb;
        $user = wp_get_current_user();
        $userId = $userId ? $userId : $user->ID;
        $maxOffset = (int) $offset + 10;

        $notifications = $wpdb->get_results("
            SELECT *
            FROM {$wpdb->prefix}notifications n
            INNER JOIN {$wpdb->prefix}notification_objects no
                ON n.notification_object_id = no.ID
            WHERE n.notifier_id = {$userId}
            ORDER BY no.created DESC
            LIMIT {$offset}, {$maxOffset};"
        );

        echo "<pre>";
        var_dump($notifications);
        wp_die();

        return $notifications;
    }

}
