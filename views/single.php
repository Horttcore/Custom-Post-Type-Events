<article id="post-<?php the_ID() ?>" <?php post_class('event') ?>>

    <?php
    if (has_post_thumbnail()) {
        ?>
        <figure class="event__image">
            <a href="<?php the_permalink() ?>">
                <?php the_post_thumbnail() ?>
            </a>
        </figure>
        <?php
    }
    ?>

    <div class="event__content">
        <header class="event__header">
            <h1 class="event__title">
                <a href="<?php the_permalink() ?>">
                    <?php the_title() ?>
                </a>
            </h1>
            <div class="event__meta">
                <?php the_terms(get_the_ID(), 'event-category', '<div class="event__categories>', ' ', '</div>') ?>
                <?php theEventDateTime(false, '<div class="event__date">', '</div>') ?>
            </div>
        </header>

        <div class="event__body">
            <?php the_content(__('Read more')) ?>
        </div>
    </div>

</article>
