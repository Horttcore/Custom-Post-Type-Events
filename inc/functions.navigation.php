<?php
if (!function_exists('getPreviousEvent')) :
    /**
     * Get event location
     *
     * @param int $postId Post ID
     * @param str $field Location field
     * @return str/array Location
     * @author Ralf Hortt
     **/
    function getPreviousEvent(int $postId): ?WP_Post
    {
        $event = getEventInfo($postId);

        if (!$event['dateTimeStart']) {
            return null;
        }

        $posts = get_posts([
            'post_type' => 'event',
            'orderby' => 'event-date',
            'order' => 'DESC',
            'showposts' => 1,
            'meta_query' => [
                [
                    'key' => 'eventStart',
                    'value' => $event['dateTimeStart'],
                    'compare' => '<',
                    'type' => 'DATETIME'
                ]
            ]
        ]);

        if (empty($posts)) {
            return null;
        }

        return $posts[0];
    }
endif;

if (!function_exists('getNextEvent')) :
    /**
     * Get event location
     *
     * @param int $postId Post ID
     * @param str $field Location field
     * @return str/array Location
     * @author Ralf Hortt
     **/
    function getNextEvent($postId): ?WP_Post
    {
        $event = getEventInfo($postId);
        if (!$event['dateTimeStart']) {
            return null;
        }
            
        $posts = get_posts([
            'post_type' => 'event',
            'orderby' => 'event-date',
            'order' => 'ASC',
            'showposts' => 1,
            'meta_query' => [
                [
                    'key' => 'eventStart',
                    'value' => $event['dateTimeStart'],
                    'compare' => '>',
                    'type' => 'DATETIME'
                ]
            ]
        ]);

        if (empty($posts)) {
            return null;
        }

        return $posts[0];
    }
endif;
