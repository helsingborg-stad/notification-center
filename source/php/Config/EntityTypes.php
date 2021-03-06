<?php
/**
 * Define entity types
 * post, comment, like, mention, group etc
 */
return [
    0 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'New comment on your post',
        'icon' => '<i class="pricon pricon-badge pricon-comments"></i>',
        'message_singular' => __('commented on your post', 'notification-center'),
        'message_plural' => __('new comments on your post', 'notification-center')
    ],
    1 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'Comment reply',
        'icon' => '<i class="pricon pricon-badge pricon-comments"></i>',
        'message_singular' => __('replied to your comment on', 'notification-center'),
        'message_plural' => __('new comment replies on', 'notification-center')
    ],
    2 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'Post thread contribution',
        'icon' => '<i class="pricon pricon-badge pricon-discuss"></i>',
        'message_singular' => __('also replied to a comment on', 'notification-center'),
        'message_plural' => __('new comment thread contributions on', 'notification-center')
    ],
    3 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'New comment on followed post',
        'icon' => '<i class="pricon pricon-badge pricon-comments"></i>',
        'message_singular' => __('commented on a post you follow', 'notification-center'),
        'message_plural' => __('new comments on your followed post', 'notification-center')
    ],
    4 => [
        'type' => 'post',
        'label' => __('Post', 'notification-center'),
        'description' => 'New update on followed post',
        'icon' => '<i class="pricon pricon-badge pricon-settings"></i>',
        'message_singular' => __('updated a post you follow', 'notification-center'),
        'message_plural' => __('new updates on your followed post', 'notification-center')
    ],
    5 => [
        'type' => 'post_type',
        'label' => __('Post', 'notification-center'),
        'description' => 'New post created on followed post type',
        'icon' => '<i class="pricon pricon-badge pricon-plus"></i>',
        'message_singular' => __('created', 'notification-center'),
        'message_plural' => '',
    ],
    6 => [
        'type' => 'post',
        'label' => __('Post', 'notification-center'),
        'description' => 'New post mention',
        'icon' => '<i class="pricon pricon-badge pricon-user"></i>',
        'message_singular' => __('mentioned you in', 'notification-center'),
        'message_plural' => __('persons mentioned you in', 'notification-center')
    ],
    7 => [
        'type' => 'comment_mention',
        'label' => __('Comment', 'notification-center'),
        'description' => 'New comment mention',
        'icon' => '<i class="pricon pricon-badge pricon-user"></i>',
        'message_singular' => __('mentioned you in a comment', 'notification-center'),
        'message_plural' => __('persons mentioned you in', 'notification-center')
    ],
    8 => [
        'type' => 'post',
        'label' => __('Forum', 'notification-center'),
        'description' => 'New forum invitation',
        'icon' => '<i class="pricon pricon-badge pricon-group"></i>',
        'message_singular' => __('invited you to the forum', 'notification-center'),
        'message_plural' => __('persons invited you to the forum', 'notification-center')
    ],
    9 => [
        'type' => 'comment',
        'label' => __('Like', 'notification-center'),
        'description' => 'New comment like',
        'icon' => '<i class="pricon pricon-badge pricon-thumbs-up"></i>',
        'message_singular' => __('liked your comment on', 'notification-center'),
        'message_plural' => __('persons liked your comment on', 'notification-center')
    ]
];
