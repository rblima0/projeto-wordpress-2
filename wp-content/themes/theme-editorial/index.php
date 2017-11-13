<?php

global $screen;

get_header(); ?>

    <!-- Section -->
    <section>
        <header class="major">
            <h2>
                <?php echo $screen->title; ?>
            </h2>
        </header>
        <div class="posts">
            <?php
            if ( !$screen->have_items() ) {
                echo '<p>Nenhum resultado foi encontrado.</p>';
            } else {
                while ( $screen->the_item() ) {
                    get_template_part( 'partial/post' );
                }
            } ?>
        </div>
    </section>

    <?php get_sidebar(); ?>

<?php get_footer(); ?>
