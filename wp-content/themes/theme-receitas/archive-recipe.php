<?php get_header(); ?>

    <div class="container">
        <?php pw_breadcrumbs(); $i=1; ?>
        <?php if ( is_home() ) { ?>
    <h2><span>Receitas</span> em Destaque</h2>
        <?php } else { ?>
    <hr>
        <?php } ?>
    </div>
    <div id="featured-content">
    <?php while ( have_posts() ) { the_post(); ?>
        <div class="column" id="column<?php echo $i; ?>">
            <?php the_post_thumbnail( 'recipe' ); ?>
            <h2><?php the_title(); ?></h2>
            <div class="my-resume">
                <?php if (has_excerpt() ) {
                    the_excerpt();
                } ?>
            </div>
            <div class="my-recipes">
                <a href="<?php the_permalink(); ?>" title="Ver receita">Ver receita</a>
            </div>
        </div>
       <?php $i++; if ( $i==4 ) $i=1; } ?>
    </div>

    <?php if ( is_home() ) { ?>
        <div class="clearfix all-recipes">
            <a href="<?php echo get_post_type_archive_link( 'recipe' ); ?>" title="Todas as receitas">Veja mais receitas</a>
        </div>
    <?php } else { ?>
        <div class="container">
    <?php pw_paginate_links(); ?>
        </div>
    <?php } ?>

<?php get_footer(); ?>