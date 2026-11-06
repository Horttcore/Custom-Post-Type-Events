<?php

declare(strict_types=1);

use RalfHortt\CustomPostTypeEvents\Support\EventOccurrenceCollection;

it('filters upcoming occurrences', function (): void {
    $now = strtotime('2026-01-10 12:00:00');

    $collection = new EventOccurrenceCollection([
        ['id' => 'past', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
        ['id' => 'future', 'range' => '2026-01-15 10:00:00/2026-01-15 12:00:00'],
    ]);

    $upcoming = $collection->filterUpcoming($now);

    expect($upcoming)->toHaveCount(1)
        ->and($upcoming[0]['id'])->toBe('future');
});

it('filters past occurrences', function (): void {
    $now = strtotime('2026-01-10 12:00:00');

    $collection = new EventOccurrenceCollection([
        ['id' => 'past', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
        ['id' => 'future', 'range' => '2026-01-15 10:00:00/2026-01-15 12:00:00'],
    ]);

    $past = $collection->filterPast($now);

    expect($past)->toHaveCount(1)
        ->and($past[0]['id'])->toBe('past');
});

it('sorts occurrences by start ascending', function (): void {
    $collection = new EventOccurrenceCollection([
        ['id' => 'b', 'range' => '2026-02-01 10:00:00/2026-02-01 12:00:00'],
        ['id' => 'a', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
    ]);

    $sorted = $collection->sortByStart('asc');

    expect($sorted[0]['id'])->toBe('a')
        ->and($sorted[1]['id'])->toBe('b');
});

it('finds an occurrence by key', function (): void {
    $collection = new EventOccurrenceCollection([
        ['id' => 'occ_abc', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
    ]);

    expect($collection->getByKey('occ_abc')['id'])->toBe('occ_abc')
        ->and($collection->getByKey('missing'))->toBeNull();
});
