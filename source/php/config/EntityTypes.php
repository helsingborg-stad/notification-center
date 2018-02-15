<?php
/**
 * Define entity types
 * post, comment, like, mention, group etc
 */
return [
    0 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'New post comment on your post',
        'message' => __('commented on your post', 'notification-center'),
        'icon' => '<i class="pricon pricon-comments pricon-badge"></i>'
    ],
    1 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'Comment reply',
        'message' => __('replied to your comment on', 'notification-center'),
        'icon' => '<i class="pricon pricon-comments pricon-badge"></i>'
    ],
    2 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'Post thread contribution',
        'message' => __('also replied to a comment on', 'notification-center'),
        'icon' => '<i class="pricon pricon-discuss pricon-badge"></i>'
    ],
    3 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'New post update',
        'message' => __('commented on a post you follow', 'notification-center'),
        'icon' => '<i class="pricon pricon-comments pricon-badge"></i>'
    ],
    4 => [
        'type' => 'update',
        'label' => __('Page', 'notification-center'),
        'description' => 'New post update on followed post',
        'message' => __('updated a page you follow', 'notification-center'),
        'icon' => '<i class="pricon pricon-check pricon-badge"></i>'
    ]
];
