<?php

namespace NotificationCenter\Helper;

class EntityTypes
{
    public static function getEntityTypes() : array
    {
        $entityTypes = include(NOTIFICATIONCENTER_PATH . 'source/php/config/EntityTypes.php');
        $entityTypes = apply_filters('notification_center/entity_types', $entityTypes);

        return $entityTypes;
    }
}
