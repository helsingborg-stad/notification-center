<?php

namespace NotificationCenter\Notification;

class Comment extends \NotificationCenter\Notification
{
    public function init()
    {
        add_action('wp_insert_comment', array($this, 'newComment'), 99, 2);
        add_action('delete_comment', array($this, 'deleteCommentNotifications'), 10, 2);
        add_filter('wp_update_comment_data', array($this, 'updatedComment'), 10, 3);
    }

    /**
     * Delete notifications related to the comment
     * @param  int $postId      The comment ID
     * @param  obj $commentObj  The comment to be deleted
     * @return void
     */
    public function deleteCommentNotifications($commentId, $commentObj)
    {
        global $wpdb;
        $postType = get_post_type($commentObj->comment_post_ID);
        $blogId = get_current_blog_id();

        if (! \NotificationCenter\App::isActivated($postType)) {
            return;
        }

        $entityTypes = \NotificationCenter\Helper\EntityTypes::getEntityTypes();

        // Get all comment entities
        $entities = array_keys(array_column($entityTypes, 'type'), 'comment');
        $entities = implode(',', $entities);

        // Delete the comment notifications
        $dbTable = $wpdb->base_prefix . 'notification_objects';
        $wpdb->query("DELETE FROM {$dbTable} WHERE entity_id = {$commentId} AND entity_type IN({$entities}) AND blog_id = {$blogId}");
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
        // Gather notifiers to avoid adding multiple notifications for the same event
        $notifiers = array();

        // Get all mentioned users
        preg_match_all('/data-mention-id="(\d*?)"/', stripslashes($commentObj->comment_content), $matches);

        // Notify all mentioned users
        if (isset($matches[1]) && !empty($matches[1])) {
            foreach ($matches[1] as $key => $notifier) {
                /** Entity #7 : User mention in comments **/
                $this->insertNotifications(7, $commentId, array((int) $notifier), $commentObj->user_id, $postId);
                $notifiers[] = (int)$notifier;
            }
        }

        // Bail if notifications is not activated
        if (! \NotificationCenter\App::isActivated(get_post_type($postId))) {
            return;
        }

        // Get list of post followers
        $followers = get_post_meta($postId, 'post_followers', true);

        if ($commentObj->comment_parent > 0) {
            /** Entity #1 : Comment reply **/
            $parentComment = get_comment($commentObj->comment_parent);
            $notifier = (int)$parentComment->user_id;
            if (!in_array($notifier, $notifiers)) {
                // Notify the comment author, even if the post is not followed
                $this->insertNotifications(1, $commentId, array($notifier), $commentObj->user_id, $postId);
                $notifiers[] = $notifier;
            }

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
        if (is_array($followers) && !empty($followers)) {
            $followers = array_keys(array_filter($followers));
            $notifiers = array_diff($followers, $notifiers);

            $this->insertNotifications(3, $commentId, $notifiers, $commentObj->user_id, $postId);
        }
    }

    /**
     * Notify mentioned users on comment update
     * @param array $data       The new, processed comment data.
     * @param array $comment    The old, unslashed comment data.
     * @param array $commentArr The new, raw comment data.
     */
    public function updatedComment($data, $comment, $commentArr)
    {
        $pattern = '/data-mention-id="(\d*?)"/';

        // Extract old mentioned users
        preg_match_all($pattern, stripslashes($comment['comment_content']), $oldMatches);

        // Extract new mentioned users
        preg_match_all($pattern, stripslashes($data['comment_content']), $newMatches);

        // Keep the newly mentioned users
        $notifiers = array_diff($newMatches[1] ?? array(), $oldMatches[1] ?? array());

        // Notify all mentioned users
        if (is_array($notifiers) && !empty($notifiers)) {
            foreach ($notifiers as $notifier) {
                /** Entity #7 : User mention in comments **/
                $this->insertNotifications(7, $data['comment_ID'], array((int) $notifier), $data['user_id'], $data['comment_post_ID']);
            }
        }

        return $data;
    }
}
