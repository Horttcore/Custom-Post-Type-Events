<?php

if (! function_exists('getPreviousEvent')) {
    /**
     * Get the previous event by start date.
     *
     * @param  int  $postId  Post ID
     * @return WP_Post|null Previous event post
     *
     * @author Ralf Hortt
     **/
    function getPreviousEvent(int $postId): ?WP_Post
    {
        $event = getEventInfo($postId);

        if (! $event['dateTimeStart']) {
            return null;
        }

        $posts = get_posts([
            'post_type' => 'event',
            'post__not_in' => [$postId],
            'orderby' => 'event-date',
            'order' => 'DESC',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => 'eventStart',
                    'value' => $event['dateTimeStart'],
                    'compare' => '<',
                    'type' => 'DATETIME',
                ],
            ],
        ]);

        if (empty($posts)) {
            return null;
        }

        return $posts[0];
    }
}

if (! function_exists('getNextEvent')) {
    /**
     * Get the next event by start date.
     *
     * @param  int  $postId  Post ID
     * @return WP_Post|null Next event post
     *
     * @author Ralf Hortt
     **/
    function getNextEvent(int $postId): ?WP_Post
    {
        $event = getEventInfo($postId);

        if (! $event['dateTimeStart']) {
            return null;
        }

        $posts = get_posts([
            'post_type' => 'event',
            'post__not_in' => [$postId],
            'orderby' => 'event-date',
            'order' => 'ASC',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => 'eventStart',
                    'value' => $event['dateTimeStart'],
                    'compare' => '>',
                    'type' => 'DATETIME',
                ],
            ],
        ]);

        if (empty($posts)) {
            return null;
        }

        return $posts[0];
    }
}
