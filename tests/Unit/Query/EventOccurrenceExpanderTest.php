<?php

declare(strict_types=1);

use RalfHortt\CustomPostTypeEvents\Query\EventOccurrenceExpander;

it('expands posts into one row per upcoming occurrence', function (): void {
    $now = strtotime('2026-01-10 12:00:00');

    $posts = [
        (object) ['ID' => 10],
        (object) ['ID' => 20],
    ];

    $occurrencesByPost = [
        10 => [
            ['id' => 'occ_a', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
            ['id' => 'occ_b', 'range' => '2026-01-15 10:00:00/2026-01-15 12:00:00'],
        ],
        20 => [
            ['id' => 'occ_c', 'range' => '2026-01-20 10:00:00/2026-01-20 12:00:00'],
        ],
    ];

    $rows = EventOccurrenceExpander::expand($posts, $occurrencesByPost, [
        'show_past' => false,
        'now' => $now,
    ]);

    expect($rows)->toHaveCount(2)
        ->and($rows[0]['post_id'])->toBe(10)
        ->and($rows[0]['occurrence_key'])->toBe('occ_b')
        ->and($rows[1]['post_id'])->toBe(20)
        ->and($rows[1]['occurrence_key'])->toBe('occ_c');
});

it('sorts expanded rows by occurrence start', function (): void {
    $now = strtotime('2026-01-01 00:00:00');

    $posts = [
        (object) ['ID' => 10],
        (object) ['ID' => 20],
    ];

    $occurrencesByPost = [
        10 => [
            ['id' => 'later', 'range' => '2026-03-01 10:00:00/2026-03-01 12:00:00'],
        ],
        20 => [
            ['id' => 'sooner', 'range' => '2026-02-01 10:00:00/2026-02-01 12:00:00'],
        ],
    ];

    $rows = EventOccurrenceExpander::expand($posts, $occurrencesByPost, [
        'show_past' => false,
        'now' => $now,
        'order' => 'asc',
    ]);

    expect($rows[0]['occurrence_key'])->toBe('sooner')
        ->and($rows[1]['occurrence_key'])->toBe('later');
});

it('paginates expanded occurrence rows', function (): void {
    $now = strtotime('2026-01-01 00:00:00');

    $posts = [(object) ['ID' => 10]];

    $occurrencesByPost = [
        10 => [
            ['id' => 'a', 'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00'],
            ['id' => 'b', 'range' => '2026-01-12 10:00:00/2026-01-12 12:00:00'],
            ['id' => 'c', 'range' => '2026-01-19 10:00:00/2026-01-19 12:00:00'],
        ],
    ];

    $rows = EventOccurrenceExpander::expand($posts, $occurrencesByPost, [
        'show_past' => false,
        'now' => $now,
        'offset' => 1,
        'per_page' => 1,
    ]);

    expect($rows)->toHaveCount(1)
        ->and($rows[0]['occurrence_key'])->toBe('b');
});
