<?php get_header(); ?>
<div class="container" id="page">
    <div id="content">
        <?php the_post(); ?>
        <div class="post">
            <h2 class="title">
                <?php the_title(); ?>
            </h2>
            <p class="meta">
                <span class="date"><?php echo get_the_date('j \d\e F \d\e Y' ); ?></span>
                <span class="posted">Postado por <?php the_author_posts_link(); ?></span>
            </p>
            <div style="clear: both;">&nbsp;</div>
            <div class="entry">
                <?php the_content(); ?>
            </div>
        </div>

        <h3><?php echo get_post_meta( $post->ID, 'pw_model', true ); ?></h3>

        <p>
            Categorias:
            <span><?php the_category( ', ' ); ?></span> |
            <span><?php the_tags(); ?></span>
        </p>



        <div class="wp-comments">
            <?php comments_template(); ?>
        </div>
        <div style="clear: both;">&nbsp;</div>
    </div>
    <?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
