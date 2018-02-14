<?php

namespace NotificationCenter\Helper;

use Philo\Blade\Blade;

class Display
{
	/**
	 * Return markup from a Blade template
	 * @param  string $view View name
	 * @param  array  $data View data
	 * @return string       The markup
	 */
    public static function blade($view, $data = array())
    {
        if (!file_exists(NOTIFICATIONCENTER_CACHE_DIR)) {
            mkdir(NOTIFICATIONCENTER_CACHE_DIR, 0777, true);
        }

        $blade = new Blade(NOTIFICATIONCENTER_VIEW_PATH, NOTIFICATIONCENTER_CACHE_DIR);
        return $blade->view()->make($view, $data)->render();
    }
}
