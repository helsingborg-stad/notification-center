<?php
/**
 * Define entity types
 * post, comment, like, mention, group etc
 */
return [
    0 => [
        'type' => 'comment',
        'description' => 'New post comment',
        'message' => __('commented on your post', 'notification-center')
    ],
    1 => [
        'type' => 'comment',
        'description' => 'Comment reply',
        'message' => __('replied to your comment', 'notification-center')
    ],
    2 => [
        'type' => 'comment',
        'description' => 'Post thread contribution',
        'message' => __('also commented on', 'notification-center')
    ]
];
