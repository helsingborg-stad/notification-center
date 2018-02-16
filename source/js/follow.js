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
    Follow.prototype.follow = function(postId) {
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
     * Handle events
     * @return {void}
     */
    Follow.prototype.handleEvents = function () {
        $(document).on('click', '.follow-button', function (e) {
            e.preventDefault();
            $target = $(e.currentTarget);

            var postId = $target.attr('data-post-id');
            $target.toggleClass('follow-button--following');

            if ($target.hasClass('follow-button--following')) {
                $('.pricon', $target).removeClass('pricon-star-o').addClass('pricon-star');
                $('.follow-button__text', $target).text(notificationCenter.unfollow);
            } else {
                $('.pricon', $target).removeClass('pricon-star').addClass('pricon-star-o');
                $('.follow-button__text', $target).text(notificationCenter.follow);
            }
            $target.blur();
            this.follow(postId);
        }.bind(this));
    };

    return new Follow();

})(jQuery);
