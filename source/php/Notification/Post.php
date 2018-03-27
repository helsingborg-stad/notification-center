<?php

namespace NotificationCenter\Notification;

class Post extends \NotificationCenter\Notification
{
    public function init()
    {
        add_action('wp_insert_post_data', array($this, 'savePostMention'), 99, 2);
        add_action('save_post', array($this, 'updatePostNotification'), 10, 3);
        add_action('before_delete_post', array($this, 'deletePostNotificaitons'));
        add_action('transition_post_status', array($this, 'newPostNotification'), 10, 3);
        add_action('Municipio/share_post/recipients', array($this, 'inviteNotification'), 10, 4);
    }

    /**
     * Notification on forum invite
     * @param  int      $postId      Post ID
     * @param  obj      $sender      Senders user object
     * @param  array    $recipients  List of recipients
     * @param  string   $shareType   [description]
     * @return void
     */
    public function inviteNotification($postId, $sender, $recipients, $shareType)
    {
        if ($shareType != 'invite') {
            return;
        }

        /** Entity #8 : New forum invitation **/
        // Collect recipients IDs
        $notifiers = array();
        if (is_array($recipients) && !empty($recipients)) {
            foreach ($recipients as $recipient) {
                $notifier = get_user_by('email', $recipient);
                if ($notifier) {
                    $notifiers[] = $notifier->ID;
                }
            }
        }

        if (!empty($notifiers)) {
            $this->insertNotifications(8, $postId, $notifiers, $sender->ID, $postId);
        }
    }

    /**
     * Adds a notification to post type followers when a new post is created
     * @param  string $new  New post status
     * @param  string $old  Old post status
     * @param  obj $post    Post Object
     * @return void
     */
    public function newPostNotification($new, $old, $post)
    {
        // On first publish
        if ($new == 'publish' && $old != 'publish' && isset($post->post_type) && \NotificationCenter\App::isActivated($post->post_type)) {

            /** Entity #5 : New post on followed post type **/
            $followers = get_option($post->post_type . '_archive_followers');
            if (!empty($followers)) {
                $followers = array_keys(array_filter($followers));
                if (is_array($followers) && !empty($followers)) {
                    $this->insertNotifications(5, $post->ID, $followers, $post->post_author, $post->ID);
                }
            }
        }
    }

    /**
     * Save @Mentions found in post content as notifications
     * @param  array $data    An array of slashed post data.
     * @param  array $postarr An array of sanitized, but otherwise unmodified post data.
     * @return array          Modified array of post data
     */
    public function savePostMention($data, $postarr)
    {
        $user = wp_get_current_user();
        // Get all user id attributes
        preg_match_all('/data-mention-id="(\d*?)"/', stripslashes($data['post_content']), $matches);

        if (isset($matches[1]) && !empty($matches[1]) && $user) {
            foreach ($matches[1] as $key => $notifier) {
                /** Entity #6 : User mention in post content **/
                $this->insertNotifications(6, $postarr['ID'], array((int) $notifier), $user->ID, $postarr['ID']);
            }

            // Replace 'data-mention-id' attribute to avoid duplicate notifications
            $data['post_content'] = preg_replace('/data-mention-id/', 'data-user-id', $data['post_content']);
        }

        return $data;
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

        $dbTable = $wpdb->prefix . 'notification_objects';
        $wpdb->delete($dbTable, array('post_id' => $postId));
    }

    /**
     * Adds a notification to post followers when a post is saved
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

        /** Entity #4 : New post update **/
        $user = wp_get_current_user();
        $followers = get_post_meta($postId, 'post_followers', true);
        $followers = array_keys(array_filter($followers));
        if (is_array($followers) && !empty($followers) && $user) {
            $this->insertNotifications(4, $postId, $followers, $user->ID, $postId);
        }
    }
}
