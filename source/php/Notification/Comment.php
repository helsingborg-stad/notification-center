<?php

namespace NotificationCenter\Notification;

class Comment extends \NotificationCenter\Notification
{
    public function init()
    {
        add_action('wp_insert_comment', array($this, 'newComment'), 99, 2);
        add_action('delete_comment', array($this, 'deleteCommentNotificaitons'), 10, 2);
    }

    /**
     * Delete notifications related to the comment
     * @param  int $postId      The comment ID
     * @param  obj $commentObj  The comment to be deleted
     * @return void
     */
    public function deleteCommentNotificaitons($commentId, $commentObj)
    {
        global $wpdb;
        $postType = get_post_type($commentObj->comment_post_ID);

        if (! \NotificationCenter\App::isActivated($postType)) {
            return;
        }

        $entityTypes = \NotificationCenter\Helper\EntityTypes::getEntityTypes();

        // Get all comment entities
        $entities = array_keys(array_column($entityTypes, 'type'), 'comment');
        $entities = implode(',', $entities);

        // Delete the comment notifications
        $dbTable = $wpdb->prefix . 'notification_objects';
        $wpdb->query("DELETE FROM {$dbTable} WHERE entity_id = {$commentId} AND entity_type IN({$entities})");
    }

    /**
     * Do notifications on insert comment hook
     * @param  int $commentId  The new comment's ID
     * @param  obj $commentObj WP_Comment object
     * @return void
     */
    public function newComment($commentId, $commentObj)
    {
        $postId = $commentObj->comment_post_ID;

        // Bail if notifications is not activated
        if (! \NotificationCenter\App::isActivated(get_post_type($postId))) {
            return;
        }

        // Gather notifiers to avoid adding multiple notifications
        $notifiers = array();
        // Get list of post followers
        $followers = get_post_meta($postId, 'post_followers', true);

        if ($commentObj->comment_parent > 0) {
            /** Entity #1 : Comment reply **/
            $parentComment = get_comment($commentObj->comment_parent);
            $notifiers[] = (int)$parentComment->user_id;
            // Notify the comment author, even if the post is not followed
            $this->insertNotifications(1, $commentId, $notifiers, $commentObj->user_id, $postId);

            /** Entity #2 : Post thread contribution. **/
            $contributors = get_comments(array(
                                'parent' => $commentObj->comment_parent,
                                'author__not_in' => array(0, $commentObj->user_id, (int)$parentComment->user_id)
                            ));
            if (!empty($contributors)) {
            $contributorNotifiers = array();
                foreach ($contributors as $key => $contributor) {
                    // Add notifer if user exists in follower array and is set to true
                    if (empty($followers)
                        || (is_array($followers)
                        && array_key_exists($contributor->user_id, $followers)
                        && $followers[$contributor->user_id])) {

                        $contributorNotifiers[] = (int)$contributor->user_id;
                    }
                }

                $this->insertNotifications(2, $commentId, $contributorNotifiers, $commentObj->user_id, $postId);
                $notifiers = array_merge($notifiers, $contributorNotifiers);
            }
        }

        /** Entity #0 : New post comment on your post **/
        $notifier = get_post_field('post_author', $postId);
        if (!in_array($notifier, $notifiers)
            && (empty($followers)
            || (is_array($followers)
            && array_key_exists($notifier, $followers)
            && $followers[$notifier]))) {

            $this->insertNotifications(0, $commentId, array((int) $notifier), $commentObj->user_id, $postId);
            $notifiers[] = (int) $notifier;
        }

        /** Entity #3: New post comment on followed post **/
        // Remove 'unfollowed' and already notified users
        $followers = array_keys(array_filter($followers));
        if (is_array($followers) && !empty($followers)) {
            $notifiers = array_diff($followers, $notifiers);

            $this->insertNotifications(3, $commentId, $notifiers, $commentObj->user_id, $postId);
        }
    }
}
