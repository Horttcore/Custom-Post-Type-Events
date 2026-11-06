<?php

declare(strict_types=1);

namespace RalfHortt\CustomPostTypeEvents\Support;

final class EventDateRange
{
    public static function parse(string $range): ?array
    {
        $range = trim($range);

        if ($range === '' || ! str_contains($range, '/')) {
            return null;
        }

        [$start, $end] = array_map('trim', explode('/', $range, 2));

        if (! self::isValidDateTime($start) || ! self::isValidDateTime($end)) {
            return null;
        }

        $startTs = strtotime($start);
        $endTs = strtotime($end);

        if ($startTs === false || $endTs === false || $endTs < $startTs) {
            return null;
        }

        return [
            'start' => $start,
            'end' => $end,
            'startTs' => $startTs,
            'endTs' => $endTs,
        ];
    }

    public static function format(string $start, string $end): string
    {
        return $start . '/' . $end;
    }

    public static function isValid(string $range): bool
    {
        return self::parse($range) !== null;
    }

    public static function isAllDay(array $parsed): bool
    {
        return str_ends_with($parsed['start'], '00:00:00')
            && (str_ends_with($parsed['end'], '23:59:59') || str_ends_with($parsed['end'], '00:00:00'));
    }

    public static function isMultiDay(array $parsed): bool
    {
        return date('Y-m-d', $parsed['startTs']) !== date('Y-m-d', $parsed['endTs']);
    }

    private static function isValidDateTime(string $value): bool
    {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value);
    }
}
