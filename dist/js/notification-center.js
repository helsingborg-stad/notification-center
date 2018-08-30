var NotificationCenter = NotificationCenter || {};
NotificationCenter.Notifications = NotificationCenter.Notifications || {};

NotificationCenter.Notifications.Dropdown = (function ($) {

    var $unseenTarget = $('.notification-toggle__icon'),
        unseenVal = $unseenTarget.attr('data-unseen'),
        offset = 0;

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

            // Redirect to target url
            setTimeout(function () {
                window.location.replace(href);
            }, 60);
        }.bind(this));

        $(document).on('click', '.js-read-all', function (e) {
            e.preventDefault();
            $unseenTarget.attr('data-unseen', 0);
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

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRyb3Bkb3duLmpzIiwiZm9sbG93LmpzIiwibm90aWZpY2F0aW9uLWNlbnRlci5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2xIQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUN2RUE7QUFDQSIsImZpbGUiOiJub3RpZmljYXRpb24tY2VudGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsidmFyIE5vdGlmaWNhdGlvbkNlbnRlciA9IE5vdGlmaWNhdGlvbkNlbnRlciB8fCB7fTtcbk5vdGlmaWNhdGlvbkNlbnRlci5Ob3RpZmljYXRpb25zID0gTm90aWZpY2F0aW9uQ2VudGVyLk5vdGlmaWNhdGlvbnMgfHwge307XG5cbk5vdGlmaWNhdGlvbkNlbnRlci5Ob3RpZmljYXRpb25zLkRyb3Bkb3duID0gKGZ1bmN0aW9uICgkKSB7XG5cbiAgICB2YXIgJHVuc2VlblRhcmdldCA9ICQoJy5ub3RpZmljYXRpb24tdG9nZ2xlX19pY29uJyksXG4gICAgICAgIHVuc2VlblZhbCA9ICR1bnNlZW5UYXJnZXQuYXR0cignZGF0YS11bnNlZW4nKSxcbiAgICAgICAgb2Zmc2V0ID0gMDtcblxuICAgIGZ1bmN0aW9uIERyb3Bkb3duKCkge1xuICAgICAgICB0aGlzLmhhbmRsZUV2ZW50cygpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENoYW5nZSBub3RpZmljYXRpb24gc3RhdHVzIHRvIFwic2VlblwiXG4gICAgICovXG4gICAgRHJvcGRvd24ucHJvdG90eXBlLmNoYW5nZVN0YXR1cyA9IGZ1bmN0aW9uIChub3RpZmljYXRpb25JZHMpIHtcbiAgICAgICAgcmV0dXJuICQuYWpheCh7XG4gICAgICAgICAgICB1cmw6IGFqYXh1cmwsXG4gICAgICAgICAgICB0eXBlOiAncG9zdCcsXG4gICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgICAgYWN0aW9uOiAnY2hhbmdlX3N0YXR1cycsXG4gICAgICAgICAgICAgICAgbm90aWZpY2F0aW9uSWRzOiBub3RpZmljYXRpb25JZHNcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIE1hcmsgYWxsIG5vdGlmaWNhdGlvbnMgYXMgcmVhZFxuICAgICAqL1xuICAgIERyb3Bkb3duLnByb3RvdHlwZS5yZWFkQWxsID0gZnVuY3Rpb24gKCkge1xuICAgICAgICByZXR1cm4gJC5hamF4KHtcbiAgICAgICAgICAgIHVybDogYWpheHVybCxcbiAgICAgICAgICAgIHR5cGU6ICdwb3N0JyxcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICBhY3Rpb246ICdyZWFkX2FsbCcsXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBMb2FkIG1vcmUgbm90aWZpY2F0aW9uc1xuICAgICAqL1xuICAgIERyb3Bkb3duLnByb3RvdHlwZS5sb2FkTW9yZSA9IGZ1bmN0aW9uICgkdGFyZ2V0KSB7XG4gICAgICAgIG9mZnNldCA9ICQoJy5ub3RpZmljYXRpb24tY2VudGVyX19pdGVtJywgJHRhcmdldCkubGVuZ3RoO1xuXG4gICAgICAgIHJldHVybiAkLmFqYXgoe1xuICAgICAgICAgICAgdXJsOiBhamF4dXJsLFxuICAgICAgICAgICAgdHlwZTogJ3Bvc3QnLFxuICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgIGFjdGlvbjogJ2xvYWRfbW9yZScsXG4gICAgICAgICAgICAgICAgb2Zmc2V0OiBvZmZzZXRcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBiZWZvcmVTZW5kOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgJHRhcmdldC5hcHBlbmQoJzxsaSBjbGFzcz1cIm5vdGlmaWNhdGlvbi1jZW50ZXJfX2xvYWRpbmcgbG9hZGluZy13cmFwcGVyXCI+PGRpdiBjbGFzcz1cImxvYWRpbmdcIj48ZGl2PjwvZGl2PjxkaXY+PC9kaXY+PGRpdj48L2Rpdj48ZGl2PjwvZGl2PjwvZGl2PjwvbGk+Jyk7XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgY29tcGxldGU6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAkKCcubm90aWZpY2F0aW9uLWNlbnRlcl9fbG9hZGluZycsICR0YXJnZXQpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChyZXNwb25zZSkge1xuICAgICAgICAgICAgICAgIGlmIChyZXNwb25zZS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICAgICAgb2Zmc2V0ID0gbnVsbDtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAkdGFyZ2V0LmFwcGVuZChyZXNwb25zZSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbiAoZXJyb3IpIHtcbiAgICAgICAgICAgICAgICBvZmZzZXQgPSBudWxsO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgfSk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIEhhbmRsZSBldmVudHNcbiAgICAgKiBAcmV0dXJuIHt2b2lkfVxuICAgICAqL1xuICAgIERyb3Bkb3duLnByb3RvdHlwZS5oYW5kbGVFdmVudHMgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICQoJy5ub3RpZmljYXRpb24tY2VudGVyX19saXN0JykuYmluZCgnc2Nyb2xsJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIHZhciAkdGFyZ2V0ID0gJChlLmN1cnJlbnRUYXJnZXQpO1xuICAgICAgICAgICAgaWYgKCR0YXJnZXQuc2Nyb2xsVG9wKCkgKyAkdGFyZ2V0LmlubmVySGVpZ2h0KCkgPj0gJHRhcmdldFswXS5zY3JvbGxIZWlnaHQgJiYgJHRhcmdldC5maW5kKCcubm90aWZpY2F0aW9uLWNlbnRlcl9fbG9hZGluZycpLmxlbmd0aCA9PT0gMCAmJiBvZmZzZXQgIT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmxvYWRNb3JlKCR0YXJnZXQpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LmJpbmQodGhpcykpO1xuXG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcubm90aWZpY2F0aW9uLWNlbnRlcl9faXRlbS0tdW5zZWVuJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAgICAgdmFyIGhyZWYgPSAkKGUudGFyZ2V0KS5jbG9zZXN0KCdhJykuYXR0cignaHJlZicpO1xuICAgICAgICAgICAgdmFyIG5vdGlmaWNhdGlvbklkID0gJChlLnRhcmdldCkuY2xvc2VzdCgnLm5vdGlmaWNhdGlvbi1jZW50ZXJfX2l0ZW0tLXVuc2VlbicpLmF0dHIoJ2RhdGEtbm90aWZpY2F0aW9uLWlkJyk7XG5cbiAgICAgICAgICAgIHRoaXMuY2hhbmdlU3RhdHVzKG5vdGlmaWNhdGlvbklkKTtcblxuICAgICAgICAgICAgLy8gUmVkdWNlIG51bWJlciBvZiBcInVuc2VlblwiXG4gICAgICAgICAgICB1bnNlZW5WYWwgPSB1bnNlZW5WYWwgLSAxO1xuICAgICAgICAgICAgJHVuc2VlblRhcmdldC5hdHRyKCdkYXRhLXVuc2VlbicsIHVuc2VlblZhbCk7XG4gICAgICAgICAgICAkKGUudGFyZ2V0KS5jbG9zZXN0KCcubm90aWZpY2F0aW9uLWNlbnRlcl9faXRlbS0tdW5zZWVuJykucmVtb3ZlQ2xhc3MoJ25vdGlmaWNhdGlvbi1jZW50ZXJfX2l0ZW0tLXVuc2VlbicpO1xuXG4gICAgICAgICAgICAvLyBSZWRpcmVjdCB0byB0YXJnZXQgdXJsXG4gICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24ucmVwbGFjZShocmVmKTtcbiAgICAgICAgICAgIH0sIDYwKTtcbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcblxuICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnLmpzLXJlYWQtYWxsJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICR1bnNlZW5UYXJnZXQuYXR0cignZGF0YS11bnNlZW4nLCAwKTtcbiAgICAgICAgICAgICQoJy5ub3RpZmljYXRpb24tY2VudGVyX19pdGVtLS11bnNlZW4nKS5yZW1vdmVDbGFzcygnbm90aWZpY2F0aW9uLWNlbnRlcl9faXRlbS0tdW5zZWVuJyk7XG4gICAgICAgICAgICB0aGlzLnJlYWRBbGwoKTtcbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcbiAgICB9O1xuXG4gICAgcmV0dXJuIG5ldyBEcm9wZG93bigpO1xuXG59KShqUXVlcnkpO1xuIiwiTm90aWZpY2F0aW9uQ2VudGVyID0gTm90aWZpY2F0aW9uQ2VudGVyIHx8IHt9O1xuTm90aWZpY2F0aW9uQ2VudGVyLk5vdGlmaWNhdGlvbnMgPSBOb3RpZmljYXRpb25DZW50ZXIuTm90aWZpY2F0aW9ucyB8fCB7fTtcblxuTm90aWZpY2F0aW9uQ2VudGVyLk5vdGlmaWNhdGlvbnMuRm9sbG93ID0gKGZ1bmN0aW9uICgkKSB7XG5cbiAgICBmdW5jdGlvbiBGb2xsb3coKSB7XG4gICAgICAgICQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB0aGlzLmhhbmRsZUV2ZW50cygpO1xuICAgICAgICB9LmJpbmQodGhpcykpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEZvbGxvdy91bmZvbGxvdyBwb3N0XG4gICAgICovXG4gICAgRm9sbG93LnByb3RvdHlwZS5mb2xsb3dQb3N0ID0gZnVuY3Rpb24ocG9zdElkKSB7XG4gICAgICAgIHJldHVybiAkLmFqYXgoe1xuICAgICAgICAgICAgdXJsOiBhamF4dXJsLFxuICAgICAgICAgICAgdHlwZTogJ3Bvc3QnLFxuICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgIGFjdGlvbiA6ICdmb2xsb3dfcG9zdCcsXG4gICAgICAgICAgICAgICAgcG9zdElkIDogcG9zdElkXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBGb2xsb3cvdW5mb2xsb3cgcG9zdCB0eXBlXG4gICAgICovXG4gICAgRm9sbG93LnByb3RvdHlwZS5mb2xsb3dQb3N0VHlwZSA9IGZ1bmN0aW9uKHBvc3RUeXBlKSB7XG4gICAgICAgIHJldHVybiAkLmFqYXgoe1xuICAgICAgICAgICAgdXJsOiBhamF4dXJsLFxuICAgICAgICAgICAgdHlwZTogJ3Bvc3QnLFxuICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgIGFjdGlvbiA6ICdmb2xsb3dfcG9zdF90eXBlJyxcbiAgICAgICAgICAgICAgICBwb3N0VHlwZSA6IHBvc3RUeXBlXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBIYW5kbGUgZXZlbnRzXG4gICAgICogQHJldHVybiB7dm9pZH1cbiAgICAgKi9cbiAgICBGb2xsb3cucHJvdG90eXBlLmhhbmRsZUV2ZW50cyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5mb2xsb3ctYnV0dG9uJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICR0YXJnZXQgPSAkKGUuY3VycmVudFRhcmdldCk7XG5cbiAgICAgICAgICAgIHZhciBpc0FyY2hpdmUgPSAkdGFyZ2V0LmRhdGEoJ2lzQXJjaGl2ZScpO1xuICAgICAgICAgICAgJHRhcmdldC50b2dnbGVDbGFzcygnZm9sbG93LWJ1dHRvbi0tZm9sbG93aW5nJyk7XG5cbiAgICAgICAgICAgIGlmICgkdGFyZ2V0Lmhhc0NsYXNzKCdmb2xsb3ctYnV0dG9uLS1mb2xsb3dpbmcnKSkge1xuICAgICAgICAgICAgICAgICQoJy5wcmljb24nLCAkdGFyZ2V0KS5yZW1vdmVDbGFzcygncHJpY29uLXN0YXItbycpLmFkZENsYXNzKCdwcmljb24tc3RhcicpO1xuICAgICAgICAgICAgICAgICQoJy5mb2xsb3ctYnV0dG9uX190ZXh0JywgJHRhcmdldCkudGV4dChub3RpZmljYXRpb25DZW50ZXIudW5mb2xsb3cpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKCcucHJpY29uJywgJHRhcmdldCkucmVtb3ZlQ2xhc3MoJ3ByaWNvbi1zdGFyJykuYWRkQ2xhc3MoJ3ByaWNvbi1zdGFyLW8nKTtcbiAgICAgICAgICAgICAgICAkKCcuZm9sbG93LWJ1dHRvbl9fdGV4dCcsICR0YXJnZXQpLnRleHQobm90aWZpY2F0aW9uQ2VudGVyLmZvbGxvdyk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICAkdGFyZ2V0LmJsdXIoKTtcblxuICAgICAgICAgICAgaWYgKGlzQXJjaGl2ZSkge1xuICAgICAgICAgICAgICAgIHRoaXMuZm9sbG93UG9zdFR5cGUoJHRhcmdldC5kYXRhKCdwb3N0VHlwZScpKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgdGhpcy5mb2xsb3dQb3N0KCR0YXJnZXQuZGF0YSgncG9zdElkJykpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LmJpbmQodGhpcykpO1xuICAgIH07XG5cbiAgICByZXR1cm4gbmV3IEZvbGxvdygpO1xuXG59KShqUXVlcnkpO1xuIiwidmFyIE5vdGlmaWNhdGlvbkNlbnRlciA9IHt9O1xuIl19
