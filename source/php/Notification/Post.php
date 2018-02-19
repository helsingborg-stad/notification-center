<?php

namespace NotificationCenter\Notification;

class Post extends \NotificationCenter\Notification
{
    public function init()
    {
        add_action('save_post', array($this, 'updatePostNotification'), 10, 3);
        add_action('before_delete_post', array($this, 'deletePostNotificaitons'));
    }

    /**
     * Delete all notifications related to the post
     * @param  int $postId The post id that is being deleted.
     * @return void
     */
    public function deletePostNotificaitons($postId)
    {
        global $post_type, $wpdb;

        if (! \NotificationCenter\App::isActivated($post_type)) {
            return;
        }

        $wpdb->delete($wpdb->prefix . 'notification_objects', array('post_id' => $postId));
    }

    /**
     * Sends a notification to post followers when a post is saved
     * @param  int $postId Post ID
     * @return void
     */
    public function updatePostNotification($postId, $post, $update)
    {
        // Bail if post is either: not activated, autosave function, revision
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
