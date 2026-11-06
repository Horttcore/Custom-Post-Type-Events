<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use RalfHortt\CustomPostTypeEvents\Meta\EventDate;

beforeEach(function (): void {
    Functions\expect('sanitize_text_field')
        ->andReturnUsing(static fn ($value): string => is_string($value) ? $value : (string) $value);
});

it('sanitizes event occurrences to id/range pairs and drops invalid rows', function (): void {
    $meta = new EventDate();

    expect($meta->sanitizeEventOccurrencesMeta([
        ['id' => 'occ_a', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
        ['id' => '', 'range' => '2026-01-06 10:00:00/2026-01-06 12:00:00'],
        'invalid',
    ]))->toBe([
        ['id' => 'occ_a', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
    ]);
});

it('returns empty array for non-array occurrence meta', function (): void {
    $meta = new EventDate();

    expect($meta->sanitizeEventOccurrencesMeta(null))->toBe([])
        ->and($meta->sanitizeEventOccurrencesMeta('x'))->toBe([]);
});

it('casts eventIsAllDay to bool', function (): void {
    $meta = new EventDate();

    expect($meta->sanitizeEventIsAllDayMeta(1))->toBeTrue()
        ->and($meta->sanitizeEventIsAllDayMeta(''))->toBeFalse();
});
