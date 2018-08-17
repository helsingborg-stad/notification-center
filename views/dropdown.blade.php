<a href="#" class="notification-toggle" data-dropdown=".notification-center" data-unseen="{{ $unseen }}">
    <i class="pricon pricon-bell notification-toggle__icon"></i>
</a>
<ul class="dropdown-menu notification-center dropdown-menu-arrow dropdown-menu-arrow-right">

    <li>
        <div class="grid grid-va-middle notification-center__header">
            <div class="grid-auto u-mr-auto">
                <?php _e('Notifications', 'notification-center'); ?>
            </div>
            @if($unseen > 0)
                <div class="grid-fit-content">
                    <button type="button" class="btn btn-sm js-read-all"><?php _e('Mark all as read', 'notification-center'); ?></button>
                </div>
            @endif
        </div>
    </li>

    @if(!empty($notifications))
        <ul class="notification-center__list">
            @include('partials.dropdown-items')
        </ul>
    @else
        <li class="notification-center__empty">
            <i class="pricon pricon-lg pricon-bell"></i>
            <p><?php _e('You don\'t have any notifications', 'notification-center'); ?></p>
        </li>
    @endif
</ul>
