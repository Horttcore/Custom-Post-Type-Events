<?php

declare(strict_types=1);

use RalfHortt\CustomPostTypeEvents\Context\EventLoopContext;

it('stores and retrieves the active occurrence', function (): void {
    EventLoopContext::set(42, 'occ_abc', '2026-01-05 10:00:00/2026-01-05 12:00:00');

    $context = EventLoopContext::get();

    expect($context)->toMatchArray([
        'post_id' => 42,
        'occurrence_key' => 'occ_abc',
        'range' => '2026-01-05 10:00:00/2026-01-05 12:00:00',
    ]);
});

it('returns null when no occurrence is set', function (): void {
    EventLoopContext::reset();

    expect(EventLoopContext::get())->toBeNull();
});

it('resets the active occurrence', function (): void {
    EventLoopContext::set(1, 'occ_a', '2026-01-05 10:00:00/2026-01-05 12:00:00');
    EventLoopContext::reset();

    expect(EventLoopContext::get())->toBeNull();
});
