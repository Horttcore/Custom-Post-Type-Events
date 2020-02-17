<?php
if (!function_exists('theEventDate')) :
    /**
     * Print event date
     *
     * @param str $format Date format
     * @param str $before Before
     * @param str $after After
     *
     * @return str Event date string
     **/
    function theEventDate($format = false, $before = '', $after = '')
    {
        $format = (false !== $format) ? $format : get_option('date_format');
        echo $before . getEventDate(get_the_ID(), $format) . $after;
    }
endif;

if (!function_exists('theEventTime')) :
    /**
     * Print event date
     *
     * @param str $format Date format
     * @param str $before Before
     * @param str $after After
     *
     * @return str Event date string
     **/
    function theEventTime($format = 'H:i\h', $before = '', $after = '')
    {
        $event = getEventInfo(get_the_ID());
        $format = apply_filters('event-time-format', $format, get_the_ID(), $event);
        echo $before . getEventDate(get_the_ID(), $format) . $after;
    }
endif;

if (!function_exists('theEventDatetime')) :
    /**
     * Print event date time
     *
     * @param int $postId Post ID
     * @param str $field Location field
     * @return str/array Location
     * @author Ralf Hortt
     **/
    function theEventDatetime($format = false, $before = '', $after = '')
    {
        $format = (false !== $format) ? $format : get_option('date_format');
        echo $before . getEventDatetime(get_the_ID(), $format) . $after;
    }
endif;
