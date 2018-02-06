NotificationCenter = NotificationCenter || {};
NotificationCenter.Notifications = NotificationCenter.Notifications || {};

NotificationCenter.Notifications.Dropdown = (function ($) {

    var unseenVal = $('.notification-center__toggle').attr('data-unseen');
    var $unseenTarget = $('.notification-center__toggle');
    console.log(unseenVal);

    function Dropdown() {
        this.handleEvents();
    }

    /**
     * Delete file
     * @return {void}
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
        $(document).on('click', '.notification-center__toggle', function (e) {
            e.preventDefault();
            $('.notification-center__dropdown').toggle();
        }.bind(this));

        $(document).on('click', '.notification-center__item--unseen a', function (e) {
            var notificationId = $(e.target).closest('.notification-center__item--unseen').attr('data-notification-id');
            unseenVal = unseenVal - 1;
            $unseenTarget.attr('data-unseen', unseenVal);
            $(e.target).closest('.notification-center__item--unseen').removeClass('notification-center__item--unseen');
            this.changeStatus(notificationId);
        }.bind(this));

        $(document).click(function(e) {
            var $target = $(e.target);
            if (!$target.is('.notification-center') && !$target.parents().is('.notification-center')) {
                $('.notification-center__dropdown').hide() ;
            }
        });
    };

    return new Dropdown();

})(jQuery);

var NotificationCenter = {};
