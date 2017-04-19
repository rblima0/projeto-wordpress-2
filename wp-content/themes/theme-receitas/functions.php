<?php
define('PW_URL', get_home_url(). '/');
define('PW_URL_THEME', get_bloginfo('template_url').'/');
define('PW_SITE_NAME', get_bloginfo('title'));
define('PW_SITE_DESCRIPTION', get_bloginfo('description'));

add_action('after_setup_theme', 'pw_theme_setup');

function pw_theme_setup(){

    add_theme_support( 'post-thumbnails' );

    add_filter( 'show_admin_bar', '__return_false' );
    add_filter( 'wp_title', 'pw_title', 10, 2 );

    register_nav_menu( 'menu-header', 'Menu do Cabeçalho' );

    add_action( 'wp_enqueue_scripts', 'pw_register_scripts_and_styles' );
    add_action( 'wp_enqueue_scripts', 'pw_enqueue_scripts_and_styles' );

    add_action('widgets_init', 'pw_register_sidebars');

    add_action( 'admin_menu', 'pw_custom_menu' );

    add_action( 'init', 'pw_init' );
    add_action( 'admin_init',   'pw_admin_init' );
    add_action( 'save_post',    'pw_box_recipe_save' );

    add_image_size( 'recipe', 300, 150, true );
    add_filter( 'intermediate_image_sizes', 'pw_image_sizes', 10, 3 );

    add_filter('manage_posts_columns', 'pw_custom_cols', 10, 2);
    add_action('manage_recipe_posts_custom_column', 'pw_custom_cols_content');

    //adicionando ordenação para os resultados - VIEW
    add_filter( 'manage_edit-recipe_sortable_columns', 'pw_sort_cols' );
    //adicionando ordenação para os resultados - ACTION
    add_filter( 'request', 'pw_column_orderby' );

    //filtro para selecionar a ordenação - VIEW
    add_action('restrict_manage_posts','pw_restrict_manage_posts');
    //filtro para selecionar a ordenação - ACTION
    add_filter( 'request', 'pw_restrict_filter' );

    add_filter( 'pre_get_posts', 'pw_pre_get_posts' );

}

function pw_register_sidebars(){
    register_sidebar(array(
        'id'    => 'sidebar_main',
        'name'  => 'Barra Lateral',
        'description'   => 'Área de Widgets'
    ));
}

function pw_custom_menu(){
    add_menu_page( 'Página de opções', 'Opções do Tema', 'manage_options', 'custom-options', 'pw_custom_options_page' );
}

function pw_register_scripts_and_styles(){
    wp_register_style( 'talent', PW_URL_THEME . 'assets/css/default.css', false, '', 'screen' );
    wp_register_style( 'talent-font1', 'http://fonts.googleapis.com/css?family=Oswald:400,300', false, '', 'screen' );
    wp_register_style( 'talent-font2', 'http://fonts.googleapis.com/css?family=Abel|Satisfy', false, '', 'screen' );
}

function pw_enqueue_scripts_and_styles(){
    wp_enqueue_style( 'talent' );
    wp_enqueue_style( 'talent-font1' );
    wp_enqueue_style( 'talent-font2' );

    wp_enqueue_script( 'comment-reply' );
}

function pw_title($title, $sep){
    if($title && !strpos($title, $sep)){
        $title .= $sep;
    }

    $title .= PW_SITE_NAME . " | " . PW_SITE_DESCRIPTION;
    return $title;
}

function pw_paginate_links()
{
    global $wp_query;
    $page_tot = $wp_query->max_num_pages;
    $page_cur = $wp_query->get( 'paged' );

    if ( $page_tot <= 1 )
        return false;

    if ( !$page_cur )
        $page_cur = 1;

    echo '<nav class="pagination">Página ' . $page_cur . ' de ' . $page_tot . ': ' .
        paginate_links( array(
                'base'      => str_replace( $page_tot, '%#%', get_pagenum_link( $page_tot ) ),
                'format'    => '?paged=%#%',
                'current'   => max( 1, $page_cur ),
                'total'     => $page_tot,
                'prev_next' => false,
                'end_size'  => 1,
                'mid_size'  => 2
            )
        ) . '</nav>';
}

function pw_custom_options_page(){
    ?>
    <div class="wrap">
        <h1>Opções do Tema</h1>

        <?php
        if ( !empty( $_POST ) ) {
            if ( !check_admin_referer( 'update_option', 'pw_admin_nonce' ) )
                wp_die( 'Trapaceando, é?!' );

            $home_recipe = (int) $_POST[ 'pw_home_recipe' ];
            update_option( 'pw_home_recipe', $home_recipe );
            echo '<div class="updated"><p>Opção atualizada com sucesso!</p></div>';
        } else {
            $home_recipe = (int) get_option( 'pw_home_recipe' );
        } ?>

        <form method="post" action="<?php echo admin_url( 'admin.php?page=custom-options' ); ?>">
            <?php wp_nonce_field( 'update_option', 'pw_admin_nonce' ); ?>
            <table class="form-table">
                <tr>
                    <th>Receitas na Home</th>
                    <td>
                        <select name="pw_home_recipe">
                            <option>Escolha....</option>
                            <?php
                            for ( $i=3; $i<=12; $i+=3 ) {
                                echo '<option value="' . $i . '"';

                                if ( $i == $home_recipe )
                                    echo ' selected="true"';

                                echo ">{$i}</option>";
                            } ?>
                        </select>
                    </td>
                </tr>
            </table>
            <p><input type="submit" value="Atualizar opções" class="button-primary"></p>
        </form></div>
    <?php
}

function pw_init()
{
    $attr = array(
        'public'        => true,
        'has_archive'   => true,
        'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
        'rewrite'       => array( 'slug' => 'receitas' ),
        'labels'        => array(
            'name'                  => 'Receitas',
            'add_new'               => 'Adicionar receita',
            'add_new_item'          => 'Adicionar nova receita',
            'edit_item'             => 'Editar receita',
            'new_item'              => 'Nova receita',
            'view_item'             => 'Visualizar receita',
            'search_items'          => 'Pesquisar receitas',
            'not_found'             => 'Nenhuma receita foi encontrada',
            'not_found_in_trash'    => 'Nenhuma receita foi encontrada na lixeira',
            'all_items'             => 'Todas as receitas'
        )
    );
    register_post_type( 'recipe', $attr );

    $attr = array(
        'public'                => true,
        'show_in_nav_menus'     => true,
        'hierarchical'          => true,
        'rewrite'               => array( 'slug' => 'pratos' ),
        'labels'                => array(
            'name'              => 'Prato',
            'singular_name'     => 'Prato',
            'search_items'      => 'Pesquisar pratos',
            'all_items'         => 'Todos os pratos',
            'parent_item'       => 'Prato acima',
            'parent_item_colon' => 'Prato acima:',
            'edit_item'         => 'Editar pratos',
            'update_item'       => 'Atualizar prato',
            'add_new_item'      => 'Adicionar novo prato',
            'new_item_name'     => 'Novo prato',
            'menu_name'         => 'Pratos'
        )
    );
    register_taxonomy( 'recipe_types', 'recipe', $attr );

    $version = '1.0';
    if ( get_option( 'pw_cpt_and_tax_version' ) !== $version ) {
        update_option( 'pw_cpt_and_tax', $version );
        flush_rewrite_rules();
    }
}

function pw_admin_init(){
    add_meta_box( 'box_recipe', 'Detalhes da Receita', 'pw_box_recipe', 'recipe', 'normal', 'high' );
}

function pw_image_sizes( $sizes ){
    global $post;

    $post_type = '';
    if ( isset( $_POST[ 'post_id' ] ) ) {
        $post_type = get_post_type( $_POST[ 'post_id' ] );
    } else if ( isset( $post ) && isset( $post->post_parent ) && ( $post->post_parent > 0 ) ) {
        $post_type = get_post_type( $post->post_parent );
    }

    $sizes = array( 'thumbnail', 'medium', 'large', 'full' );

    if ( $post_type == 'recipe' )
        array_push( $sizes, 'recipe' );

    return $sizes;
}

function pw_box_recipe(){
    global $post; ?>
    <table class="form-table">
        <tr>
            <th>Dificuldade</th>
            <td>
                <select name="pw_level">
                    <option>Escolha....</option>
                    <?php
                    $levels = get_levels();
                    $l = get_post_meta( $post->ID, 'pw_level', true );
                    foreach ( $levels as $level => $label ) {
                        echo '<option value="' . $level . '"';

                        if ( $l == $level )
                            echo ' selected="true"';

                        echo ">{$label}</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Calorias</th>
            <td><input type="text" name="pw_kcal" value="<?php echo get_post_meta( $post->ID, 'pw_kcal', true ); ?>" /></td>
        </tr>
        <tr>
            <th>Tempo de Preparo</th>
            <td><input type="text" name="pw_time" value="<?php echo get_post_meta( $post->ID, 'pw_time', true ); ?>" /></td>
        </tr>
    </table>
    <?php
}

function get_levels()
{
    return array(
        'easy'     => 'Fácil',
        'medium'   => 'Médio',
        'hard'     => 'Difícil'
    );
}

function pw_box_recipe_save( $id )
{
    if ( isset( $_POST[ 'pw_level' ] ) && isset( $_POST[ 'pw_kcal' ] ) && isset($_POST['pw_time']) ) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $id;

        update_post_meta( $id, 'pw_level', $_POST[ 'pw_level' ] );
        update_post_meta( $id, 'pw_kcal', $_POST[ 'pw_kcal' ] );
        update_post_meta( $id, 'pw_time', $_POST[ 'pw_time' ] );
    }
}

function pw_custom_cols($columns, $post_type){
    if($post_type == 'recipe'){
        $columns['tag'] = 'Pratos';
        $columns['level'] = 'Dificuldade';
        $columns['kcal'] = 'Calorias (Kcal)';
        $columns['time'] = 'Tempo de Preparo';
        $columns['thumb'] = 'Imagem destacada';
    }
    return $columns;
}

function pw_custom_cols_content($column){
    global $post;
    $value = get_post_meta($post->ID, 'pw_' . $column, true);
    switch ($column){
        case 'tag':
            echo get_the_term_list( $post->ID, 'recipe_types', '', ', ' );
            break;
        case 'level':
            $levels = get_levels();
            echo ( isset( $levels[ $value ] ) ) ? $levels[ $value ] : 'Não definido';
            break;
        case 'thumb':
            echo get_the_post_thumbnail( $post->ID, array( 64, 64 ) );
            break;
        default:
            echo $value;
            break;
    }
}

function pw_sort_cols($columns){
    $cols = array(
        'level' => 'dificuldade',
        'kcal'  => 'calorias',
        'time'  => 'tempo de preparo'
    );
    $columns = array_merge( $columns, $cols );
    return $columns;
}

function pw_column_orderby($vars){
    if ( isset( $vars[ 'orderby' ] ) && ( in_array( $vars[ 'orderby' ], array( 'dificuldade', 'calorias', 'tempo de preparo' ) ) ) ) :
        if ( $vars[ 'orderby' ] == 'dificuldade' ) {
            $key = 'pw_level';
            $ord = 'meta_value';
        } else if ($vars[ 'orderby' ] == 'calorias'){
            $key = 'pw_kcal';
            $ord = 'meta_value_num';
        } else {
            $key = 'pw_time';
            $ord = 'meta_value_num';
        }
        $vars = array_merge( $vars, array(
                'meta_key'  => $key,
                'orderby'   => $ord
            )
        );
    endif;
    return $vars;
}

function pw_restrict_manage_posts(){
    $type = (isset($_GET['post_type'])) ? $_GET['post_type']:false;
    if($type == 'recipe'){
        $levels = get_levels();
        $l = (isset($_GET['level'])) ? $_GET['level']:false;
        echo '<select name="level">';
        echo '<option>Todas as Dificuldades</option>';

        foreach($levels as $level => $label){
            echo '<option value="' . $level . '"';
            if($l == $level)
                echo 'selected="true"';
            echo ">{$label}</option>";
        }

        echo '</select>';

        $tag = ( isset( $_GET[ 'group' ] ) ) ? $_GET[ 'group' ] : 0;
        wp_dropdown_categories( array(
                'name'              => 'group',
                'orderby'           => 'title',
                'taxonomy'          => 'recipe_types',
                'selected'          => $tag,
                'show_option_none'  => 'Todos os Pratos'
            )
        );

    }
}

function pw_restrict_filter($vars){
    if ( $_GET[ 'level' ] ) {
        $l = get_levels();
        if ( isset( $l[ $_GET[ 'level' ] ] ) ) {
            $vars[ 'meta_query' ] = array(
                array(
                    'key' => 'pw_level',
                    'value' => $_GET[ 'level' ]
                )
            );
        }
    }

    if ( isset( $_GET[ 'group' ] ) ) {
        $group = (int) $_GET[ 'group' ];
        if ( $group ) {
            $tag = get_term_by( $id, $group, 'recipe_types' );
            if ( isset( $tag->slug ) ) {
                $vars[ 'taxonomy' ] = 'recipe_types';
                $vars[ 'term' ] = $tag->slug;
            }
        }
    }

    return $vars;
}

function pw_pre_get_posts( $q )
{
    if ( $q->is_home ) {
        $posts_per_page = (int) get_option( 'pw_home_recipe' );
        if ( !$posts_per_page ) $posts_per_page = 3;

        $q->set( 'post_type', 'recipe' );
        $q->set( 'posts_per_page', $posts_per_page );
        $q->set( 'meta_key', '_thumbnail_id' );
    }
    return $q;
}

function pw_breadcrumbs($sep = '/'){
    if(is_home()) return false;

    $links = '<span>Você está aqui: </span>';
    $links .= '<a href="' . PW_URL . '" title="' . esc_attr( PW_SITE_NAME ) . '">Home</a>' . $sep;

    if ( is_singular() ) {
        global $post;
        $type = get_post_type( $post->ID );
        switch ( $type ) {
            case 'post':
                $categs = get_the_category( $data );
                if ( count( $categs ) > 1 )
                    $links .= get_the_category_list( ', ', '', $data ) . $sep;
                else if ( isset( $categs[0]->cat_ID ) )
                    $links .= get_category_parents( $categs[0]->cat_ID, true, $sep );

                break;
            case 'page':
                $parent_id = (int) $post->post_parent;
                if ( $parent_id > 0 ) {
                    $ancestors = array_reverse( $post->ancestors );
                    foreach ( $ancestors as $ancestor ) {
                        $title = get_the_title( $ancestor );
                        $links .= '<a href="' . get_permalink( $ancestor ) .
                            '" title="' . esc_attr( $title ) .
                            '">'. $title . '</a>' . $sep;
                    }
                }
                break;
            case 'recipe':
                $type_obj = get_post_type_object( $type );
                $links .= '<a href="' . get_post_type_archive_link( $type ) . '">' . $type_obj->label . '</a>' . $sep .
                '<a href="' . get_post_type_archive_link( $type ) . '">' . get_the_term_list( $post->ID, 'recipe_types', '', ', ' ) . '</a>' . $sep;
                break;
        }
        $links .= get_the_title();
    } else if ( is_category() ) {
        $links .= single_cat_title( '', false );
    } else if ( is_tag() ) {
        $links .= 'Tag "' . single_tag_title( '', false ) . '"';
    } else if ( is_post_type_archive( 'recipe' ) ) {
        $links .= 'Receitas';
    } else if ( is_tax( 'recipe_types' ) ) {
        $links .= 'Prato: ' . single_term_title( '', false );
    } else if ( is_search() ) {
        $links .= 'Resultados da busca';
    } else if ( is_404() ) {
        $links .= 'Página não encontrada...';
    } else {
        $links .= 'Arquivo';
    }
    echo '<nav class="breadcrumbs">' . $links . '</nav>';

}

?>