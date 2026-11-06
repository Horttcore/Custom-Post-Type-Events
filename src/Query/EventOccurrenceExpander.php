<?php

declare(strict_types=1);

namespace RalfHortt\CustomPostTypeEvents\Query;

use RalfHortt\CustomPostTypeEvents\Support\EventDateRange;
use RalfHortt\CustomPostTypeEvents\Support\EventOccurrenceCollection;

final class EventOccurrenceExpander
{
    public static function expand(array $posts, array $occurrencesByPost, array $args = []): array
    {
        $now = (int) ($args['now'] ?? time());
        $showPast = (bool) ($args['show_past'] ?? false);
        $order = (string) ($args['order'] ?? 'asc');
        $offset = (int) ($args['offset'] ?? 0);
        $perPage = isset($args['per_page']) ? (int) $args['per_page'] : null;

        $rows = [];

        foreach ($posts as $post) {
            $postId = (int) $post->ID;
            $occurrences = $occurrencesByPost[$postId] ?? [];
            $collection = new EventOccurrenceCollection($occurrences);
            $filtered = $showPast ? $collection->filterPast($now) : $collection->filterUpcoming($now);

            foreach ($filtered as $occurrence) {
                $parsed = EventDateRange::parse((string) ($occurrence['range'] ?? ''));

                if ($parsed === null) {
                    continue;
                }

                $rows[] = [
                    'post_id' => $postId,
                    'occurrence_key' => (string) ($occurrence['id'] ?? ''),
                    'range' => (string) $occurrence['range'],
                    'start' => $parsed['start'],
                    'end' => $parsed['end'],
                    'startTs' => $parsed['startTs'],
                    'endTs' => $parsed['endTs'],
                ];
            }
        }

        usort($rows, static function (array $a, array $b) use ($order): int {
            $compare = $a['startTs'] <=> $b['startTs'];

            return $order === 'desc' ? -$compare : $compare;
        });

        if ($perPage !== null) {
            return array_slice($rows, $offset, $perPage);
        }

        if ($offset > 0) {
            return array_slice($rows, $offset);
        }

        return $rows;
    }
}
