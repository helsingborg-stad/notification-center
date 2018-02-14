<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5a7dc01cb8cd6',
    'title' => __('Notification center', 'notification-center'),
    'fields' => array(
        0 => array(
            'key' => 'field_5a82ec34ea342',
            'label' => __('Daily email summary', 'notification-center'),
            'name' => 'notification_email_summary',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('Send daily email summary', 'notification-center'),
            'default_value' => 0,
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
        1 => array(
            'key' => 'field_5a84481599e44',
            'label' => __('Email subject', 'notification-center'),
            'name' => 'notification_email_subject',
            'type' => 'text',
            'instructions' => __('Leave empty to use default.', 'notification-center'),
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5a82ec34ea342',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
        2 => array(
            'key' => 'field_5a84482199e45',
            'label' => __('Heading', 'notification-center'),
            'name' => 'notification_email_heading',
            'type' => 'text',
            'instructions' => __('Heading used in email. Leave empty to use default.', 'notification-center'),
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5a82ec34ea342',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
        3 => array(
            'key' => 'field_5a844c1e22d55',
            'label' => __('Sender name', 'notification-center'),
            'name' => 'notification_sender_name',
            'type' => 'text',
            'instructions' => __('Leave empty to use default.', 'notification-center'),
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5a82ec34ea342',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
        4 => array(
            'key' => 'field_5a84482c99e46',
            'label' => __('Sender email address', 'notification-center'),
            'name' => 'notification_sender_email',
            'type' => 'email',
            'instructions' => __('Leave empty to use default.', 'notification-center'),
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5a82ec34ea342',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'notification-center-options',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}