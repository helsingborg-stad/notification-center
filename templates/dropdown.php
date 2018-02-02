<div class="notification-center">
	<a href="#" class="notification-center__toggle">
		<?php echo $icon; ?>
	</a>

	<div class="notification-center__dropdown">
		<ul>
			<?php
			if (!empty($notifications)):
			foreach ($notifications as $key => $notification): ?>
				<li class="notification-center__item notification-center__item--read">
					<?php echo \NotificationCenter\Notification::buildMessage($notification->entity_type, $notification->entity_id, $notification->sender_id); ?>
				</li>
			<?php endforeach;
			endif; ?>
			<li class="notification-center__load-more"><p>Load more</p></li>
		</ul>
	</div>
</div>
