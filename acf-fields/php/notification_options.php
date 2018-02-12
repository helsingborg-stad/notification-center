<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5a7dc01cb8cd6',
    'title' => 'Notification center',
    'fields' => array(
        0 => array(
            'key' => 'field_5a7dc17460fff',
            'label' => __('Follow', 'notification-center'),
            'name' => 'notification_post_types',
            'type' => 'posttype_select',
            'instructions' => __('Enable follow functionality for selected post types.', 'notification-center'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'allow_null' => 0,
            'multiple' => 1,
            'placeholder' => '',
            'disabled' => 0,
            'readonly' => 0,
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