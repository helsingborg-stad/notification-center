<?php
/**
 * Define entity types
 * post, comment, like, mention, group etc
 */
return [
    0 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'New comment on your page',
        'icon' => '<i class="pricon pricon-comments pricon-badge"></i>',
        'message_singular' => __('commented on your page', 'notification-center'),
        'message_plural' => __('new comments on your page', 'notification-center')
    ],
    1 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'Comment reply',
        'icon' => '<i class="pricon pricon-comments pricon-badge"></i>',
        'message_singular' => __('replied to your comment on', 'notification-center'),
        'message_plural' => __('new comment replies on', 'notification-center')
    ],
    2 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'Post thread contribution',
        'icon' => '<i class="pricon pricon-discuss pricon-badge"></i>',
        'message_singular' => __('also replied to a comment on', 'notification-center'),
        'message_plural' => __('new comment thread contributions on', 'notification-center')
    ],
    3 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'New post update',
        'icon' => '<i class="pricon pricon-comments pricon-badge"></i>',
        'message_singular' => __('commented on a page you follow', 'notification-center'),
        'message_plural' => __('new comments on your followed page', 'notification-center')
    ],
    4 => [
        'type' => 'post',
        'label' => __('Page', 'notification-center'),
        'description' => 'New update on followed post',
        'icon' => '<i class="pricon pricon-check pricon-badge"></i>',
        'message_singular' => __('updated a page you follow', 'notification-center'),
        'message_plural' => __('new updates on your followed page', 'notification-center')
    ]
];
