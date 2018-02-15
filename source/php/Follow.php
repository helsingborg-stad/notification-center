<?php

namespace NotificationCenter;

class Follow
{
    public function __construct()
    {
        add_action('wp_ajax_follow_post', array($this, 'followPost'));
        add_action('save_post', array($this, 'addFollowerMeta'), 10, 3);
        add_action('Municipio/blog/post_info', array($this, 'postFollowButton'), 9, 1);
        add_filter('accessibility_items', array($this, 'pageFollowButton'), 11, 1);
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
            if (array_key_exists($user->ID, $followers)) {
                $followers[$user->ID] = $followers[$user->ID] ? 0 : 1;
            } else {
                $followers[$user->ID] = 1;
            }
        } else {
            $followers = array($user->ID => 1);
        }

        update_post_meta($postId, 'post_followers', $followers);

        echo('Done');
        wp_die();
    }

    /**
     * Add follower metadata when a post is created
     * @param int $postId The post ID.
     * @param post $post The post object.
     * @param bool $update Whether this is an existing post being updated or not.
     */
    public function addFollowerMeta($postId, $post, $update)
    {
        // Bail if notifications is not activated or is update
        if ($update || !\NotificationCenter\App::isActivated(get_post_type($postId))) {
            return;
        }

        add_post_meta($postId, 'post_followers', array($post->post_author => 1), true);
    }

    /**
     * Filter for adding post info items
     * @param  array $items Default item array
     * @return array        Modified item array
     */
    public function postFollowButton($post)
    {
        // Bail if user is not logged in or notifications is not activated
        if (!is_user_logged_in() || !\NotificationCenter\App::isActivated(get_post_type($post))) {
            return;
        }

        $user = wp_get_current_user();
        $followers = get_post_meta($post->ID, 'post_followers', true);
        $isFollowing = (is_array($followers) && in_array($user->ID, array_keys(array_filter($followers)))) ? 1 : 0;
        echo '<li><a href="#" class="follow-button ' . ($isFollowing ? 'follow-button--following' : '') . ' " data-post-id="' . $post->ID . '"><i class="pricon ' . ($isFollowing ? 'pricon-star' : 'pricon-star-o') . '"></i> <span class="follow-button__text">' . ($isFollowing ? __('Following', 'notification-center') : __('Follow', 'notification-center')) . '</span></a></li>';
    }

    /**
     * Filter for adding accessibility items
     * @param  array $items Default item array
     * @return array        Modified item array
     */
    public function pageFollowButton($items)
    {
        global $post;

        // Bail if user is not logged in or notifications is not activated
        if (!is_user_logged_in() || !\NotificationCenter\App::isActivated(get_post_type($post))) {
            return $items;
        }

        $user = wp_get_current_user();
        $followers = get_post_meta($post->ID, 'post_followers', true);
        $isFollowing = (is_array($followers) && in_array($user->ID, array_keys(array_filter($followers)))) ? 1 : 0;
        $items[] = '<span><a href="#" class="follow-button ' . ($isFollowing ? 'follow-button--following' : '') . ' " data-post-id="' . $post->ID . '"><i class="pricon ' . ($isFollowing ? 'pricon-star' : 'pricon-star-o') . '"></i> <span class="follow-button__text">' . ($isFollowing ? __('Following', 'notification-center') : __('Follow', 'notification-center')) . '</span></a></span>';

        return $items;
    }
}
