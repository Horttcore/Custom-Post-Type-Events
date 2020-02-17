<?php

namespace RalfHortt\CustomPostTypeEvents\MetaBoxes;

use Horttcore\MetaBoxes\MetaBox;

class EventLocation extends MetaBox
{
    /**
     * Construct.
     *
     * @since 1.0.0
     **/
    public function __construct()
    {
        $this->identifier = 'post-icon';
        $this->name = __('Location', 'custom-post-type-events');
        $this->screen = 'event';
    }

    /**
     * Register meta.
     *
     * @return void
     **/
    public function registerMeta()
    {
        register_meta('post', 'event-date-start', [
            'object_subtype' => 'event',
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'number',
            'description' => 'Event date start',
            'single' => true,
            'show_in_rest' => true,
        ]);

        register_meta('post', 'event-date-end', [
            'object_subtype' => 'event',
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'number',
            'description' => 'Event date end',
            'single' => true,
            'show_in_rest' => true,
        ]);

        register_meta('post', 'event-time-start', [
            'object_subtype' => 'event',
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'number',
            'description' => 'Event time start',
            'single' => true,
            'show_in_rest' => true,
        ]);

        register_meta('post', 'event-time-end', [
            'object_subtype' => 'event',
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'number',
            'description' => 'Event time end',
            'single' => true,
            'show_in_rest' => true,
        ]);
    }

    /**
     * Render the meta box.
     *
     * @param WP_Post $post Post object
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function render(\WP_Post $post)
    {
    }

    /**
     * Register post type.
     *
     * @param int $postId Post ID
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function save(int $postId)
    {
        update_post_meta($postId, '_url', esc_url_raw($_POST['event-url']));
    }
}
