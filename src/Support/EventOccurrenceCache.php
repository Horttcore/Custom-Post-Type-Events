<?php

declare(strict_types=1);

namespace RalfHortt\CustomPostTypeEvents\Support;

final class EventOccurrenceCache
{
    public static function fromOccurrences(array $occurrences, int $now): array
    {
        if ($occurrences === []) {
            return [
                'eventPrimaryRange' => '',
                'eventLastEnd' => '',
            ];
        }

        $parsed = [];

        foreach ($occurrences as $occurrence) {
            $range = EventDateRange::parse((string) ($occurrence['range'] ?? ''));

            if ($range === null) {
                continue;
            }

            $parsed[] = [
                'id' => (string) ($occurrence['id'] ?? ''),
                'range' => (string) $occurrence['range'],
                'parsed' => $range,
            ];
        }

        if ($parsed === []) {
            return [
                'eventPrimaryRange' => '',
                'eventLastEnd' => '',
            ];
        }

        usort($parsed, static fn (array $a, array $b): int => $a['parsed']['startTs'] <=> $b['parsed']['startTs']);

        $primary = self::resolvePrimaryRange($parsed, $now);
        $lastEnd = '';

        foreach ($parsed as $item) {
            if ($lastEnd === '' || $item['parsed']['endTs'] > strtotime($lastEnd)) {
                $lastEnd = $item['parsed']['end'];
            }
        }

        return [
            'eventPrimaryRange' => $primary,
            'eventLastEnd' => $lastEnd,
        ];
    }

    private static function resolvePrimaryRange(array $parsed, int $now): string
    {
        foreach ($parsed as $item) {
            if ($item['parsed']['endTs'] >= $now) {
                return $item['range'];
            }
        }

        return $parsed[0]['range'];
    }
}
