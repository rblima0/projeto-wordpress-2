<?php
$my_recipes = get_posts( array(
        'post_type'         => 'recipe',
        'posts_per_page'    => 8,
        'order'          => 'ASC',
        'orderby'        => 'title'
    )
);
?>

<div id="sidebar">
    <ul>
        <li>
            <h3 class="title-sidebar">Pesquise aqui</h3>
            <?php get_search_form(); ?>
        </li>
        <li>
            <h3 class="title-sidebar">Pratos</h3>
            <ul>
                <?php
                wp_list_categories( array(
                        'title_li'  => null,
                        'taxonomy'  => 'recipe_types'
                    )
                ); ?>
            </ul>
        </li>
        <li>
            <h3 class="title-sidebar">Últimos Pratos</h3>
            <ul>
                <?php
                $first = false;
                foreach ( $my_recipes as $my_recipe ) {
                    echo '<li';
                    if ( !$first ) {
                        $first = true;
                        echo ' class="first"';
                    }
                    echo '><a href="' . get_permalink( $my_recipe->ID ) . '">' . get_the_title( $my_recipe->ID ) . '</a></li>';
                }
                ?>
            </ul>
        </li>
        <!--<li>
            <h3 class="title-sidebar">Categories</h3>
            <ul>
                <?php /*wp_list_categories( 'title_li=' ); */?>
            </ul>
        </li>-->
    </ul>
</div>
<div style="clear: both;">&nbsp;</div>