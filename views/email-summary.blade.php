<h2><?php _e('Your latest notifications', 'customer-feedback'); ?></h2>
<h3>Helsingborgs stads intranät</h3>
<ul style="list-style-type:none;padding:0;">
    @foreach ($notifications as $notification)
        <li style="padding:15px;background:#e6e6e6;border-radius:3px;margin-bottom:15px;">
            <small style="color:#888;">
                {{ mysql2date('j F', $notification->created, true) . ', ' . mysql2date('H:i', $notification->created, true) }}
            </small>
            <p>{!! \NotificationCenter\Helper\Message::buildMessage($notification->entity_type, $notification->entity_id, $notification->sender_id) !!}</p>
            <a href="{{ \NotificationCenter\Helper\Message::notificationUrl($notification->entity_type, $notification->entity_id) }}" target="_blank" style="color: #ffffff; font-size:12px; text-decoration: none; border-radius: 3px; background-color: #a84c98; border: 7px solid #a84c98; display: inline-block;">
                <?php _e('Show', 'notification-center'); ?> {{ strtolower($entityTypes[$notification->entity_type]['label']) }}
            </a>
        </li>
    @endforeach
</ul>
<br>
<?php _e('This message was sent via', 'notification-center'); ?> <a href="{{ get_option('home') }}">{{ get_option('home') }}</a>
