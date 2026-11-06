<?php

declare(strict_types=1);

use RalfHortt\CustomPostTypeEvents\Support\EventDateRange;

it('parses a valid slash-separated range string', function (): void {
    $parsed = EventDateRange::parse('2026-01-05 10:00:00/2026-01-05 12:00:00');

    expect($parsed)->toMatchArray([
        'start' => '2026-01-05 10:00:00',
        'end' => '2026-01-05 12:00:00',
    ])
        ->and($parsed['startTs'])->toBeInt()
        ->and($parsed['endTs'])->toBeInt()
        ->and($parsed['endTs'])->toBeGreaterThan($parsed['startTs']);
});

it('returns null for an invalid range string', function (): void {
    expect(EventDateRange::parse('not-a-range'))->toBeNull();
    expect(EventDateRange::parse('2026-01-05 10:00:00'))->toBeNull();
    expect(EventDateRange::parse(''))->toBeNull();
});

it('formats a range from datetime strings', function (): void {
    expect(EventDateRange::format('2026-01-05 10:00:00', '2026-01-05 12:00:00'))
        ->toBe('2026-01-05 10:00:00/2026-01-05 12:00:00');
});

it('detects all-day ranges', function (): void {
    $parsed = EventDateRange::parse('2026-01-05 00:00:00/2026-01-05 23:59:59');

    expect($parsed)->not->toBeNull()
        ->and(EventDateRange::isAllDay($parsed))->toBeTrue();
});

it('detects multi-day ranges', function (): void {
    $parsed = EventDateRange::parse('2026-01-05 10:00:00/2026-01-07 18:00:00');

    expect($parsed)->not->toBeNull()
        ->and(EventDateRange::isMultiDay($parsed))->toBeTrue();
});

it('rejects ranges where end is before start', function (): void {
    expect(EventDateRange::isValid('2026-01-10 10:00:00/2026-01-05 12:00:00'))->toBeFalse();
});

it('accepts valid ranges', function (): void {
    expect(EventDateRange::isValid('2026-01-05 10:00:00/2026-01-05 12:00:00'))->toBeTrue();
});
