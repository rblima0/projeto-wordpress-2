<?php
define('PW_URL', get_home_url(). '/');
define('PW_URL_THEME', get_bloginfo('template_url').'/');
define('PW_SITE_NAME', get_bloginfo('title'));
define('PW_SITE_DESCRIPTION', get_bloginfo('description'));

add_action('after_setup_theme', 'pw_theme_setup');

function pw_theme_setup(){

    register_nav_menu('menu-header', 'Menu do Cabeçalho');

    add_filter( 'show_admin_bar', '__return_false' );

    add_action('widgets_init', 'pw_register_sidebars');

    add_action('admin_init', 'pw_admin_init');

    add_action('save_post', 'pw_box_model_save');

    add_theme_support( 'post-thumbnails' );

    remove_shortcode( 'gallery' );
    add_shortcode( 'gallery', 'pw_code_gallery' );

    add_image_size( 'model', 250, 215, true );
    add_image_size( 'gallery', 195, 150, true );

    add_filter( 'intermediate_image_sizes', 'pw_image_sizes', 10, 3 );

    add_filter( 'wp_mail_content_type', 'pw_mail_content_type' );

    add_filter( 'excerpt_length', 'custom_excerpt_length' );

}

function pw_admin_init(){
    add_meta_box('box_model', 'Informações da Modelo', 'pw_box_model', 'post', 'normal', 'high');
}

function pw_box_model(){
    global $post; ?>
    <table>
        <tr>
            <th>Modelo</th>
            <td><input type="text" name="pw_model" value="<?php echo get_post_meta( $post->ID, 'pw_model', true ); ?>"/></td>
        </tr>
    </table>
<?php }

function pw_box_model_save($id){
    if(isset($_POST['pw_model'])){
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
            return $id;

        update_post_meta($id, 'pw_model', $_POST['pw_model']);
    }
}

function pw_register_sidebars(){
    register_sidebar(array(
        'id'    => 'sidebar_main',
        'name'  => 'Barra Lateral',
        'description'   => 'Área de Widgets'
    ));
}

function pw_paginate_links(){
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

function pw_code_gallery( $atts ){
    $atts = shortcode_atts( array(
        'orderby'   => 'menu_order',
        'order'     => 'ASC',
        'ids'       => false
    ), $atts );

    if ( $atts[ 'ids' ] ) {
        $atts[ 'include' ] = $atts[ 'ids' ];
        unset( $atts[ 'ids' ] );
    }

    global $post;
    $args = array(
        'post_type'         => 'attachment',
        'post_mime_type'    => 'image',
        'post_parent'       => $post->ID
    );
    $args = array_merge( $args, $atts );
    $images = get_children( $args );

    $html = '';
    if ( is_array( $images ) && count( $images ) > 0 ):
        $html = '<ul>';

        foreach ( $images as $image ):
            if ( isset( $image->ID ) && isset( $image->post_title ) ) {
                $alt = get_post_meta( $image->ID, '_wp_attachment_image_alt', true );
                if ( !$alt ) $alt = $image->post_title;
                list( $src ) = wp_get_attachment_image_src( $image->ID, 'thumbnail' );
                $url = wp_get_attachment_url( $image->ID );

                $html .= '<li><a rel="gallery" href="' . $url .
                    '" title="' . esc_attr( $image->post_title ) .
                    '"><img alt="' . esc_attr( $alt ) . '" src="' . $src . '"></a></li>';
            }
        endforeach;

        $html .= '</ul>';
    endif;

    return $html;
}

function pw_image_sizes( $sizes ){
    global $post;
    $post_type = '';
    if ( isset( $_POST[ 'post_id' ] ) ) {
        $post_type = get_post_type( $_POST[ 'post_id' ] );
    } else if ( isset( $post ) && isset( $post->post_parent ) && ( $post->post_parent > 0 ) ) {
        // A variável global $post contém os dados do anexo e não do post a qual ele pertence
        $post_type = get_post_type( $post->post_parent );
    }

    $sizes = array( 'thumbnail', 'medium', 'large' );

    switch( $post_type ):
        case 'post':
            array_push( $sizes, 'model', 'gallery' );
            break;
        default:
            array_push( $sizes, 'gallery' );
            break;
    endswitch;

    return $sizes;
}

function pw_mail_content_type(){
    return "text/html";
}

function custom_excerpt_length( $length ) {
    return 15;
}

?>