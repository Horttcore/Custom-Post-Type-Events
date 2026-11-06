<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use RalfHortt\CustomPostTypeEvents\Context\EventLoopContext;

beforeEach(function (): void {
    EventLoopContext::reset();
    require_once __DIR__ . '/../../../inc/functions.date.php';
    require_once __DIR__ . '/../../../inc/functions.navigation.php';
});

afterEach(function (): void {
    EventLoopContext::reset();
});

it('returns all occurrences for a post', function (): void {
    $occurrences = [
        ['id' => 'occ_a', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
        ['id' => 'occ_b', 'range' => '2026-02-01 10:00:00/2026-02-01 12:00:00'],
    ];

    Functions\expect('get_post_meta')
        ->once()
        ->with(42, 'eventOccurrences', true)
        ->andReturn($occurrences);

    expect(getEventOccurrences(42))->toBe($occurrences);
});

it('reads event info from loop context when set', function (): void {
    Functions\expect('get_post_meta')
        ->once()
        ->with(42, 'eventIsAllDay', true)
        ->andReturn('');

    EventLoopContext::set(42, 'occ_b', '2026-02-01 10:00:00/2026-02-01 12:00:00');

    $info = getEventInfo(42, 'occ_b');

    expect($info['dateTimeStart'])->toBe('2026-02-01 10:00:00')
        ->and($info['dateTimeEnd'])->toBe('2026-02-01 12:00:00');
});

it('falls back to eventPrimaryRange when no occurrence key is given', function (): void {
    Functions\expect('get_post_meta')
        ->once()
        ->with(42, 'eventPrimaryRange', true)
        ->andReturn('2026-01-05 10:00:00/2026-01-05 12:00:00');

    Functions\expect('get_post_meta')
        ->once()
        ->with(42, 'eventIsAllDay', true)
        ->andReturn('');

    $info = getEventInfo(42);

    expect($info['dateTimeStart'])->toBe('2026-01-05 10:00:00')
        ->and($info['dateTimeEnd'])->toBe('2026-01-05 12:00:00');
});

it('considers an event past when event end is before now', function (): void {
    Functions\expect('get_post_meta')
        ->once()
        ->with(42, 'eventPrimaryRange', true)
        ->andReturn('2020-01-05 10:00:00/2020-01-05 12:00:00');

    Functions\expect('get_post_meta')
        ->once()
        ->with(42, 'eventIsAllDay', true)
        ->andReturn('');

    Functions\expect('get_the_ID')->andReturn(42);

    expect(isEventInPast(42))->toBeTrue();
});

it('excludes the current post when querying the previous event', function (): void {
    Functions\expect('get_post_meta')->andReturnUsing(function (int $id, string $key, bool $single) {
        if ($id !== 10) {
            return '';
        }

        return match ($key) {
            'eventOccurrences' => [],
            'eventPrimaryRange' => '2026-06-01 10:00:00/2026-06-01 12:00:00',
            'eventIsAllDay' => '',
            default => '',
        };
    });

    Functions\expect('get_posts')
        ->once()
        ->with([
            'post_type' => 'event',
            'post__not_in' => [10],
            'orderby' => 'event-date',
            'order' => 'DESC',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => 'eventStart',
                    'value' => '2026-06-01 10:00:00',
                    'compare' => '<',
                    'type' => 'DATETIME',
                ],
            ],
        ])
        ->andReturn([]);

    expect(getPreviousEvent(10))->toBeNull();
});

it('excludes the current post when querying the next event', function (): void {
    Functions\expect('get_post_meta')->andReturnUsing(function (int $id, string $key, bool $single) {
        if ($id !== 20) {
            return '';
        }

        return match ($key) {
            'eventOccurrences' => [],
            'eventPrimaryRange' => '2026-06-01 10:00:00/2026-06-01 12:00:00',
            'eventIsAllDay' => '',
            default => '',
        };
    });

    Functions\expect('get_posts')
        ->once()
        ->with([
            'post_type' => 'event',
            'post__not_in' => [20],
            'orderby' => 'event-date',
            'order' => 'ASC',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => 'eventStart',
                    'value' => '2026-06-01 10:00:00',
                    'compare' => '>',
                    'type' => 'DATETIME',
                ],
            ],
        ])
        ->andReturn([]);

    expect(getNextEvent(20))->toBeNull();
});

it('does not query posts when the event has no start datetime', function (): void {
    Functions\expect('get_post_meta')->andReturnUsing(function (int $id, string $key, bool $single) {
        return match ($key) {
            'eventOccurrences' => [],
            'eventPrimaryRange' => '',
            'eventStart' => '',
            'eventEnd' => '',
            default => '',
        };
    });

    Functions\expect('get_posts')->never();

    expect(getPreviousEvent(5))->toBeNull()
        ->and(getNextEvent(5))->toBeNull();
});
