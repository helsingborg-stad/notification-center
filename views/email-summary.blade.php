<h2>{{ $heading }}</h2>
<table style="font-family:Helvetica,Arial,sans-serif;" cellspacing="10" cellpadding="10" border="0">
    @foreach ($notifications as $notification)
        <tr>
            <td bgcolor="#e6e6e6" style="border-radius:3px;">
                <small style="color:#888;">
                    {{ mysql2date('j F', $notification->created, true) . ', ' . mysql2date('H:i', $notification->created, true) }}
                </small>
                <p>{!! \NotificationCenter\Helper\Message::buildMessage($notification->entity_type, $notification->entity_id, $notification->sender_id, $notification->count, $notification->blog_id) !!}</p>
                <a href="{{ \NotificationCenter\Helper\Message::notificationUrl($notification->entity_type, $notification->entity_id, $notification->blog_id) }}" target="_blank" style="color: #fff; font-size:12px; text-decoration: none; border-radius: 3px; background-color: #a84c98; border: 7px solid #a84c98; display: inline-block;">
                   <span style="color:#fff"><?php _e('Show', 'notification-center'); ?> {{ strtolower($entityTypes[$notification->entity_type]['label']) }}</span>
                </a>
            </td>
        </tr>
    @endforeach
</table>
<br>
{!! $footer !!}
