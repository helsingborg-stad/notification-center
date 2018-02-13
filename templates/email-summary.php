<h2><?php _e('Your latest notifications', 'customer-feedback'); ?></h2>
<h3>Helsingborgs stads intranät</h3>
<ul style="list-style-type:none;padding:0;">
    <?php
        foreach ($notifications as $notification) :
        $text = \NotificationCenter\Helper\Message::buildMessage($notification->entity_type, $notification->entity_id, $notification->sender_id);
        $url = \NotificationCenter\Helper\Message::notificationUrl($notification->entity_type, $notification->entity_id);
    ?>
        <li style="padding:15px;background:#e6e6e6;border-radius:3px;margin-bottom:15px;">
            <small style="color:#888;">
                <?php
                $date = mysql2date('j F', $notification->created, true) . ', ' . mysql2date('H:i', $notification->created, true);
                echo $date;
                ?>
            </small>
            <p><?php echo $text; ?></p>
            <a href="<?php echo $url; ?>" target="_blank" style="color: #ffffff; font-size:12px; text-decoration: none; border-radius: 3px; background-color: #a84c98; border: 7px solid #a84c98; display: inline-block;">
                <?php _e('Show', 'notification-center') ?>
                <?php echo strtolower($entityTypes[$notification->entity_type]['label']); ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
<br>
<?php _e('This message was sent via', 'notification-center'); ?> <a href="<?php echo get_option('home'); ?>"><?php echo get_option('home'); ?></a>
