@foreach($notifications as $notification)
<li class="notification-center__item {{ !$notification->status ? 'notification-center__item--unseen' : '' }}"  data-notification-id="{{ $notification->ID }}">
	<a href="{{ \NotificationCenter\Helper\Message::notificationUrl($notification->entity_type, $notification->entity_id) }}">
		<div class="notification-center__message">
			{!! \NotificationCenter\Helper\Message::buildMessage($notification->entity_type, $notification->entity_id, $notification->sender_id) !!}
		</div>
		<div class="notification-center__time">
			<i class="pricon pricon-clock"></i> {{ human_time_diff(strtotime($notification->created), current_time('timestamp')) . ' ' . __('ago', 'notification-center') }}
		</div>
	</a>
</li>
@endforeach
