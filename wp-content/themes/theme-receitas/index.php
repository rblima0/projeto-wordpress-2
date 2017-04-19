<?php get_header(); ?>

<div id="page">
    <div id="content">
        <?php pw_breadcrumbs(); ?>
        <hr>
        <?php while ( have_posts() ) { the_post(); ?>
            <div class="column-index">
            <?php the_post_thumbnail( 'recipe' ); ?>
            <h3 class="title">
                <?php
                printf( '<a href="%1$s" title="%2$s">%2$s</a>',
                    get_permalink(),
                    get_the_title()
                );?>
            </h3>
            <div class="my-resume">
                <?php the_excerpt(); ?>
            </div>
            </div>
        <?php } ?>
        <?php pw_paginate_links(); ?>
    </div>
    <?php get_sidebar(); ?>
    <div style="clear: both;">&nbsp;</div>
</div>

<?php get_footer(); ?>
