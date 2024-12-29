<?php

namespace wnikk\FlexibleThrottle\Helpers;

class TimeHelper
{
    /**
     * Convert seconds to human readable time.
     *
     * @param int $sec
     * @return string
     */
    public static function convertTime($sec)
    {
        $timeParts = [];
        foreach (self::durations() as $tf => $duration) {
            extract(self::createTimeString($sec, $tf, $duration));
            array_push($timeParts, $timeString);
        }
        return implode(' ', array_filter($timeParts, function ($item) {
            return $item != '';
        }));
    }

    /**
     * Get the durations.
     *
     * @return array
     */
    private static function durations()
    {
        return array_reverse([
            'second' => 1,
            'minute' => $minute = 60,
            'hour' => $hour = $minute * 60,
            'day' => $day = $hour * 24,
            'week' => $day * 7,
            'month' => $day * 30,
            'year' => $day * 365
        ]);
    }

    /**
     * Create time string.
     *
     * @param int $sec
     * @param string $tf
     * @param int $duration
     * @return array
     */
    private static function createTimeString($sec, $tf, $duration)
    {
        $howManyLeft = floor($sec / $duration);
        return [
            'sec' => !$howManyLeft ? $sec : $sec - $howManyLeft * $duration,
            'timeString' => !$howManyLeft ? '' : $howManyLeft . " $tf" . ($howManyLeft > 1 ? 's' : '')
        ];
    }

    /**
     * Get the current time in seconds.
     *
     * @return int
     */
    public static function now()
    {
        return time();
    }

    /**
     * Get the current time in milliseconds.
     *
     * @return int
     */
    public static function nowMs()
    {
        return round(microtime(true) * 1000);
    }
}