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
        new Summary();
        new Follow();
        new Admin\Options();

        // Register notification types
        new Notification\Comment();
        new Notification\Post();
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
            'unfollow'  => __('Unfollow', 'notification-center'),
        ));
        wp_enqueue_script('notification-center');
    }

    /**
     * Checks if notifications is activated for a post type
     * @return boolean
     */
    public static function isActivated($postType) : bool
    {
        if (empty($postType)) {
            return false;
        }

        $postTypes = apply_filters('notification_center/activated_posttypes', get_post_types(array('public' => true)));
        return in_array($postType, $postTypes);
    }
}
