<?php get_header(); ?>

    <div id="page">
        <div id="content">
            <?php pw_breadcrumbs(); ?>
            <hr>
            <div class="img-post"><?php the_post_thumbnail( 'full' ); ?></div>
            <?php the_post(); ?>
            <div class="post">
                <h1 class="title title-post">
                    <?php the_title(); ?>
                </h1>

                <div class="info">
                    <div class="info-detalhes">
                        <h4>Dificuldade</h4>
                        <?php echo get_post_meta( $post->ID, 'pw_level', true ); ?>
                    </div>
                    <div class="info-detalhes">
                        <h4>Calorias</h4>
                        <?php echo get_post_meta( $post->ID, 'pw_kcal', true ); ?>
                    </div>
                    <div class="info-detalhes">
                        <h4>Tempo de Preparo</h4>
                        <?php echo get_post_meta( $post->ID, 'pw_time', true ); ?>
                    </div>
                </div>

                <div class="entry">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        <?php get_sidebar(); ?>
    </div>

<?php get_footer(); ?>