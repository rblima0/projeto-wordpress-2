<?php get_header(); ?>
    <div id="page" class="container">
            <div id="content">
                <?php while(have_posts()){ the_post(); global $post; ?>
                    <div class="post">
                        <h2 class="title">
                            <?php
                            printf( '<a href="%1$s" title="%2$s">%2$s</a>',
                                get_permalink(),
                                get_the_title()
                            );?>
                        </h2>
                        <h3><?php echo get_post_meta( $post->ID, 'Modelo', true ); ?></h3>
                        <p class="meta">
                            Categorias:
                            <span class="cats"><?php the_category( ', ' ); ?></span> |
                            <span class="tags"><?php the_tags(); ?></span>
                        </p>
                        <div style="clear: both;">&nbsp;</div>
                        <div class="entry">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>
                <?php } ?>
                <?php pw_paginate_links(); ?>
                <div style="clear: both;">&nbsp;</div>
            </div>
        <?php get_sidebar(); ?>
    </div>
</div>
<?php get_footer(); ?>