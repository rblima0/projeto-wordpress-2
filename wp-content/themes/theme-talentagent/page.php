<?php get_header(); ?>

    <div id="page" class="container">
        <div id="content">
            <div class="post">
                <?php the_post(); ?>
                <h1><?php the_title(); ?></h1>
                <div class="post-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        <?php get_sidebar(); ?>
    </div>

<?php get_footer(); ?>