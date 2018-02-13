<?php

namespace NotificationCenter\Admin;

class Options
{
    public function __construct()
    {
        if (function_exists('acf_add_options_sub_page')) {
            acf_add_options_sub_page(array(
                'page_title'    => _x('Notification center settings', 'Notification center', 'notification-center'),
                'menu_title'    => _x('Notifications', 'Notification center', 'notification-center'),
                'menu_slug'     => 'notification-center-options',
                'parent_slug'   => 'options-general.php',
                'capability'    => 'manage_options'
            ));
        }
    }
}
