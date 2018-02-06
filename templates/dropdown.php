<div class="notification-center">
	<a href="#" class="notification-center__toggle" data-unseen="<?php echo $unseen; ?>">
		<?php echo $toggleIcon; ?>
	</a>

	<div class="notification-center__dropdown">
		<div class="notification-center__header">
			<?php _e('Notifications', 'notification-center'); ?>
		</div>
		<ul>
			<?php
			if (!empty($notifications)):
			foreach ($notifications as $key => $notification): ?>
				<li class="notification-center__item <?php echo !$notification->status ? 'notification-center__item--unseen' : ''; ?>"  data-notification-id="<?php echo $notification->ID; ?>">
					<a href="<?php echo \NotificationCenter\Notification::notificationUrl($notification->entity_type, $notification->entity_id); ?>">
						<div class="notification-center__message">
							<?php echo \NotificationCenter\Notification::buildMessage($notification->entity_type, $notification->entity_id, $notification->sender_id); ?>
						</div>
						<div class="notification-center__time">
							<i class="pricon pricon-clock"></i> <?php echo human_time_diff(strtotime($notification->created), current_time('timestamp')) . ' ' . __('sedan', 'notification-center'); ?>
						</div>
					</a>
				</li>
			<?php
			endforeach;
			endif; ?>
		</ul>
	</div>
</div>
