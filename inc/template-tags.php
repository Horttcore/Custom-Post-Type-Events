<?php

if (! function_exists('theEventDate')) {
    /**
     * Print event date
     *
     * @param  string  $format  Date format
     * @param  string  $before  Before
     * @param  string  $after  After
     * @return string Event date string
     **/
    function theEventDate($format = false, $before = '', $after = '')
    {
        $format = ($format !== false) ? $format : get_option('date_format');
        echo $before.getEventDate(get_the_ID(), $format).$after;
    }
}

if (! function_exists('theEventTime')) {
    /**
     * Print event time
     *
     * @param  string  $format  Time format
     * @param  string  $before  Before
     * @param  string  $after  After
     * @return void
     **/
    function theEventTime($format = 'H:i\h', $before = '', $after = '')
    {
        $event = getEventInfo(get_the_ID());
        $format = apply_filters('event-time-format', $format, get_the_ID(), $event);
        echo $before.getEventTime(get_the_ID(), $format).$after;
    }
}

if (! function_exists('theEventDatetime')) {
    /**
     * Print event date time
     *
     * @param  int  $postId  Post ID
     * @param  string  $field  Location field
     * @return str/array Location
     *
     * @author Ralf Hortt
     **/
    function theEventDatetime(?string $format = null, string $before = '', string $after = ''): void
    {
        if (! hasEventDate(get_the_ID())) {
            return;
        }

        $date = getEventDatetime(get_the_ID(), $format ?? get_option('date_format'));

        if (! $date) {
            return;
        }

        echo $before.$date.$after;
    }
}
