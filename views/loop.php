<section class="events">
    <div class="events__list">
        <?php
        while ($query->have_posts()) :
            $query->the_post();
            require apply_filters('custom-post-type-events-single-template', plugin_dir_path(__FILE__) . 'single.php', $query, $attributes);
        endwhile;
        ?>
    </div>
</section>