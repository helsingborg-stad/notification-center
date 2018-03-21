<?php

namespace NotificationCenter\Helper;

class Message
{
	/**
     * Build the notification message
     * @param  int    $entityType   Entity type ID
     * @param  int    $entityId     Entity ID
     * @param  string $senderId     Sender ID
     * @param  string $count        Number of notifications
     * @return string               The notification message
     */
    public static function buildMessage($entityType, $entityId, $senderId, $count, $blogId) : string
    {
        switch_to_blog((int) $blogId);

        $entityTypes = \NotificationCenter\Helper\EntityTypes::getEntityTypes();
        $senderName = self::getUserName($senderId);
        $isSingular = (int)$count == 1 ? true : false;

        // Build message depending on entity type, default is Post
        switch ($entityTypes[$entityType]['type']) {
            case 'comment':
                $commentObj = get_comment($entityId);
                $message = sprintf('<strong>%s</strong> %s <strong>%s</strong>',
                    $isSingular ? $senderName : $count,
                    $isSingular ? $entityTypes[$entityType]['message_singular'] : $entityTypes[$entityType]['message_plural'],
                    get_the_title($commentObj->comment_post_ID)
                );
                break;
            case 'comment_mention':
                $commentObj = get_comment($entityId);
                if ($isSingular) {
                    $message = sprintf('<strong>%s</strong> %s.',
                        $senderName,
                        $isSingular ? $entityTypes[$entityType]['message_singular'] : $entityTypes[$entityType]['message_plural']
                    );
                } else {
                    $message = sprintf('<strong>%s</strong> %s <strong>%s</strong>',
                        $count,
                        $entityTypes[$entityType]['message_plural'],
                        get_the_title($commentObj->comment_post_ID)
                    );
                }
                break;
            case 'post_type':
                $postTypeSlug = get_post_type($entityId);
                $postTypes = \NotificationCenter\App::activePostTypes();
                $postTypeLabel = !empty($postTypes[$postTypeSlug]) ? $postTypes[$postTypeSlug] : __('a post type you follow', 'notification-center');

                $message = sprintf('<strong>%s</strong> %s <strong>%s</strong> %s <strong>%s</strong>',
                    $senderName,
                    $entityTypes[$entityType]['message_singular'],
                    get_the_title($entityId),
                    __('in', 'notification-center'),
                    $postTypeLabel
                );
                break;
            default:
                $message = sprintf('<strong>%s</strong> %s <strong>%s</strong>',
                    $isSingular ? $senderName : $count,
                    $isSingular ? $entityTypes[$entityType]['message_singular'] : $entityTypes[$entityType]['message_plural'],
                    get_the_title($entityId)
                );
                break;
        }

        restore_current_blog();
        return $message;
    }

    /**
     * Get the notification URL
     * @param  int    $entityType   Entity type ID
     * @param  int    $entityId     Entity ID
     * @return string               The notification URL
     */
    public static function notificationUrl($entityType, $entityId, $blogId) : string
    {
        switch_to_blog((int) $blogId);
        $entityTypes = \NotificationCenter\Helper\EntityTypes::getEntityTypes();

        // Get URL depending on entity type, default is Post
        switch ($entityTypes[$entityType]['type']) {
            case 'comment':
            case 'comment_mention':
                $commentObj = get_comment($entityId);
                $postType = get_post_type($commentObj->comment_post_ID);
                // Parameter 'page_id' only works with Pages
                $param = $postType == 'page' ? 'page_id' : 'p';
                $url = add_query_arg( array(
                    $param => $commentObj->comment_post_ID,
                    'post_type' => $postType
                ), home_url('/'));

                // Add comment/answer target
                $url = $commentObj->comment_parent > 0 ? $url . '#answer-' .  $entityId : $url . '#comment-' .  $entityId;

                break;

            default:
                $postType = get_post_type($entityId);
                $param = $postType == 'page' ? 'page_id' : 'p';
                $url = add_query_arg( array(
                    $param => $entityId,
                    'post_type' => $postType
                ), home_url('/'));

                break;
        }

        restore_current_blog();
        return $url;
    }

    /**
     * Get user name, either full name, first name or display name
     * @param  int      $userId The user ID
     * @return string           The user's name.
     */
    public static function getUserName($userId)
    {
        if (!$userId) {
            return __('Someone', 'notification-center');
        }

        $userInfo = get_userdata($userId);
        if ($userInfo->first_name) {
            if ($userInfo->last_name) {
                return $userInfo->first_name . ' ' . $userInfo->last_name;
            }
            return $userInfo->first_name;
        }
        return $userInfo->display_name;
    }
}
