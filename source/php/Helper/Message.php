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
            case 'post_type':
                $postType = get_post_type($entityId);
                $postTypeObj = get_post_type_object($postType);
                $message = sprintf('<strong>%s</strong> %s <strong>%s</strong>',
                    $isSingular ? $senderName : $count,
                    $isSingular ? $entityTypes[$entityType]['message_singular'] : $entityTypes[$entityType]['message_plural'],
                    $postTypeObj->labels->singular_name
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
                $commentObj = get_comment($entityId);

                $postType = get_post_type($commentObj->comment_post_ID);
                $url = add_query_arg( array(
                    'post_type' => $postType
                ), wp_get_shortlink($commentObj->comment_post_ID));

                // Add comment/answer target
                $url = $commentObj->comment_parent > 0 ? $url . '#answer-' .  $entityId : $url . '#comment-' .  $entityId;

                break;

            default:
                $postType = get_post_type($entityId);
                $url = add_query_arg( array(
                    'post_type' => $postType
                ), wp_get_shortlink($entityId));

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
