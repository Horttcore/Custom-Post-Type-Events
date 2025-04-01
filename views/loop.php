<section <?= get_block_wrapper_attributes(['class' => 'events']) ?>>
    <?php do_action('custom-post-type-events-loop-template-before') ?>
    <div class="events__list" style="--columns: <?= $atts['gridColumns'] ?>; <?php if ($atts['style']['spacing']['blockGap']) { ?>--gap: <?= convertStylesToCustomProperty($atts['style']['spacing']['blockGap']) ?><?php } ?>">
        <?php
        while ($query->have_posts()) {
            $query->the_post();
            require apply_filters('custom-post-type-events-single-template', plugin_dir_path(__FILE__) . 'single.php', $query, $attributes);
        }
        ?>
    </div>
    <?php do_action('custom-post-type-events-loop-template-after') ?>
</section>