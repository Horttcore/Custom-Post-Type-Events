<?php

use RalfHortt\CustomPostTypeEvents\Context\EventLoopContext;
use RalfHortt\CustomPostTypeEvents\Support\EventDateRange;

if (! function_exists('getEventOccurrences')) {
    /**
     * Get all occurrences for an event.
     *
     * @param  int  $postId  Post ID
     * @return array<int, array{id: string, range: string}>
     **/
    function getEventOccurrences(int $postId): array
    {
        $occurrences = get_post_meta($postId, 'eventOccurrences', true);

        return is_array($occurrences) ? $occurrences : [];
    }
}

if (! function_exists('eventInfoFromRange')) {
    /**
     * @return array{
     *     dateTimeStart: string,
     *     dateTimeEnd: string,
     *     start: int,
     *     end: int,
     *     isMultiDay: bool,
     *     isAllDay: bool
     * }
     **/
    function eventInfoFromRange(string $range, int $postId): array
    {
        $parsed = EventDateRange::parse($range);

        if ($parsed === null) {
            return [
                'isMultiDay' => false,
                'isAllDay' => false,
                'start' => 0,
                'end' => 0,
                'dateTimeStart' => '',
                'dateTimeEnd' => '',
            ];
        }

        $args = [
            'dateTimeStart' => $parsed['start'],
            'dateTimeEnd' => $parsed['end'],
            'start' => $parsed['startTs'],
            'end' => $parsed['endTs'],
            'isMultiDay' => EventDateRange::isMultiDay($parsed),
            'isAllDay' => EventDateRange::isAllDay($parsed),
        ];

        $explicitAllDay = get_post_meta($postId, 'eventIsAllDay', true);

        if ($explicitAllDay) {
            $args['isAllDay'] = true;
        }

        return $args;
    }
}

if (! function_exists('resolveEventRange')) {
    function resolveEventRange(int $postId, ?string $occurrenceKey = null): string
    {
        $context = EventLoopContext::get();

        if (
            $context
            && (int) $context['post_id'] === $postId
            && ($occurrenceKey === null || $context['occurrence_key'] === $occurrenceKey)
        ) {
            return (string) $context['range'];
        }

        if ($occurrenceKey !== null) {
            foreach (getEventOccurrences($postId) as $occurrence) {
                if (($occurrence['id'] ?? null) === $occurrenceKey) {
                    return (string) ($occurrence['range'] ?? '');
                }
            }
        }

        $primaryRange = (string) get_post_meta($postId, 'eventPrimaryRange', true);

        if ($primaryRange !== '') {
            return $primaryRange;
        }

        $start = (string) get_post_meta($postId, 'eventStart', true);
        $end = (string) get_post_meta($postId, 'eventEnd', true);

        if ($start !== '' && $end !== '') {
            return EventDateRange::format($start, $end);
        }

        return '';
    }
}

if (! function_exists('getEventInfo')) {
    /**
     * Get event information
     *
     * @param  int|false  $postId  Post ID
     * @param  string|null  $occurrenceKey  Occurrence key
     * @return array{
     *     dateTimeStart: string,
     *     dateTimeEnd: string,
     *     start: int,
     *     end: int,
     *     isMultiDay: bool,
     *     isAllDay: bool
     * } Event information array
     *
     * @author Ralf Hortt
     **/
    function getEventInfo(int|false $postId = false, ?string $occurrenceKey = null): array
    {
        $postId = ($postId !== false) ? $postId : get_the_ID();
        $range = resolveEventRange($postId, $occurrenceKey);

        if ($range === '') {
            return [
                'isMultiDay' => false,
                'isAllDay' => false,
                'start' => 0,
                'end' => 0,
                'dateTimeStart' => '',
                'dateTimeEnd' => '',
            ];
        }

        return eventInfoFromRange($range, $postId);
    }
}

if (! function_exists('hasEventDate')) {
    /**
     * Check if event has a date
     *
     * @param  int|false  $postId  Post ID
     * @return bool Whether event has a date
     **/
    function hasEventDate(int|false $postId = false): bool
    {
        $event = getEventInfo($postId);

        if (! $event['start']) {
            return false;
        }

        return true;
    }
}

if (! function_exists('getEventDate')) {
    /**
     * Get event date string
     *
     * @param  int|false  $postId  Post ID
     * @param  string|false  $format  Date format
     * @return string Formatted date string
     **/
    function getEventDate(int|false $postId = false, string|false $format = false): string
    {
        $postId = ($postId !== false) ? $postId : get_the_ID();
        $format = ($format !== false) ? $format : get_option('date_format');
        $event = getEventInfo($postId);

        if (! $event['start']) {
            return '';
        }

        $output = date_i18n($format, $event['start']);

        if (eventIsMultiDay($postId)) {
            $output .= apply_filters('event-date-separator', ' - ', $postId, $event);
            $output .= date_i18n($format, $event['end']);
        }

        return $output;
    }
}

if (! function_exists('getEventTime')) {
    /**
     * Get event time string
     *
     * @param  int  $postId  Post ID
     * @param  string  $format  Time format
     * @return string Formatted time string
     **/
    function getEventTime(int $postId = 0, string $format = 'H:i\h'): string
    {
        $postId = $postId ? $postId : get_the_ID();
        $event = getEventInfo($postId);

        if (! $event['start'] || $event['isAllDay']) {
            return '';
        }

        $output = date_i18n(
            apply_filters('event-time-format', $format, $postId, $event),
            $event['start']
        );

        if (date_i18n('H:i:s', $event['end']) != '23:59:59' && date_i18n('H:i:s', $event['end']) != '00:00:00') {
            $output .= apply_filters('event-time-separator', ' - ', $postId, $event);
            $output .= date_i18n(
                apply_filters('event-time-format', $format, $postId, $event),
                $event['end']
            );
        }

        return $output;
    }
}

if (! function_exists('getEventDatetime')) {
    /**
     * Get event date time string
     *
     * @param  int  $postId  Post ID
     * @param  string|null  $format  Date format
     * @return string Formatted date time string
     **/
    function getEventDatetime(int $postId = 0, ?string $format = null): string
    {
        $postId = $postId ? $postId : get_the_ID();
        $format = $format ?? get_option('date_format');
        $event = getEventInfo($postId);

        if (! $event['start']) {
            return '';
        }

        $output = getEventDate($postId, $format);
        $time = getEventTime($postId);

        if ($time) {
            $output .= apply_filters('event-date-time-separator', ' | ', $postId, $event);
            $output .= $time;
        }

        return $output;
    }
}

if (! function_exists('eventIsAllDay')) {
    /**
     * Check if event is all day
     *
     * @param  int|false  $postId  Post ID
     * @return bool Whether event is all day
     *
     * @author Ralf Hortt
     **/
    function eventIsAllDay(int|false $postId = false): bool
    {
        $postId = ($postId !== false) ? $postId : get_the_ID();
        $event = getEventInfo($postId);

        return $event['isAllDay'];
    }
}

if (! function_exists('eventIsMultiDay')) {
    /**
     * Check if event spans multiple days
     *
     * @param  int|false  $postId  Post ID
     * @return bool Whether event is multi-day
     *
     * @author Ralf Hortt
     **/
    function eventIsMultiDay(int|false $postId = false): bool
    {
        $postId = ($postId !== false) ? $postId : get_the_ID();
        $event = getEventInfo($postId);

        return $event['isMultiDay'];
    }
}

if (! function_exists('isEventInPast')) {
    /**
     * Check whether an event has ended.
     *
     * @param  int  $postId  Post ID
     * @return bool Whether the event is in the past
     *
     * @author Ralf Hortt
     **/
    function isEventInPast(int $postId = 0): bool
    {
        $postId = $postId ? $postId : get_the_ID();
        $event = getEventInfo($postId);

        if (! $event['end']) {
            return $event['start'] < time();
        }

        return $event['end'] < time();
    }
}
