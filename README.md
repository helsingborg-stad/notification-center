Notification Center
==========

Plugin to give logged in users notifications when others react on comments etc.

Shortcodes
----------------
#### notification-center

> Outputs the notification list in a clickable dropdown

*Example:*

```php
do_shortcode('[notification-center]');
```

Filters
----------------

#### notification_center/activated_posttypes

> Activate notifications for selected post types

*Params:*
```
$postTypes      The default post types array
```

---

#### notification_center/entity_types

> Add custom entity types. e.g. comments, likes, updates etc.

*Params:*
```
$entityTypes      The default entity types array
```


