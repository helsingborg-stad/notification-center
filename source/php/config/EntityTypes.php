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
        'message' => __('commented on your post', 'notification-center')
    ],
    1 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'Comment reply',
        'message' => __('replied to your comment on', 'notification-center')
    ],
    2 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'Post thread contribution',
        'message' => __('also replied to a comment on', 'notification-center')
    ],
    3 => [
        'type' => 'comment',
        'label' => __('Comment', 'notification-center'),
        'description' => 'New post update',
        'message' => __('commented on a post you follow', 'notification-center')
    ],
    4 => [
        'type' => 'update',
        'label' => __('Update', 'notification-center'),
        'description' => 'New post update on followed post',
        'message' => __('updated a post you follow', 'notification-center')
    ]
];
