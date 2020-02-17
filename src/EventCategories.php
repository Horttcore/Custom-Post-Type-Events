<?php
namespace RalfHortt\CustomPostTypeEvents;

use Horttcore\CustomTaxonomy\Taxonomy;

/**
 *  Custom Post Type Produts.
 */
class EventCategories extends Taxonomy
{
    protected $slug = 'event-category';
    
    protected $postTypes = ['event'];

    /**
     * Register post type.
     *
     * @return array Post type configuration
     */
    public function getConfig() : array
    {
        return [
            'public'            => true,
            'hierarchical'      => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rest_base'         => _x('event-categories', 'Taxonomy slug', 'custom-post-type-events'),
        ];
    }
    // END config

    /**
     * Labels.
     *
     * @return array
     **/
    public function getLabels() : array
    {
        return [
            'name'                       => _x('Event Categories', 'taxonomy general name', 'custom-post-type-events'),
            'singular_name'              => _x('Event Category', 'taxonomy singular name', 'custom-post-type-events'),
            'menu_name'                  => __('Event Categories', 'custom-post-type-events'),
            'all_items'                  => __('All Event Categories', 'custom-post-type-events'),
            'edit_item'                  => __('Edit Event Category', 'custom-post-type-events'),
            'view_item'                  => __('View Event Category', 'custom-post-type-events'),
            'update_item'                => __('Update Event Category', 'custom-post-type-events'),
            'add_new_item'               => __('Add New Event Category', 'custom-post-type-events'),
            'new_item_name'              => __('New Event Category Name', 'custom-post-type-events'),
            'parent_item'                => __('Parent Event Category', 'custom-post-type-events'),
            'parent_item_colon'          => __('Parent Event Category:', 'custom-post-type-events'),
            'search_items'               => __('Search Event Categories', 'custom-post-type-events'),
            'popular_items'              => __('Popular Event Categories', 'custom-post-type-events'),
            'separate_items_with_commas' => __('Separate event categories with commas', 'custom-post-type-events'),
            'add_or_remove_items'        => __('Add or remove tags event categories', 'custom-post-type-events'),
            'choose_from_most_used'      => __('Choose from the most used event categories', 'custom-post-type-events'),
            'not_found'                  => __('No event categories found.', 'custom-post-type-events'),
            'back_to_items'              => __('Back to event categories', 'custom-post-type-events'),
        ];
    }
} // END class Events
