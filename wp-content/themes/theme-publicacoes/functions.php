<?php
define( 'PW_URL',           get_home_url() . '/' );
define( 'PW_URL_THEME',     get_bloginfo( 'template_url' ) . '/' );
define( 'PW_SITE_NAME',     get_bloginfo( 'title' ) );

add_action('after_setup_theme', 'pw_theme_setup');

function pw_theme_setup() {
    register_nav_menu('menu-header', 'Menu do Cabecalho');

    add_filter( 'show_admin_bar', '__return_false' );

    add_action('widgets_init', 'pw_register_sidebars');

    /*//Filtro para criar propria customização das categorias
    add_filter( 'wp_list_categories', 'pw_format_list_categories', 10, 2 );*/

    // FILTRO PARA INFORMAR QUANTIDADE DE LETRAS DO RESUMO
    add_filter( 'excerpt_length', 'custom_excerpt_length' );

    //FILTRO PARA COLOCAR DESCRIÇÃO NO FIM DO RESUMO
    add_filter( 'excerpt_more', 'custom_excerpt_more' );

}

function pw_register_sidebars(){
    register_sidebar(array(
        'id'    => 'sidebar_main',
        'name'  => 'Barra Lateral',
        'description'   => 'Área de Widgets'
    ));
}

/*//Função para tratar as categorias
function pw_format_list_categories($output, $args){
    // Tratamento do resultado
    // Sendo $output a lista gerada e $args os parâmetros utilizados
}*/

function custom_excerpt_length( $length ) {
    return 10;
}

function custom_excerpt_more( $more ) {
    return ' [Leia mais...]';
}

function pw_paginate_links()
{
    global $wp_query;
    $page_total = $wp_query->max_num_pages;
    $page_current = $wp_query->get( 'paged' );

    if ( $page_total <= 1 )
        return false;

    if ( !$page_current )
        $page_current = 1;

    echo '<nav>Página ' . $page_current . ' de ' . $page_total . ': ' .
        paginate_links( array(
                'base'      => str_replace( $page_total, '%#%', esc_url(get_pagenum_link( $page_total ) )),
                'format'    => '?paged=%#%',
                'current'   => max( 1, $page_current ),
                'total'     => $page_total,
                'prev_text' => false,
                'next_text' => false,
                'end_size'  => 1,
                'mid_size'  => 2,
                'before_page_number' => '<span class="screen-reader-text">'.$translated.' </span>'
            )
        ) . '</nav>';
}

function pw_list_comments( $comment, $args, $depth ){
    if ( $comment->comment_type == ''){
        $comment_id = (int) $comment->comment_ID;
        $author = get_comment_author_link();

        $content = get_comment_text();
        $avatar = get_avatar( $comment, 80 );
        $date_time = get_comment_date( 'd/m/Y H:i' );

        $reply = get_comment_reply_link(
            array_merge( $args, array(
                    'reply_text'    => 'Responder',
                    'respond_id'    => 'responder',
                    'depth'         => $depth
                )
            )
        );

        echo '<li id="li-comment-' . $comment_id . '">';
        echo '<div id="comment-' . $comment_id . '">';

        echo $avatar . $author . '<br />Enviado em ' . $date_time . ' | ' . $reply;
        echo "<div class=\"comment-text\">{$content}</div>";

        echo '</div>';
    }
}

?>