NotificationCenter = NotificationCenter || {};
NotificationCenter.Notifications = NotificationCenter.Notifications || {};

NotificationCenter.Notifications.Follow = (function ($) {

    function Follow() {
        $(function() {
            this.handleEvents();
        }.bind(this));
    }

    /**
     * Follow/unfollow post
     */
    Follow.prototype.followPost = function(postId) {
        return $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action : 'follow_post',
                postId : postId
            }
        });
    };

    /**
     * Follow/unfollow post type
     */
    Follow.prototype.followPostType = function(postType) {
        return $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action : 'follow_post_type',
                postType : postType
            }
        });
    };

    /**
     * Handle events
     * @return {void}
     */
    Follow.prototype.handleEvents = function () {
        $(document).on('click', '.follow-button', function (e) {
            e.preventDefault();
            $target = $(e.currentTarget);

            var isArchive = $target.attr('data-is-archive');
            $target.toggleClass('follow-button--following');

            if ($target.hasClass('follow-button--following')) {
                $('.pricon', $target).removeClass('pricon-star-o').addClass('pricon-star');
                $('.follow-button__text', $target).text(notificationCenter.unfollow);
            } else {
                $('.pricon', $target).removeClass('pricon-star').addClass('pricon-star-o');
                $('.follow-button__text', $target).text(notificationCenter.follow);
            }
            $target.blur();

            if (isArchive) {
                this.followPostType($target.attr('data-post-type'));
            } else {
                this.followPost($target.attr('data-post-id'));
            }
        }.bind(this));
    };

    return new Follow();

})(jQuery);
