<?php global $item; ?>
<?php 

the_post();

get_header(); ?>

    <!-- Content -->
    <section>
        <header class="main">
            <h1><?php the_title(); ?></h1>
        </header>

        <div class="post-content">
            <?php the_content(); ?>
        </div>
    </section>

    <?php get_sidebar(); ?>

<?php get_footer(); ?>
