<?php

declare(strict_types=1);

namespace RalfHortt\CustomPostTypeEvents\Sync;

use InvalidArgumentException;
use RalfHortt\CustomPostTypeEvents\Support\EventDateRange;
use RalfHortt\CustomPostTypeEvents\Support\EventOccurrenceCache;

final class EventOccurrenceSync
{
    /**
     * @param  array<int, array{id?: string, range?: string}>  $occurrences
     * @param  int  $referenceTime  Unix timestamp; used to pick primary range (next upcoming)
     * @return array{eventOccurrences: list<array{id: string, range: string}>, eventPrimaryRange: string, eventLastEnd: string}
     */
    public static function normalize(array $occurrences, int $referenceTime): array
    {
        $normalized = [];

        foreach ($occurrences as $occurrence) {
            $id = (string) ($occurrence['id'] ?? '');
            $range = (string) ($occurrence['range'] ?? '');

            if ($id === '' || ! EventDateRange::isValid($range)) {
                throw new InvalidArgumentException('Invalid event occurrence.');
            }

            $normalized[] = [
                'id' => $id,
                'range' => $range,
            ];
        }

        $cache = EventOccurrenceCache::fromOccurrences($normalized, $referenceTime);

        return [
            'eventOccurrences' => $normalized,
            'eventPrimaryRange' => $cache['eventPrimaryRange'],
            'eventLastEnd' => $cache['eventLastEnd'],
        ];
    }

    public static function fromLegacyMeta(string $start, string $end, int $referenceTime): array
    {
        $range = EventDateRange::format($start, $end);

        return self::normalize([
            ['id' => 'occ_migrated', 'range' => $range],
        ], $referenceTime);
    }
}
