NotificationCenter = NotificationCenter || {};
NotificationCenter.Notifications = NotificationCenter.Notifications || {};

NotificationCenter.Notifications.Dropdown = (function ($) {

    function Dropdown() {
        this.handleEvents();
    }

    /**
     * Handle events
     * @return {void}
     */
    Dropdown.prototype.handleEvents = function () {
        $(document).on('click', '.notification-center__toggle', function (e) {
            e.preventDefault();
            $('.notification-center__dropdown').toggle();
        }.bind(this));

        $(document).click(function(e) {
            var target = e.target;
            if (!$(target).is('.notification-center') && !$(target).parents().is('.notification-center')) {
                $('.notification-center__dropdown').hide() ;
            }
        });
    };

    return new Dropdown();

})(jQuery);

var NotificationCenter = {};
