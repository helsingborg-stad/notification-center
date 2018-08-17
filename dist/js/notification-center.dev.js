var NotificationCenter = NotificationCenter || {};
NotificationCenter.Notifications = NotificationCenter.Notifications || {};

NotificationCenter.Notifications.Dropdown = (function ($) {

    var unseenVal = $('.notification-toggle').attr('data-unseen');
    var $unseenTarget = $('.notification-toggle');
    var offset = 0;

    function Dropdown() {
        this.handleEvents();
    }

    /**
     * Change notification status to "seen"
     */
    Dropdown.prototype.changeStatus = function (notificationIds) {
        return $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'change_status',
                notificationIds: notificationIds
            }
        });
    };

    /**
     * Mark all notifications as read
     */
    Dropdown.prototype.readAll = function () {
        return $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'read_all',
            }
        });
    };

    /**
     * Load more notifications
     */
    Dropdown.prototype.loadMore = function ($target) {
        offset = $('.notification-center__item', $target).length;

        return $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'load_more',
                offset: offset
            },
            beforeSend: function () {
                $target.append('<li class="notification-center__loading loading-wrapper"><div class="loading"><div></div><div></div><div></div><div></div></div></li>');
            },
            complete: function () {
                $('.notification-center__loading', $target).remove();
            },
            success: function (response) {
                if (response.length === 0) {
                    offset = null;
                } else {
                    $target.append(response);
                }
            },
            error: function (error) {
                offset = null;
            },
        });
    };

    /**
     * Handle events
     * @return {void}
     */
    Dropdown.prototype.handleEvents = function () {
        if (unseenVal > 0) {
            $('.notification-toggle').prepend('<style class="unseenNumbers">.notification-toggle__icon::after{content: "' + unseenVal + '" !important;}</style>');
        }

        $('.notification-center__list').bind('scroll', function (e) {
            var $target = $(e.currentTarget);
            if ($target.scrollTop() + $target.innerHeight() >= $target[0].scrollHeight && $target.find('.notification-center__loading').length === 0 && offset !== null) {
                this.loadMore($target);
            }
        }.bind(this));

        $(document).on('click', '.notification-center__item--unseen', function (e) {
            e.preventDefault();

            var href = $(e.target).closest('a').attr('href');
            var notificationId = $(e.target).closest('.notification-center__item--unseen').attr('data-notification-id');

            this.changeStatus(notificationId);

            // Reduce number of "unseen"
            unseenVal = unseenVal - 1;
            $unseenTarget.attr('data-unseen', unseenVal);
            $(e.target).closest('.notification-center__item--unseen').removeClass('notification-center__item--unseen');

            // Notification number
            $('.unseenNumbers').detach();
            $('.notification-toggle').prepend('<style class="unseenNumbers">.notification-toggle__icon::after{content: "' + unseenVal + '" !important;}</style>');
            if (unseenVal <= 0) {
                $('.unseenNumbers').detach();
            }

            // Redirect to target url
            setTimeout(function () {
                window.location.replace(href);
            }, 60);
        }.bind(this));

        $(document).on('click', '.js-read-all', function (e) {
            e.preventDefault();
            $unseenTarget.attr('data-unseen', 0);
            $('.unseenNumbers').detach();
            $('.notification-center__item--unseen').removeClass('notification-center__item--unseen');
            this.readAll();
        }.bind(this));
    };

    return new Dropdown();

})(jQuery);

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

            var isArchive = $target.data('isArchive');
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
                this.followPostType($target.data('postType'));
            } else {
                this.followPost($target.data('postId'));
            }
        }.bind(this));
    };

    return new Follow();

})(jQuery);

var NotificationCenter = {};
