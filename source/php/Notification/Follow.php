<?php

namespace NotificationCenter\Notification;

class Follow extends \NotificationCenter\Notification
{
    public function init()
    {
        add_action('wp_ajax_follow_post', array($this, 'followPost'));
        add_action('Municipio/blog/post_info', array($this, 'postFollowButton'), 9, 1);
        add_filter('accessibility_items', array($this, 'pageFollowButton'), 11, 1);
        add_action('save_post', array($this, 'updatePostNotification'));
    }

    /**
     * Sends a notification to post followers when a post is saved
     * @param  int $postId Post ID
     * @return void
     */
    public function updatePostNotification($postId)
    {
        // Deny for autosave function and revision
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($postId)) {
            return;
        }
        // Entity #4 : New post update
        $followers = get_post_meta($postId, 'post_followers', true);
        if (is_array($followers) && !empty($followers)) {
            $user = wp_get_current_user();
            $this->insertNotifications(4, $postId, $followers, $user->ID);
        }
    }

    /**
     * Adds/removes follower from a post
     * @return void
     */
    public function followPost()
    {
        ignore_user_abort(true);
        $user = wp_get_current_user();

        if (empty($_POST['postId']) || !$user) {
            wp_die();
        }

        $postId     = (int)$_POST['postId'];
        $followers  = get_post_meta($postId, 'post_followers', true);

        if (is_array($followers)) {
            if (in_array($user->ID, $followers)) {
                unset($followers[$user->ID]);
            } else {
                $followers[$user->ID] = $user->ID;
            }
        } else {
            $followers = array($user->ID => $user->ID);
        }

        update_post_meta($postId, 'post_followers', $followers);

        echo('Done');
        wp_die();
    }

    /**
     * Filter for adding post info items
     * @param  array $items Default item array
     * @return array        Modified item array
     */
    public function postFollowButton($post)
    {
        if (!is_user_logged_in()) {
            return;
        }

        $user = wp_get_current_user();
        $followers = get_post_meta($post->ID, 'post_followers', true);
        $following = (is_array($followers) && in_array($user->ID, $followers)) ? true : false;

        echo '<li><a href="#" class="follow-button ' . ($following ? 'follow-button--following' : '') . ' " data-post-id="' . $post->ID . '"><i class="pricon ' . ($following ? 'pricon-star' : 'pricon-star-o') . '"></i> <span class="follow-button__text">' . ($following ? __('Following', 'notification-center') : __('Follow', 'notification-center')) . '</span></a></li>';
    }

    /**
     * Filter for adding accessibility items
     * @param  array $items Default item array
     * @return array        Modified item array
     */
    public function pageFollowButton($items)
    {
        if (!is_user_logged_in()) {
            return;
        }

        global $post;
        $user = wp_get_current_user();
        $followers = get_post_meta($post->ID, 'post_followers', true);
        $following = (is_array($followers) && in_array($user->ID, $followers)) ? true : false;

        $items[] = '<span><a href="#" class="follow-button ' . ($following ? 'follow-button--following' : '') . ' " data-post-id="' . $post->ID . '"><i class="pricon ' . ($following ? 'pricon-star' : 'pricon-star-o') . '"></i> <span class="follow-button__text">' . ($following ? __('Following', 'notification-center') : __('Follow', 'notification-center')) . '</span></a></span>';

        return $items;
    }
}
