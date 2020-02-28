<?php

if (!function_exists('getAuthTimezone')) {
    function getRequestTimezone() {
        $timezone = request()->input('timezone');
        if ($timezone) {
            return $timezone;
        }
        $user = auth()->user();
        if ($user) {
            return $user->getTimezone();
        }
        return 'UTC';
    }
}


if (! function_exists('times')) {
    /**
     * Pass a callback into a collection over set times
     * @param int $times
     * @param callable $callback
     * @return collection
     */
    function times(int $times, callable $callback)
    {
        $collection = collect([]);
        for ($i=0; $i < $times; $i++) {
            $collection->push($callback());
        }

        return $collection;
    }
}

if (! function_exists('toUTC')) {
    function toUTC(string $date, string $timezone, string $format = 'Y-m-d H:i:s'): \Carbon\Carbon
    {
        $dateObj = \Carbon\Carbon::createFromFormat($format, $date, $timezone);

        return $dateObj->setTimezone('UTC');
    }
}