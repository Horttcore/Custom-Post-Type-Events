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
        \add_action('manage_event_posts_custom_column', [$this,'printAdminColumn'], 5, 2);
        \add_filter('manage_edit-event_sortable_columns', [$this,'makeAdminColumnSortable']);
        \add_action('pre_get_posts', [$this, 'addDefaultOrder']);
        \add_action('pre_get_posts', [$this, 'sortableByEventDate']);
        \add_action('pre_get_posts', [$this, 'archiveArgs']);
        \add_filter('rest_event_collection_params', [$this,'addCustomOrderByValue']);
    }

    /**
     * Register meta.
     **/
    public function registerMeta(): void
    {
        \register_meta('post', 'eventStart', [
            'object_subtype' => 'event',
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'string',
            'description' => 'Event date start',
            'single' => true,
            'show_in_rest' => true,
        ]);

        \register_meta('post', 'eventEnd', [
            'object_subtype' => 'event',
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'string',
            'description' => 'Event date end',
            'single' => true,
            'show_in_rest' => true,
        ]);
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
        $postType = $query->get('post_type');
        
        if ('event' != $postType) {
            return;
        }

        $orderBy = $query->get('orderby');
        if (!$orderBy) {
            $query->set('orderby', 'event-date');
        }

        $order = $query->get('order');
        if (!$order) {
            $query->set('order', 'asc');
        }
    }

    /**
     * How to sort by event-date
     **/
    public function sortableByEventDate(\WP_Query $query): void
    {
        $orderby = $query->get('orderby');
        
        if ('event-date' != $orderby) {
            return;
        }
        
        $query->set('meta_key', 'eventStart');
        $query->set('orderby', 'meta_value');
    }

    /**
     * Add custom sort parameter
     **/
    public function addCustomOrderByValue(array $params): array
    {
        $params['orderby']['enum'][] = 'event-date';

        return $params;
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function archiveArgs($query)
    {
        if ( !is_post_type_archive('event') || !$query->is_main_query() || is_admin()) {
            return;
        }

        $query->set('meta_query', [
            [
                'key' => 'eventStart',
                'value' => date('Y-m-d H:i:s'),
                'compare' => '>=',
                'type' => 'DATETIME',
            ]
        ]);
    }
}
