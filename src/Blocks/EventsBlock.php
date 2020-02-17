<?php
namespace RalfHortt\CustomPostTypeEvents\Blocks;

class EventsBlock
{
    /**
     * Register hooks.
     *
     * @todo Refactor in a composer package
     *
     * @return void
     **/
    public function register()
    {
        register_block_type('custom-post-type-events/events', [
            'render_callback' => function ($attributes) {
                return $this->render($attributes);
            },
        ]);
    }

    /**
     * Render the meta box.
     *
     * @param mixed $attributes Attributes
     *
     * @return string HTML output
     *
     * @since 1.0.0
     */
    public function render($attributes)
    {
        ob_start();

        $attributes = wp_parse_args($attributes, [
            'orderBy'     => 'event-date',
            'order'       => 'asc',
            'postsToShow' => 10,
        ]);

        $query = new \WP_Query([
            'post_type' => 'event',
            'orderby'   => $attributes['orderBy'],
            'order'     => $attributes['order'],
            'showposts' => $attributes['postsToShow'],
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'eventStart',
                    'value' => date_i18n('Y-m-d H:i:s'),
                    'compare' => '>',
                    'type' => 'DATETIME'
                ]
            ]
        ]);

        if ($query->have_posts()) :
            require apply_filters('custom-post-type-events-loop-template', plugin_dir_path(__FILE__).'/../../views/loop.php', $query, $attributes);
        else :
            require apply_filters('custom-post-type-events-empty-loop', plugin_dir_path(__FILE__).'/../../views/empty.php', $query, $attributes);
        endif;

        return ob_get_clean();
    }
}
