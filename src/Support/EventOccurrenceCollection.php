<?php

declare(strict_types=1);

namespace RalfHortt\CustomPostTypeEvents\Support;

final class EventOccurrenceCollection
{
    public function __construct(private array $occurrences)
    {
    }

    public function filterUpcoming(int $now): array
    {
        return array_values(array_filter(
            $this->occurrences,
            function (array $occurrence) use ($now): bool {
                $parsed = EventDateRange::parse((string) ($occurrence['range'] ?? ''));

                return $parsed !== null && $parsed['endTs'] >= $now;
            }
        ));
    }

    public function filterPast(int $now): array
    {
        return array_values(array_filter(
            $this->occurrences,
            function (array $occurrence) use ($now): bool {
                $parsed = EventDateRange::parse((string) ($occurrence['range'] ?? ''));

                return $parsed !== null && $parsed['endTs'] < $now;
            }
        ));
    }

    public function sortByStart(string $order = 'asc'): array
    {
        $occurrences = $this->occurrences;

        usort($occurrences, function (array $a, array $b) use ($order): int {
            $aParsed = EventDateRange::parse((string) ($a['range'] ?? ''));
            $bParsed = EventDateRange::parse((string) ($b['range'] ?? ''));

            $compare = ($aParsed['startTs'] ?? 0) <=> ($bParsed['startTs'] ?? 0);

            return $order === 'desc' ? -$compare : $compare;
        });

        return $occurrences;
    }

    public function getByKey(string $key): ?array
    {
        foreach ($this->occurrences as $occurrence) {
            if (($occurrence['id'] ?? null) === $key) {
                return $occurrence;
            }
        }

        return null;
    }
}
