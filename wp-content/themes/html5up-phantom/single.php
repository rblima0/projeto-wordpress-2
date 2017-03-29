<?php get_header(); ?>

<div id="main">
    <div class="inner">
        <?php the_post(); ?>
        <h1><?php the_title(); ?></h1>
        <?php if(has_post_thumbnail()){ ?>
            <span class="image main">
                <?php the_post_thumbnail('full'); ?>
            </span>
        <?php } ?>

        <div class="post-content">
            <?php the_content(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>

