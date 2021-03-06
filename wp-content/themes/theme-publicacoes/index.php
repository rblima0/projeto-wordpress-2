<?php get_header(); ?>
<div id="page">
        <div id="content">
            <?php
            if ( !is_home() ) {
                $title = 'Arquivo';
                if ( is_search() ) {
                    $title = 'Resultados da busca por "' . get_search_query() . '"';
                } else if ( is_category() ){
                    $title = 'Categoria: ' . single_cat_title( '', false );
                }
                echo "<h1>{$title}</h1>";
            }
            while(have_posts()){ the_post(); ?>
            <div class="post">
                <h2 class="title">
                    <?php
                    //the_title(); FORMA RESUMIDA
                    printf( '<a href="%1$s" title="%2$s">%2$s</a>',
                        get_permalink(),
                        get_the_title()
                    );?>
                </h2>
                <p class="meta">
                    <span class="date"><?php echo get_the_date('j \d\e F \d\e Y' ); ?></span>
                    <span class="posted">Postado por <?php the_author_posts_link(); ?></span>
                </p>
                <div style="clear: both;">&nbsp;</div>
                <div class="entry">
                    <?php the_excerpt(); ?>
                    <p class="links">
                        <a href="<?php the_permalink(); ?>" title="Leia mais" class="more">Leia Mais</a>
                    </p>
                </div>
            </div>
            <?php } ?>
            <?php /*posts_nav_link( ' | ', 'Pagina Anterior', 'Proxima Pagina' ); */?>
            <?php pw_paginate_links(); ?>
            <div style="clear: both;">&nbsp;</div>
        </div>
    <?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
