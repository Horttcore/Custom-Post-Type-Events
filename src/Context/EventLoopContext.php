<?php

declare(strict_types=1);

namespace RalfHortt\CustomPostTypeEvents\Context;

final class EventLoopContext
{
    private static ?array $context = null;

    public static function set(int $postId, string $occurrenceKey, string $range): void
    {
        self::$context = [
            'post_id' => $postId,
            'occurrence_key' => $occurrenceKey,
            'range' => $range,
        ];
    }

    public static function get(): ?array
    {
        return self::$context;
    }

    public static function reset(): void
    {
        self::$context = null;
    }
}
