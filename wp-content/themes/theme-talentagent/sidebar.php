<?php
$categories = get_categories();
$entries  = get_posts();
?>

<div id="sidebar">
    <?php
    get_search_form();
    if(is_active_sidebar('sidebar_main')){
        dynamic_sidebar('sidebar_main');
    } else { ?>
        <ul class="style1">
            <li>
                <h2>Categorias</h2>
                <ul>
                    <?php
                    foreach($categories as $categorie){
                        printf('<li><a href="%1$s" title="%2$s">%2$s</a></li>',
                            get_category_link($categorie->term_id),
                            $categorie->name
                        );
                    }
                    ?>
                </ul>
            </li>
            <li>
                <h2>Últimos Posts</h2>
                <ul>
                    <?php
                    foreach ($entries as $entry){
                        printf('<li><a href="%1$s" title="%2$s">%2$s</a></li>',
                            get_permalink($entry->ID),
                            $entry->post_title
                        );
                    } ?>
                </ul>
            </li>
            <li>
                <h2>Arquivos</h2>
                <ul>
                    <?php wp_get_archives(); ?>
                </ul>
            </li>
        </ul>
    <?php } ?>
</div>
<div style="clear: both;">&nbsp;</div>
</div>