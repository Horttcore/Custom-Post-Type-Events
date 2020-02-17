<?php
if (!function_exists('getEventInfo')) :
/**
 * Get event location
 *
 * @param int $postId Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function getEventInfo($postId = false)
{
    $postId = (false !== $postId) ? $postId : get_the_ID();

    $start = get_post_meta($postId, 'eventStart', true);
    $end = get_post_meta($postId, 'eventEnd', true);
    
    $args = [
        'dateTimeStart' => $start,
        'dateTimeEnd' => $end,
    ];

    if ($start) {
        $args['start'] = (int)strtotime($start);
    }

    if ($end) {
        $args['end'] = (int)strtotime($end);
    }

    if (isset($args['start']) && isset($args['end']) && date('d.m.Y', $args['start']) != date('d.m.Y', $args['end'])) {
        $args['isMultiDay'] = true;
    }

    if (isset($args['start']) && isset($args['end']) && date('H:i:s', $args['start']) == '00:00:00' && date('H:i:s', $args['end']) == '23:59:59') {
        $args['isAllDay'] = true;
    }

    return wp_parse_args($args, [
        'isMultiDay' => false,
        'isAllDay' => false,
        'start' => '',
        'end' => '',
        'dateTimeStart' => '',
        'dateTimeEnd' => '',
    ]);
}
endif;

if (!function_exists('getEventDate')) :
    /**
     * Get event date time string
     *
     * @param int $postId Post ID
     * @param str $format Date format
     *
     * @return str Formatted date time string
     **/
    function getEventDate($postId = false, $format = false)
    {
        $format = (false !== $format) ? $format : get_option('date_format');
        $event = getEventInfo($postId);

        if (!$event['start']) {
            return '';
        }

        $output = date_i18n($format, $event['start']);
        
        if (eventIsMultiDay($postId)) {
            $output .= apply_filters('event-date-seperator', ' - ', $postId, $event);
            $output .= date_i18n($format, $event['end']);
        }

        return $output;
    }
endif;

if (!function_exists('getEventTime')) :
    /**
     * Get event date time string
     *
     * @param int $postId Post ID
     * @param str $format Date format
     *
     * @return str Formatted date time string
     **/
    function getEventTime($postId = false, $format = false)
    {
        $format = (false !== $format) ? $format : get_option('date_format');
        $event = getEventInfo($postId);

        if (!$event['start']) {
            return '';
        }

        if (
            date_i18n('H:i:s', $event['start']) == '00:00:00' &&
            date_i18n('H:i:s', $event['end']) == '23:59:59'
        ) {
            return '';
        }

        $output = date_i18n(
            apply_filters('event-time-format', 'H:i\h', $postId, $event),
            $event['start']
        );
        
        if (!eventIsAllDay($postId) && date_i18n('H:i:s', $event['end']) != '23:59:59') {
            $output .= apply_filters('event-time-seperator', ' - ', $postId, $event);
            $output .= date_i18n(
                apply_filters('event-time-format', 'H:i\h', $postId, $event),
                $event['end']
            );
        }

        return $output;
    }
endif;

if (!function_exists('getEventDatetime')) :
    /**
     * Get event date time string
     *
     * @param int $postId Post ID
     * @param str $format Date format
     *
     * @return str Formatted date time string
     **/
    function getEventDatetime($postId = false, $format = false)
    {
        $format = (false !== $format) ? $format : get_option('date_format');
        $event = getEventInfo($postId);

        if (!$event['start']) {
            return '';
        }

        $output = getEventDate($postId, $format);
        
        if (!eventIsAllDay($postId)) {
            $output .= apply_filters('event-date-time-seperator', ' | ', $postId, $event);
            $output .= getEventTime($postId, $format);
        }

        return $output;
    }
endif;

if (!function_exists('eventIsAllDay')) :
    /**
     * Conditional if event time is set
     *
     * @param int $postId Post ID
     * @return bool
     * @author Ralf Hortt
     **/
    function eventIsAllDay($postId)
    {
        $event = getEventInfo($postId);
        return $event['isAllDay'];
    }
endif;

if (!function_exists('eventIsMultiDay')) :
    /**
     * Conditional if event time is set
     *
     * @param int $postId Post ID
     * @return bool
     * @author Ralf Hortt
     **/
    function eventIsMultiDay($postId)
    {
        $event = getEventInfo($postId);
        return $event['isMultiDay'];
    }
endif;
