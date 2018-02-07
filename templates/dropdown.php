<a href="#" class="notification-toggle" data-dropdown=".notification-center" data-unseen="<?php echo $unseen; ?>">
	<?php echo $toggleIcon; ?>
</a>
<ul class="dropdown-menu notification-center dropdown-menu-arrow dropdown-menu-arrow-right">
	<li class="notification-center__header">
		<?php _e('Notifications', 'notification-center'); ?>
	</li>
	<?php if (!empty($notifications)): ?>
        <ul class="notification-center__list">
        <?php foreach ($notifications as $key => $notification): ?>
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
		<?php endforeach; ?>
        </ul>
    <?php else: ?>
		<li class="notification-center__empty">
			<i class="pricon pricon-lg pricon-bell"></i>
			<p><?php _e('You don\'t have any notifications', 'notification-center'); ?></p>
		</li>
	<?php endif; ?>
</ul>
