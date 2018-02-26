NotificationCenter = NotificationCenter || {};
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
    Dropdown.prototype.changeStatus = function(notificationIds) {
        return $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action : 'change_status',
                notificationIds : notificationIds
            }
        });
    };

    /**
     * Load more notifications
     */
    Dropdown.prototype.loadMore = function($target) {
        offset = $('.notification-center__item', $target).length;

        return $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action : 'load_more',
                offset : offset
            },
            beforeSend: function() {
                $target.append('<li class="notification-center__loading loading-wrapper"><div class="loading"><div></div><div></div><div></div><div></div></div></li>');
            },
            complete: function() {
                $('.notification-center__loading', $target).remove();
            },
            success: function(response) {
                if (response.length === 0) {
                    offset = null;
                } else {
                    $target.append(response);
                }
            },
            error: function(error) {
                offset = null;
            },
        });
    };

    /**
     * Handle events
     * @return {void}
     */
    Dropdown.prototype.handleEvents = function () {
        $('.notification-center__list').bind('scroll', function(e) {
            $target = $(e.currentTarget);
            if ($target.scrollTop() + $target.innerHeight() >= $target[0].scrollHeight
                && $target.find('.notification-center__loading').length === 0
                && offset !== null) {
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

            // Redirect to target url
            setTimeout(function() {
                window.location.replace(href);
            }, 60);

        }.bind(this));
    };

    return new Dropdown();

})(jQuery);
