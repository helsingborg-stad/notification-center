<?php

namespace NotificationCenter\Notification;

class Comment extends \NotificationCenter\Notification
{
    public function init()
    {
        add_action('wp_insert_comment', array($this, 'newComment'), 99, 2);
    }

    /**
     * Do notifications on insert comment hook
     * @param  int $commentId  The new comment's ID
     * @param  obj $commentObj WP_Comment object
     * @return void
     */
    public function newComment($commentId, $commentObj)
    {
        // Gather notifiers to avoid adding multiple notifications
        $notifiers = array();
        // Get list of post followers
        $followers = get_post_meta($commentObj->comment_post_ID, 'post_followers', true);

        if ($commentObj->comment_parent > 0) {
            // Entity #1 : Comment reply
            $parentComment = get_comment($commentObj->comment_parent);
            $notifiers[] = (int)$parentComment->user_id;
            // Add notifer if user exists in follower array and is set to true
            if (empty($followers)
                || (is_array($followers)
                && array_key_exists($parentComment->user_id, $followers)
                && $followers[$parentComment->user_id])) {

                $this->insertNotifications(1, $commentId, $notifiers, $commentObj->user_id);
            }

            // Entity #2 : Post thread contribution.
            $contributors = get_comments(array(
                                'parent' => $commentObj->comment_parent,
                                'author__not_in' => array(0, $commentObj->user_id, (int)$parentComment->user_id)
                            ));
            if (!empty($contributors)) {
                $notifiers = array();
                foreach ($contributors as $key => &$contributor) {
                    // Add notifer if user exists in follower array and is set to true
                    if (empty($followers)
                        || (is_array($followers)
                        && array_key_exists($contributor->user_id, $followers)
                        && $followers[$contributor->user_id])) {

                        $notifiers[] = (int)$contributor->user_id;
                    }
                }

                $this->insertNotifications(2, $commentId, $notifiers, $commentObj->user_id);
            }
        }

        // Entity #0 : New post comment on your post
        $notifier = get_post_field('post_author', $commentObj->comment_post_ID);
        if (!in_array($notifier, $notifiers)
            && (empty($followers)
            || (is_array($followers)
            && array_key_exists($notifier, $followers)
            && $followers[$notifier]))) {

            $this->insertNotifications(0, $commentId, array((int) $notifier), $commentObj->user_id);
            $notifiers[] = (int) $notifier;
        }

        // Entity #3: New post comment on followed post
        // Remove 'unfollowed' and already notified users
        $followers = array_keys(array_filter($followers));
        if (is_array($followers) && !empty($followers)) {
            $notifiers = array_diff_key($followers, $notifiers);
            $this->insertNotifications(3, $commentId, $notifiers, $commentObj->user_id);
        }
    }
}
