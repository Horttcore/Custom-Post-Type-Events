<?php

if (! function_exists('theEventDate')) {
    /**
     * Print event date
     *
     * @param  string  $format Date format
     * @param  string  $before Before
     * @param  string  $after After
     * @return string Event date string
     **/
    function theEventDate($format = false, $before = '', $after = '')
    {
        $format = ($format !== false) ? $format : get_option('date_format');
        echo $before . getEventDate(get_the_ID(), $format) . $after;
    }
}

if (! function_exists('theEventTime')) {
    /**
     * Print event date
     *
     * @param  string  $format Date format
     * @param  string  $before Before
     * @param  string  $after After
     * @return string Event date string
     **/
    function theEventTime($format = 'H:i\h', $before = '', $after = '')
    {
        $event = getEventInfo(get_the_ID());
        $format = apply_filters('event-time-format', $format, get_the_ID(), $event);
        echo $before . getEventTime(get_the_ID(), $format) . $after;
    }
}

if (! function_exists('theEventDatetime')) {
    /**
     * Print event date time
     *
     * @param  int  $postId Post ID
     * @param  string  $field Location field
     * @return str/array Location
     *
     * @author Ralf Hortt
     **/
    function theEventDatetime($format = false, $before = '', $after = '')
    {
        if (! hasEventDate(get_the_ID())) {
            return;
        }

        $format = ($format !== false) ? $format : get_option('date_format');
        $date = getEventDatetime(get_the_ID(), $format);

        if (! $date) {
            return '';
        }

        echo $before . $date . $after;
    }
}

if (!function_exists('convertStylesToCustomProperty')) {
    function convertStylesToCustomProperty(string $string): string
    {
        if (!str_contains($string, 'var:')) {
            return '';
        }
        $string = explode("|", $string);
        $string[0] = str_replace("var:", "", $string[0]);
        $string = implode("--", $string);
        return "var(--wp--$string)";
    }
}
