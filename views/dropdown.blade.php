<a href="#" class="notification-toggle" data-dropdown=".notification-center" data-unseen="{{ $unseen }}">
	{!! $toggleIcon !!}
</a>
<ul class="dropdown-menu notification-center dropdown-menu-arrow dropdown-menu-arrow-right">
	<li class="notification-center__header">
		<?php _e('Notifications', 'notification-center'); ?>
	</li>
    <ul class="notification-center__list">
		@each('partials.dropdown-item', $notifications, 'notification', 'partials.dropdown-empty')
    </ul>
</ul>
