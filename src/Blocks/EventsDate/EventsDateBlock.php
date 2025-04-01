<?php

namespace RalfHortt\CustomPostTypeEvents\Blocks\EventsDate;

use RalfHortt\WPBlock\Block;

class EventsDateBlock extends Block
{
    protected string $name = 'custom-post-type-events/events-date';

    protected string $title = 'Events Date';

    protected string $blockJson = __DIR__.'/../../../build/Blocks/EventsDate/block.json';

    public function render(array $atts, string $content): void
    {
        $args = wp_parse_args($atts, [
            'format' => '',
        ]);

        ?>
        <div class="wp-block-custom-post-type-events-event-date">
            <?= getEventDate(get_the_ID(), $args['format'] ? $args['format'] : get_option('date_format')) ?>
        </div>
        <?php
    }
}
