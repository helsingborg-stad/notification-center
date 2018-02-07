NotificationCenter = NotificationCenter || {};
NotificationCenter.Notifications = NotificationCenter.Notifications || {};

NotificationCenter.Notifications.Dropdown = (function ($) {

    var unseenVal = $('.notification-toggle').attr('data-unseen');
    var $unseenTarget = $('.notification-toggle');

    function Dropdown() {
        this.handleEvents();
    }

    /**
     * Change notification status to "seen"
     */
    Dropdown.prototype.changeStatus = function(notificationId) {
        return $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action : 'change_status',
                notificationId : notificationId
            }
        });
    };

    /**
     * Handle events
     * @return {void}
     */
    Dropdown.prototype.handleEvents = function () {
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
            setTimeout(function(){
                window.location.replace(href);
            }, 10);

        }.bind(this));
    };

    return new Dropdown();

})(jQuery);

var NotificationCenter = {};
