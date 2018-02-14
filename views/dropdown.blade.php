<a href="#" class="notification-toggle" data-dropdown=".notification-center" data-unseen="{{ $unseen }}">
	{!! $toggleIcon !!}
</a>
<ul class="dropdown-menu notification-center dropdown-menu-arrow dropdown-menu-arrow-right">
	<li class="notification-center__header">
		<?php _e('Notifications', 'notification-center'); ?>
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
