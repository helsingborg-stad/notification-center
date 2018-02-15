<?php

namespace NotificationCenter\Notification;

class Update extends \NotificationCenter\Notification
{
    public function init()
    {
        add_action('save_post', array($this, 'updatePostNotification'), 10, 3);
    }

    /**
     * Sends a notification to post followers when a post is saved
     * @param  int $postId Post ID
     * @return void
     */
    public function updatePostNotification($postId, $post, $update)
    {
        // Bail if notifications: is not activated, autosave function, revision
        if (! \NotificationCenter\App::isActivated(get_post_type($postId))
            || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            || wp_is_post_revision($postId)) {
            return;
        }

        // Deny for autosave function and revision
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($postId)) {
            return;
        }
        /** Entity #4 : New post update **/
        $followers = get_post_meta($postId, 'post_followers', true);
        $followers = array_keys(array_filter($followers));
        if (is_array($followers) && !empty($followers)) {
            $user = wp_get_current_user();
            $this->insertNotifications(4, $postId, $followers, $user->ID, $postId);
        }
    }
}
