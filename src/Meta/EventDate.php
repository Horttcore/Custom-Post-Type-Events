<?php

namespace RalfHortt\CustomPostTypeEvents\Meta;

class EventDate
{
    /**
     * Construct.
     **/
    public function register(): void
    {
        \add_action('init', [$this, 'registerMeta']);
        \add_filter('manage_event_posts_columns', [$this, 'addAdminColumn'], 5);
        \add_action('manage_event_posts_custom_column', [$this, 'printAdminColumn'], 5, 2);
        \add_filter('manage_edit-event_sortable_columns', [$this, 'makeAdminColumnSortable']);
        \add_action('pre_get_posts', [$this, 'addDefaultOrder'], 5);
        \add_action('pre_get_posts', [$this, 'orderByEventDate'], 10);
        \add_action('pre_get_posts', [$this, 'sortableByEventDate'], 15);
        \add_action('query_loop_block_query_vars', [$this, 'loopBlockArgs'], 5, 3);
        \add_filter('rest_event_collection_params', [$this, 'addCustomOrderByValue']);
        \add_filter('rest_event_query', [$this, 'addDateRangeToRestQuery'], 10, 2);
    }

    /**
     * Register meta.
     **/
    public function registerMeta(): void
    {
        $eventSubtype = ['object_subtype' => 'event'];

        \register_meta('post', 'eventStart', $eventSubtype + [
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'string',
            'description' => __('Event date start (Y-m-d H:i:s)', 'custom-post-type-events'),
            'single' => true,
            'show_in_rest' => true,
        ]);

        \register_meta('post', 'eventEnd', $eventSubtype + [
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'string',
            'description' => __('Event date end (Y-m-d H:i:s)', 'custom-post-type-events'),
            'single' => true,
            'show_in_rest' => true,
        ]);

        \register_meta('post', 'eventOccurrences', $eventSubtype + [
            'type' => 'array',
            'description' => __('Serialized event occurrences (id + range)', 'custom-post-type-events'),
            'single' => true,
            'sanitize_callback' => [$this, 'sanitizeEventOccurrencesMeta'],
            'show_in_rest' => [
                'schema' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => [
                                'type' => 'string',
                            ],
                            'range' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        \register_meta('post', 'eventPrimaryRange', $eventSubtype + [
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'string',
            'description' => __('Primary display range (start/end joined with /)', 'custom-post-type-events'),
            'single' => true,
            'show_in_rest' => true,
        ]);

        \register_meta('post', 'eventLastEnd', $eventSubtype + [
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'string',
            'description' => __('Latest occurrence end datetime (Y-m-d H:i:s)', 'custom-post-type-events'),
            'single' => true,
            'show_in_rest' => true,
        ]);

        \register_meta('post', 'eventIsAllDay', $eventSubtype + [
            'type' => 'boolean',
            'description' => __('Whether the event is treated as all-day', 'custom-post-type-events'),
            'single' => true,
            'sanitize_callback' => [$this, 'sanitizeEventIsAllDayMeta'],
            'show_in_rest' => true,
        ]);
    }

    /**
     * @param  mixed  $value
     * @return array<int, array{id: string, range: string}>
     */
    public function sanitizeEventOccurrencesMeta($value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $out = [];

        foreach ($value as $row) {
            if (! is_array($row)) {
                continue;
            }

            $id = isset($row['id']) ? \sanitize_text_field((string) $row['id']) : '';
            $range = isset($row['range']) ? \sanitize_text_field((string) $row['range']) : '';

            if ($id === '' || $range === '') {
                continue;
            }

            $out[] = [
                'id' => $id,
                'range' => $range,
            ];
        }

        return $out;
    }

    /**
     * @param  mixed  $value
     */
    public function sanitizeEventIsAllDayMeta($value): bool
    {
        return (bool) $value;
    }

    /**
     * Add management columns
     **/
    public function printAdminColumn($column, $postID): void
    {
        switch ($column) {
            case 'event-date':
                echo \getEventDate($postID);
                echo '<br>';
                echo \getEventTime($postID);
                break;
        }
    }

    /**
     * Add event columns
     **/
    public function addAdminColumn(array $columns): array
    {
        $columns['event-date'] = __('Event Date', 'custom-post-type-events');

        return $columns;
    }

    /**
     * Make event columns sortable
     **/
    public function makeAdminColumnSortable(array $columns): array
    {
        $columns['event-date'] = 'event-date';

        return $columns;
    }

    /**
     * How to sort by event-date
     **/
    public function addDefaultOrder(\WP_Query $query): void
    {

        if ($query->get('post_type') != 'event' || !is_admin()) {
            return;
        }

        $orderBy = $query->get('orderby');
        if (! $orderBy) {
            $order = $query->get('order');
            $query->set('orderby', 'event-date');
            $query->set('order', $order ?: 'asc');
        }
    }

    /**
     * How to sort by event-date
     **/
    public function loopBlockArgs(array $query, \WP_Block $block, int $page): array
    {
        if ($query['post_type'] != 'event') {
            return $query;
        }

        $orderBy = $query['orderby'] ??  null;
        if (! $orderBy) {
            $order = $query['order'] ?? null;
            $query['orderby'] = 'event-date';
            $query['order'] = $order ?: 'asc';
        }
        return $query;
    }

    /**
     * How to sort by event-date
     **/
    public function sortableByEventDate(\WP_Query $query): void
    {
        $orderby = $query->get('orderby');

        if ($orderby != 'event-date') {
            return;
        }

        $query->set('meta_key', 'eventStart');
        $query->set('orderby', 'meta_value');
    }

    public function orderByEventDate(\WP_Query $query): void
    {
        if (is_admin() || $query->get('post_type') != 'event' || $query->is_singular()) {
            return;
        }

        $eventStartAfter = $query->get('eventStartAfter');
        $eventStartBefore = $query->get('eventStartBefore');
        $showPast = (isset($query->query_vars['showPastEvents']) && $query->query_vars['showPastEvents']) || $query->get('showPastEvents');

        // Check if an explicit orderby is already set (respect user's choice over defaults)
        $currentOrderby = $query->get('orderby');
        $isExplicitOrderby = $currentOrderby && $currentOrderby !== 'date';

        // Only apply meta_value ordering if no explicit orderby is set
        if (!$isExplicitOrderby) {
            $query->set('meta_key', 'eventStart');
            $query->set('orderby', 'meta_value');
            $query->set('order', $showPast ? 'DESC' : 'ASC');
        }

        if ($eventStartAfter || $eventStartBefore) {
            $metaQuery = [
                'key' => 'eventStart',
                'type' => 'DATETIME',
            ];

            if ($eventStartAfter && $eventStartBefore) {
                $metaQuery['compare'] = 'BETWEEN';
                $metaQuery['value'] = [
                    sanitize_text_field((string) $eventStartAfter) . ' 00:00:00',
                    sanitize_text_field((string) $eventStartBefore) . ' 23:59:59',
                ];
            } elseif ($eventStartAfter) {
                $metaQuery['compare'] = '>=';
                $metaQuery['value'] = sanitize_text_field((string) $eventStartAfter) . ' 00:00:00';
            } else {
                $metaQuery['compare'] = '<=';
                $metaQuery['value'] = sanitize_text_field((string) $eventStartBefore) . ' 23:59:59';
            }

            $query->set('meta_query', [$metaQuery]);

            return;
        }

        // Always apply the past/future filter (regardless of orderby method)
        $query->set('meta_query', [
            [
                'key' => 'eventEnd',
                'compare' => $showPast ? '<' : '>=',
                'value' => date('Y-m-d H:i:s'),
                'type' => 'DATETIME',
            ],
        ]);
    }

    /**
     * Add custom sort parameter
     **/
    public function addCustomOrderByValue(array $params): array
    {
        $params['orderby']['enum'][] = 'event-date';

        $params['eventDateRange'] = [
            'description' => __('Preset event date range.', 'custom-post-type-events'),
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ];

        $params['eventStartAfter'] = [
            'description' => __('Filter events starting on or after this date.', 'custom-post-type-events'),
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ];

        $params['eventStartBefore'] = [
            'description' => __('Filter events starting on or before this date.', 'custom-post-type-events'),
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ];

        return $params;
    }

    public function addDateRangeToRestQuery(array $args, \WP_REST_Request $request): array
    {
        $range = (string) $request->get_param('eventDateRange');

        if ($range) {
            $start = new \DateTimeImmutable('first day of next month');
            $end = match ($range) {
                'next-month' => $start->modify('last day of this month'),
                'next-3-months' => $start->modify('+2 months')->modify('last day of this month'),
                'next-6-months' => $start->modify('+5 months')->modify('last day of this month'),
                'next-12-months' => $start->modify('+11 months')->modify('last day of this month'),
                default => null,
            };

            if ($end instanceof \DateTimeImmutable) {
                $args['eventStartAfter'] = $start->format('Y-m-d');
                $args['eventStartBefore'] = $end->format('Y-m-d');
            }
        }

        $eventStartAfter = $request->get_param('eventStartAfter');
        $eventStartBefore = $request->get_param('eventStartBefore');

        if ($eventStartAfter) {
            $args['eventStartAfter'] = sanitize_text_field((string) $eventStartAfter);
        }

        if ($eventStartBefore) {
            $args['eventStartBefore'] = sanitize_text_field((string) $eventStartBefore);
        }

        return $args;
    }
}
