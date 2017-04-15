<?php
$q = new WP_Query(array(
        'posts_per_page' => 3,
        'meta_query' => array(
            array(
                'key' => 'pw_model'
            ),
            array(
                'key' => '_thumbnail_id'
            )
        )
    )
);

get_header(); ?>

    <div id="page" class="container">
        <div id="banner"><a href="#"><img src="<?php echo PW_URL_THEME; ?>images/img02.jpg" width="940" height="400"
                                          alt=""/></a></div>
        <div id="three-column">
            <?php
            $i = 1;
            while ($q->have_posts()){$q->the_post(); global $post; ?>
                <div id="column<?php echo $i; ?>">
                    <p>
                        <a href="<?php the_permalink(); ?>" title="Ver post completo">
                            <?php
                            if (has_post_thumbnail())
                                the_post_thumbnail(array(250, 215));
                            else
                                echo '<img src="' . PW_URL_THEME . 'img/no-image.png" alt="Nenhuma imagem definida" />' ?>
                        </a>
                    </p>
                    <h2><?php the_title(); ?></h2>
                    <h3><?php echo get_post_meta($post->ID, 'pw_model', true); ?></h3>
                    <p><?php the_excerpt(); ?></p>
                </div>
                <?php $i++;
            } ?>
        </div>

        <div id="content">
            <?php
            if (!is_home()) {
                $title = 'Arquivo';
                if (is_search()) {
                    $title = 'Resultados da busca por "' . get_search_query() . '"';
                } else if (is_category()) {
                    $title = 'Categoria: ' . single_cat_title('', false);
                }
                echo "<h1>{$title}</h1>";
            }
            while (have_posts()) {
                the_post();
                global $post; ?>
                <div class="post">
                    <h2 class="title">
                        <?php
                        //the_title(); FORMA RESUMIDA
                        printf('<a href="%1$s" title="%2$s">%2$s</a>',
                            get_permalink(),
                            get_the_title()
                        ); ?>
                    </h2>
                    <p class="meta">
                        <span class="date"><?php echo get_the_date('j \d\e F \d\e Y'); ?></span>
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
            <?php pw_paginate_links(); ?>
            <div style="clear: both;">&nbsp;</div>
        </div>

        <?php get_sidebar(); ?>

    </div>
    </div>

<?php get_footer(); ?>