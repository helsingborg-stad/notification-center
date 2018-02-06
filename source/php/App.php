<?php

namespace NotificationCenter;

class App
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));

        new Install();
        new Notification();
        new Dropdown();
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
        wp_enqueue_script('notification-center', NOTIFICATIONCENTER_URL . '/dist/js/notification-center.min.js', '', '', true);
    }
}
