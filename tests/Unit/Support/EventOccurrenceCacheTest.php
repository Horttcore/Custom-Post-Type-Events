<?php

declare(strict_types=1);

use RalfHortt\CustomPostTypeEvents\Support\EventOccurrenceCache;

it('computes eventPrimaryRange as the nearest upcoming occurrence', function (): void {
    $now = strtotime('2026-01-10 12:00:00');

    $cache = EventOccurrenceCache::fromOccurrences([
        ['id' => 'occ_past', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
        ['id' => 'occ_next', 'range' => '2026-01-15 10:00:00/2026-01-15 12:00:00'],
        ['id' => 'occ_later', 'range' => '2026-02-01 10:00:00/2026-02-01 12:00:00'],
    ], $now);

    expect($cache['eventPrimaryRange'])->toBe('2026-01-15 10:00:00/2026-01-15 12:00:00');
});

it('computes eventLastEnd as the latest end across all occurrences', function (): void {
    $now = strtotime('2026-01-01 00:00:00');

    $cache = EventOccurrenceCache::fromOccurrences([
        ['id' => 'occ_a', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
        ['id' => 'occ_b', 'range' => '2026-03-20 18:00:00/2026-03-20 20:00:00'],
    ], $now);

    expect($cache['eventLastEnd'])->toBe('2026-03-20 20:00:00');
});

it('uses earliest past occurrence as primary when all are past', function (): void {
    $now = strtotime('2026-06-01 00:00:00');

    $cache = EventOccurrenceCache::fromOccurrences([
        ['id' => 'occ_a', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
        ['id' => 'occ_b', 'range' => '2026-02-01 10:00:00/2026-02-01 12:00:00'],
    ], $now);

    expect($cache['eventPrimaryRange'])->toBe('2026-01-05 10:00:00/2026-01-05 12:00:00');
});

it('returns empty cache for no occurrences', function (): void {
    $cache = EventOccurrenceCache::fromOccurrences([], time());

    expect($cache['eventPrimaryRange'])->toBe('')
        ->and($cache['eventLastEnd'])->toBe('');
});
