<?php
/**
 *
 * Template Name: Página de largura completa
 *
 */
get_header(); ?>

    <div class="container">
        <?php the_post(); ?>
        <h1><?php the_title(); ?></h1>
        <div class="post-content">
            <?php the_content(); ?>
        </div>
    </div>

<?php get_footer(); ?>