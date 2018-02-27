@foreach($notifications as $notification)
<li class="notification-center__item {{ !$notification->status ? 'notification-center__item--unseen' : '' }}"  data-notification-id="{{ $notification->id_list }}">
	<a href="{{ \NotificationCenter\Helper\Message::notificationUrl($notification->entity_type, $notification->entity_id, $notification->blog_id) }}">
		<div class="notification-center__item__wrapper">
			<div class="notification-center__item__entity">
				{!! $entityTypes[$notification->entity_type]['icon'] !!}
			</div>
			<div class="notification-center__item__text">
				<div class="notification-center__message">
					{!! \NotificationCenter\Helper\Message::buildMessage($notification->entity_type, $notification->entity_id, $notification->sender_id, $notification->count, $notification->blog_id) !!}
				</div>
				<div class="notification-center__time">
					<i class="pricon pricon-clock"></i> <?php echo human_time_diff(strtotime($notification->created), current_time('timestamp')) . ' ' . __('ago', 'notification-center'); ?>
				</div>
			</div>
		</div>
	</a>
</li>
@endforeach
