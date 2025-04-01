<?php

namespace RalfHortt\CustomPostTypeEvents\PostTypes;

use RalfHortt\CustomPostType\PostType;

class Events extends PostType
{
    protected string $slug = 'event';

    public function getConfig(): array
    {
        return [
            'public' => true,
            'show_ui' => true,
            'query_var' => true,
            'menu_position' => null,
            'menu_icon' => 'dashicons-calendar-alt',
            'capability_type' => 'post',
            'hierarchical' => true,
            'supports' => [
                'title',
                'editor',
                'thumbnail',
                'custom-fields',
                'revisions',
            ],
            'has_archive' => true,
            'rewrite' => [
                'slug' => _x('events', 'Post Type Slug', 'custom-post-type-events'),
                'with_front' => false,
            ],
            'show_in_rest' => true,
            'rest_base' => _x('events', 'Post Type Slug', 'custom-post-type-events'),
        ];
    }

    public function getLabels(): array
    {
        return [
            'name' => _x('Events', 'post type general name', 'custom-post-type-events'),
            'singular_name' => _x('Event', 'post type singular name', 'custom-post-type-events'),
            'add_new' => _x('Add New', 'Event', 'custom-post-type-events'),
            'add_new_item' => __('Add New Event', 'custom-post-type-events'),
            'edit_item' => __('Edit Event', 'custom-post-type-events'),
            'new_item' => __('New Event', 'custom-post-type-events'),
            'view_item' => __('View Event', 'custom-post-type-events'),
            'view_items' => __('View Events', 'custom-post-type-events'),
            'search_items' => __('Search Events', 'custom-post-type-events'),
            'not_found' => __('No Events found', 'custom-post-type-events'),
            'not_found_in_trash' => __('No Events found in Trash', 'custom-post-type-events'),
            'parent_item_colon' => __('Parent Event', 'custom-post-type-events'),
            'all_items' => __('All Events', 'custom-post-type-events'),
            'archives' => __('Event Archives', 'custom-post-type-events'),
            'attributes' => __('Event Attributes', 'custom-post-type-events'),
            'insert_into_item' => __('Insert into event', 'custom-post-type-events'),
            'uploaded_to_this_item' => __('Uploaded to this page', 'custom-post-type-events'),
            'featured_image' => __('Event image', 'custom-post-type-events'),
            'set_featured_image' => __('Set Event image', 'custom-post-type-events'),
            'remove_featured_image' => __('Remove Event image', 'custom-post-type-events'),
            'use_featured_image' => __('Use as Event image', 'custom-post-type-events'),
            'menu_name' => _x('Events', 'post type general name', 'custom-post-type-events'),
            'filter_items_list' => __('Events', 'custom-post-type-events'),
            'items_list_navigation' => __('Events', 'custom-post-type-events'),
            'items_list' => __('Events', 'custom-post-type-events'),
        ];
    }

    public function getPostUpdateMessages(\WP_Post $post, string $postType, \WP_Post_Type $postTypeObjects): array
    {
        $messages = [
            0 => '', // Unused. Messages start at index 1.
            1 => __('Event updated.', 'custom-post-type-events'),
            2 => __('Custom field updated.'),
            3 => __('Custom field deleted.'),
            4 => __('Event updated.', 'custom-post-type-events'),
            5 => isset($_GET['revision']) ? sprintf(__('Event restored to revision from %s', 'custom-post-type-events'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => __('Event published.', 'custom-post-type-events'),
            7 => __('Event saved.', 'custom-post-type-events'),
            8 => __('Event submitted.', 'custom-post-type-events'),
            9 => sprintf(__('Event scheduled for: <strong>%1$s</strong>.', 'custom-post-type-events'), date_i18n(__('M j, Y @ G:i', 'custom-post-type-events'), strtotime($post->post_date))),
            10 => __('Event draft updated.', 'custom-post-type-events'),
        ];

        if (! $postTypeObjects->publicly_queryable) {
            return $messages;
        }

        $permalink = get_permalink($post->ID);
        $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View event', 'custom-post-type-events'));
        $messages[1] .= $view_link;
        $messages[6] .= $view_link;
        $messages[9] .= $view_link;

        $preview_permalink = add_query_arg('preview', 'true', $permalink);
        $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview event', 'custom-post-type-events'));
        $messages[8] .= $preview_link;
        $messages[10] .= $preview_link;

        return $messages;
    }
}
