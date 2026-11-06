<?php

declare(strict_types=1);

use RalfHortt\CustomPostTypeEvents\Sync\EventOccurrenceSync;

it('normalizes valid occurrences and computes cache meta', function (): void {
    $now = strtotime('2026-01-01 00:00:00');

    $result = EventOccurrenceSync::normalize([
        ['id' => 'occ_a', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
        ['id' => 'occ_b', 'range' => '2026-02-01 10:00:00/2026-02-01 12:00:00'],
    ], $now);

    expect($result['eventOccurrences'])->toHaveCount(2)
        ->and($result['eventPrimaryRange'])->toBe('2026-01-05 10:00:00/2026-01-05 12:00:00')
        ->and($result['eventLastEnd'])->toBe('2026-02-01 12:00:00');
});

it('rejects invalid occurrence ranges', function (): void {
    expect(fn () => EventOccurrenceSync::normalize([
        ['id' => 'bad', 'range' => 'invalid'],
    ], time()))->toThrow(InvalidArgumentException::class);
});

it('merges legacy start and end into a single occurrence', function (): void {
    $result = EventOccurrenceSync::fromLegacyMeta(
        '2026-01-05 10:00:00',
        '2026-01-05 12:00:00',
        time()
    );

    expect($result['eventOccurrences'])->toHaveCount(1)
        ->and($result['eventOccurrences'][0]['range'])
        ->toBe('2026-01-05 10:00:00/2026-01-05 12:00:00');
});
