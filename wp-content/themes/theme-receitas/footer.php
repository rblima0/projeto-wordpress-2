<?php
$easies = get_posts( array(
        'post_type'         => 'recipe',
        'posts_per_page'    => 7,
        'meta_key'          => 'pw_level',
        'meta_value'        => 'easy'
    )
);

$lights = get_posts( array(
        'post_type'         => 'recipe',
        'posts_per_page'    => 7,
        'meta_query'        => array(
            array(
                'key'       => 'pw_kcal',
                'value'     => 1000,
                'compare'   => '<=',
                'type'      => 'NUMERIC'
            )
        )
    )
);
?>

<div id="footer-content-wrapper">
    <div id="footer-content">
        <div id="fbox1">
            <h2>Pratos</h2>
            <ul class="style1">
                <?php
                wp_list_categories( array(
                        'title_li'  => null,
                        'taxonomy'  => 'recipe_types'
                    )
                ); ?>
            </ul>
        </div>
        <div id="fbox2">
            <h2>Cardápio Light</h2>
            <ul class="style1">
                <?php
                $first = false;
                foreach($lights as $light){
                    echo '<li';
                    if(!$first){
                        $first = true;
                        echo 'class="first"';
                    }
                    echo '><a href="' . get_permalink($light->ID) . '">' . get_the_title($light->ID) . '<a></li>';
                }
                ?>
            </ul>
        </div>
        <div id="fbox3">
            <h2>Receitas Simples</h2>
            <ul class="style1">
                <?php
                $first = false;
                foreach($easies as $easy){
                    echo '<li';
                    if(!$first){
                        $first = true;
                        echo 'class="first"';
                    }
                    echo '><a href="' . get_permalink($easy->ID) . '">' . get_the_title($easy->ID) . '<a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
</div>

<div id="footer">
    <p>NomeDoSite.com.br | Todos os direitos reservados.</p>
</div>
<?php wp_footer(); ?>
</body>
</html>