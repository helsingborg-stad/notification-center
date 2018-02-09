<?php

namespace NotificationCenter;

class App
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));

        new Install();
        new Dropdown();

        // Register notification types
        new Notification\Comment();
        new Notification\Follow();
    }

    /**
     * Enqueue required style
     * @return void
     */
    public function enqueueStyles()
    {
        wp_enqueue_style('notification-center', NOTIFICATIONCENTER_URL . '/dist/css/notification-center.min.css');
    }

    /**
     * Enqueue required scripts
     * @return void
     */
    public function enqueueScripts()
    {
        wp_enqueue_script('notification-center', NOTIFICATIONCENTER_URL . '/dist/js/notification-center.min.js', 'jquery', false, true);
        wp_localize_script('notification-center', 'notificationCenter', array(
            'follow'    => __('Follow', 'notification-center'),
            'following' => __('Following', 'notification-center'),
        ));
        wp_enqueue_script('notification-center');
    }
}
