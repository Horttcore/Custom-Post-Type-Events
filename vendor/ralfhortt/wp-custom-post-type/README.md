# Custom Post Type Helper Class

## Installation

`composer require ralfhortt/wp-custom-post-type`

## Usage

Extend the abstract class `RalfHortt\CustomPostType\PostType` and overwrite following methods:

* `getConfig()`
* `getLabels()`
* `getPostUpdateMessage( \WP_Post $post, string $postType, \WP_Post_Type $postTypeObjects )`

The extending class _MUST_ define protected class variable `slug`

## Example

```php
<?php
use RalfHortt\CustomPostType\PostType;

class Thing extends PostType
{
    protected $slug = 'thing';

    /**
     * Register post type.
     *
     * @return array Post type configuration
     */
    public function getConfig() : array
    {
        return [
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [
                'slug'       => _x('things', 'Post Type Slug', 'custom-post-type-things'),
                'with_front' => false,
            ],
            'capability_type' => 'post',
            'has_archive'     => false,
            'hierarchical'    => false,
            'menu_position'   => null,
            'menu_icon'       => 'dashicons-businessman',
            'supports'        => [
                'title',
                'editor',
                'thumbnail',
                'page-attributes',
            ],
            'show_in_rest' => true,
        ];
    }

    /**
     * Labels.
     *
     * @return array
     **/
    public function getLabels() : array
    {
        return [
            'name'                  => _x('Things', 'post type general name', 'custom-post-type-things'),
            'singular_name'         => _x('Thing', 'post type singular name', 'custom-post-type-things'),
            'add_new'               => _x('Add New', 'Thing', 'custom-post-type-things'),
            'add_new_item'          => __('Add New Thing', 'custom-post-type-things'),
            'edit_item'             => __('Edit Thing', 'custom-post-type-things'),
            'new_item'              => __('New Thing', 'custom-post-type-things'),
            'view_item'             => __('View Thing', 'custom-post-type-things'),
            'view_items'            => __('View Things', 'custom-post-type-things'),
            'search_items'          => __('Search Things', 'custom-post-type-things'),
            'not_found'             => __('No Things found', 'custom-post-type-things'),
            'not_found_in_trash'    => __('No Things found in Trash', 'custom-post-type-things'),
            'parent_item_colon'     => __('Parent Thing', 'custom-post-type-things'),
            'all_items'             => __('All Things', 'custom-post-type-things'),
            'archives'              => __('Thing Archives', 'custom-post-type-things'),
            'attributes'            => __('Thing Attributes', 'custom-post-type-things'),
            'insert_into_item'      => __('Insert into thing', 'custom-post-type-things'),
            'uploaded_to_this_item' => __('Uploaded to this page', 'custom-post-type-things'),
            'featured_image'        => __('Logo', 'custom-post-type-things'),
            'set_featured_image'    => __('Set logo', 'custom-post-type-things'),
            'remove_featured_image' => __('Remove logo', 'custom-post-type-things'),
            'use_featured_image'    => __('Use as logo', 'custom-post-type-things'),
            'menu_name'             => _x('Things', 'post type general name', 'custom-post-type-things'),
            'filter_items_list'     => __('Things', 'custom-post-type-things'),
            'items_list_navigation' => __('Things', 'custom-post-type-things'),
            'items_list'            => __('Things', 'custom-post-type-things'),
        ];
    }

    /**
     * Update messages.
     *
     * @param WP_Post      $post     Post object
     * @param string       $postType Post type slug
     * @param WP_Post_Type $postType Post type slug
     *
     * @return array Update messages
     **/
    public function getPostUpdateMessages(\WP_Post $post, string $postType, \WP_Post_Type $postTypeObjects) : array
    {
        $messages = [
            0  => '', // Unused. Messages start at index 1.
            1  => __('Thing updated.', 'custom-post-type-things'),
            2  => __('Custom field updated.'),
            3  => __('Custom field deleted.'),
            4  => __('Thing updated.', 'custom-post-type-things'),
            5  => isset($_GET['revision']) ? sprintf(__('Thing restored to revision from %s', 'custom-post-type-things'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6  => __('Thing published.', 'custom-post-type-things'),
            7  => __('Thing saved.', 'custom-post-type-things'),
            8  => __('Thing submitted.', 'custom-post-type-things'),
            9  => sprintf(__('Thing scheduled for: <strong>%1$s</strong>.', 'custom-post-type-things'), date_i18n(__('M j, Y @ G:i', 'custom-post-type-things'), strtotime($post->post_date))),
            10 => __('Thing draft updated.', 'custom-post-type-things'),
        ];

        if (!$postTypeObjects->publicly_queryable) {
            return $messages;
        }

        $permalink = get_permalink($post->ID);
        $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View thing', 'custom-post-type-things'));
        $messages[1] .= $view_link;
        $messages[6] .= $view_link;
        $messages[9] .= $view_link;

        $preview_permalink = add_query_arg('preview', 'true', $permalink);
        $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview thing', 'custom-post-type-things'));
        $messages[8] .= $preview_link;
        $messages[10] .= $preview_link;

        return $messages;
    }
}

```

## Changelog

### v2.0.0

* Update namespace

### v1.0.2

* Documentation and enforcing return types

### v1.0.1

* Fix typos

### v1.0.0

* Initial release
