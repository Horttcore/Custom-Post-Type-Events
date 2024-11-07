<?php

namespace RalfHortt\CustomPostTypeEvents\Blocks\Events;

use RalfHortt\WPBlock\Block;

class EventsBlock extends Block
{
    public string $name = 'custom-post-type-events/events';

    protected string $title = 'Events';

    protected string $blockJson = './build/blocks/Events/block.json';

    public function render(array $atts, string $content): void
    {
        $defaults = wp_parse_args($atts, [
            'post_type' => 'event',
            'orderBy' => 'event-date',
            'order' => 'asc',
            'offset' => 0,
            'eventCategory' => [],
        ]);

        $attributes = array_filter([
            ...$defaults,
            'posts_per_page' => $defaults['numberOfItems'],
            'order' => ! empty($defaults['postIn']) ? 'ASC' : $defaults['order'],
            'orderBy' => $this->getOrderBy($defaults),
            'tax_query' => $defaults['eventCategory'] ? [
                [
                    'taxonomy' => 'event-category',
                    'field' => 'id',
                    'terms' => $defaults['eventCategory'],
                ],
            ] : false,
            'post__in' => $defaults['postIn'],
        ]);
        $query = new \WP_Query($attributes);
        if ($query->have_posts()) {
            require apply_filters('custom-post-type-events-loop-template', plugin_dir_path(__FILE__).'/../../../views/loop.php', $query, $attributes);
        }

        wp_reset_query();
    }

    private function getOrderBy(array $attributes): string
    {
        if (! empty($attributes['postIn'])) {
            return 'post__in';
        }

        if ($attributes['orderBy'] === 'date') {
            return 'event-date';
        }

        return $attributes['orderBy'];
    }
}