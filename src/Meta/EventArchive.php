<?php

namespace RalfHortt\CustomPostTypeEvents;

class EventArchive
{
    public string $postTypeSlug = 'event';

    public function register(): void
    {
        add_action('init', [$this, 'addCustomRewriteRules']);
        add_filter('query_vars', [$this, 'registerQueryVars']);
        add_action('pre_get_posts', [$this, 'modifyEventArchiveQuery']);
    }

    /**
     * Add custom rewrite rules for /events/archive/.
     *
     * @return void
     */
    public function addCustomRewriteRules() : void
    {
        add_rewrite_rule(
            '^' . _x('events', 'Post Type Slug', 'custom-post-type-events') . '/' . __('archive', 'custom-post-type-events') . '/page/([0-9]+)/?$',
            'index.php?post_type=event&archive=true&paged=$matches[1]',
            'top'
        );

        add_rewrite_rule(
            '^events/' . _x('archive', 'Past events archive suffix', 'custom-post-type-events') . '/?$',
            'index.php?post_type=event&archive=true',
            'top'
        );
    }

    /**
     * Register the 'archive' query variable.
     *
     * @param array $vars The existing query variables.
     * @return array The modified query variables.
     */
    public function registerQueryVars(array $vars) : array
    {
        $vars[] = 'archive';
        return $vars;
    }

    /**
     * Modify the query for the event archive to include past events.
     *
     * @param \WP_Query $query The WP_Query instance.
     * @return void
     */
    public function modifyEventArchiveQuery(\WP_Query $query) : void
    {
        if (!is_admin() && $query->is_main_query() && is_post_type_archive($this->postTypeSlug)) {
            if (get_query_var('archive') === 'true') {
                $query->set('meta_query', [
                    [
                        'key'     => 'eventEnd',
                        'value'   => current_time('Y-m-d'),
                        'compare' => '<',
                        'type'    => 'DATE',
                    ],
                ]);
                $query->set('meta_key', 'eventStart');
                $query->set('orderby', 'meta_value');
                $query->set('order', 'DESC');
            }
        }
    }
}
