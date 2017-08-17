<?php

/**
*
*   Plugin Name: Custom Slider
*   Description: Gerencia e insere sliders ao site.
*   Version: 1.0
*   Author: Rodrigo Banci
*   Author URI: http://rodrigobanci.com.br/
*
**/

class KDM_Custom_Slider
{

    static $url;
    static $cpt = 'slide';
    static $tax = 'slider';
    static $prefix = 'kdm_';

    function activation()
    {

    }

    function deactivation()
    {

    }

    function init()
    {
        self::$url = plugins_url( basename( dirname( __FILE__ ) ) ) . '/';

        $imgs = array(
            'slide'     => array( 480, 320 ),
            'slide-min' => array( 100,  75 )
        );
        foreach ( $imgs as $name => $size )
            add_image_size( $name, $size[0], $size[1], true );

        add_filter( 'intermediate_image_sizes', array( 'KDM_Custom_Slider', 'image_sizes' ), 10, 3 );

        add_action( 'init',         array( 'KDM_Custom_Slider', 'register_cpt' ) );

        add_action( 'admin_init',   array( 'KDM_Custom_Slider', 'metabox' ) );
        add_action( 'save_post',    array( 'KDM_Custom_Slider', 'metabox_save' ) );

        add_filter( 'manage_posts_columns', array( 'KDM_Custom_Slider', 'cols' ), 10, 2 );
        add_filter( 'request',              array( 'KDM_Custom_Slider', 'orderby' ) );

        add_action( 'manage_' . self::$cpt . '_posts_custom_column',    array( 'KDM_Custom_Slider', 'cols_content' ) );
        add_filter( 'manage_edit-' . self::$cpt . '_sortable_columns',  array( 'KDM_Custom_Slider', 'cols_sort' ) );

        add_action( 'wp_enqueue_scripts', array( 'KDM_Custom_Slider', 'enqueue' ) );
    }

    function image_sizes( $sizes )
    {
        global $post;

        $post_type = '';
        if ( isset( $_POST[ 'post_id' ] ) ) {
            $post_type = get_post_type( $_POST[ 'post_id' ] );
        } else if ( isset( $post ) && isset( $post->post_parent ) && ( $post->post_parent > 0 ) ) {
            $post_type = get_post_type( $post->post_parent );
        }

        $sizes = array( 'thumbnail', 'medium', 'large' );
        if ( $post_type == self::$cpt )
            $sizes = array( 'slide', 'slide-min' );

        return $sizes;
    }


    function register_cpt()
    {
        $attr = array(
            'public'    => false,
            'show_ui'   => true,
            'supports'  => array( 'title', 'thumbnail', 'page-attributes' ),
            'labels'    => array(
                'name'                  => 'Slides',
                'add_new'               => 'Novo',
                'add_new_item'          => 'Novo Slide',
                'edit_item'             => 'Editar Slide',
                'new_item'              => 'Adicionar Slide',
                'view_item'             => 'Visualizar Slide',
                'search_items'          => 'Pesquisar Slides',
                'not_found'             => 'Nenhum Slide foi encontrado',
                'not_found_in_trash'    => 'Nenhum Slide foi encontrado na lixeira',
                'all_items'             => 'Todos os Slides'
            )
        );
        register_post_type( self::$cpt, $attr );

        $attr = array(
            'public'        => false,
            'show_ui'       => true,
            'hierarchical'  => true,
            'labels'        => array(
                'name'              => 'Sliders',
                'singular_name'     => 'Slider',
                'search_items'      => 'Pesquisar Sliders',
                'all_items'         => 'Todos os Sliders',
                'parent_item'       => 'Slider acima',
                'parent_item_colon' => 'Slider acima:',
                'edit_item'         => 'Editar Sliders',
                'update_item'       => 'Atualizar Slider',
                'add_new_item'      => 'Novo Slider',
                'new_item_name'     => 'Novo Slider',
                'menu_name'         => 'Sliders'
            )
        );
        register_taxonomy( self::$tax, self::$cpt, $attr );

        // flush_rewrite_rules();
    }

    function metabox()
    {
        add_meta_box( 'box_slide', 'Detalhes', array( 'KDM_Custom_Slider', 'metabox_edit' ), self::$cpt, 'normal', 'high' );
    }

    function metabox_edit( $post )
    {
        $field = self::$prefix . 'url';
        $value = get_post_meta( $post->ID, $field, true );
        ?>
        <table class="form-table">
            <tr>
				<th><label for="slide-url">URL</label></th>
                <td><input type="text" name="<?php echo $field; ?>" value="<?php echo $value; ?>" id="slide-url" /></td>
            </tr>
        </table>
        <?php
    }

    function metabox_save( $id )
    {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $id;

        if ( !current_user_can( 'edit_post', $id ) )
            return $id;

        $field = self::$prefix . 'url';
        $old = get_post_meta( $id, $field, true );
        $new = ( isset( $_POST[ $field ] ) ) ? $_POST[ $field ] : '';

        if ( ( $new !== $old ) && $new )
            update_post_meta( $id, $field, $new );
        else if ( !$new && $old )
            delete_post_meta( $id, $field, $old );
    }


    function cols( $cols, $post_type )
    {
        if ( $post_type == self::$cpt ) {
            $cols[ 'url' ]      = 'URL Destino';
            $cols[ 'slider' ]   = 'Slider';
            $cols[ 'order' ]    = 'Ordem';
            $cols[ 'thumb' ]    = 'Imagem';
        }
        return $cols;
    }

    function cols_content( $col )
    {
        global $post;
        switch ( $col ) {
            case 'thumb':
                the_post_thumbnail( 'slide-min' );
                break;
            case 'order':
                echo $post->menu_order;
                break;
            case 'slider':
                $tax = '';
                $terms = get_the_terms( $post->ID, self::$tax );
                foreach ( $terms as $t ) {
                    if ( $tax ) $tax .= ', ';
                    $tax .= $t->name;
                }
                /*
                printf( '<a href="%s">%s</a>',
                    admin_url(
                        sprintf( 'edit-tags.php?action=edit&taxonomy=%s&post_type=%s&tag_ID=%d',
                            self::$tax,
                            self::$cpt,
                            $t->term_id
                        )
                    ),
                    $t->name
                ); */
                echo $tax;
                break;
            default:
                echo get_post_meta( $post->ID, self::$prefix . $col, true );
                break;
        }
    }

    function cols_sort( $cols )
    {
        $cols[ 'order' ] = 'ordem';
        return $cols;
    }

    function orderby( $vars )
    {
        if ( isset( $vars[ 'orderby' ] ) && ( $vars[ 'orderby' ] == 'ordem' ) )
            $vars[ 'orderby' ] = 'menu_order';

        return $vars;
    }


    function enqueue()
    {
        if ( is_admin() ) return;

        wp_enqueue_script( 'cs-cycle', self::$url . 'cycle.lite.js', array( 'jquery' ), null, true );
        wp_enqueue_script( 'cs-script', self::$url . 'script.js', array(), null, true );

        wp_enqueue_style( 'cs-style', self::$url . 'style.css', array(), null, 'screen' );
    }

    function slider( $args )
    {
        $q = array(
            'post_type'     => self::$cpt,
            'numberposts'   => $args[ 'count' ],
            'orderby'       => 'menu_order',
            'order'         => 'ASC'
        );

        if ( $args[ 'slider' ] )
            $q[ self::$tax ] = $args[ 'slider' ];

        $slides = get_posts( $q );
        if ( is_array( $slides ) && count( $slides ) ) { ?>
        <div class="custom-slider-container">
            <ul id="custom-slider" class="custom-slider">
                <?php
                foreach( $slides as $slide ) {
                    if ( !has_post_thumbnail( $slide->ID ) ) continue;

                    $thumb = get_the_post_thumbnail( $slide->ID, 'slide' );
                    $url = get_post_meta( $slide->ID, self::$prefix . 'url', true );

                    if ( $url )
                        $html = '<li><a href="' . $url . '" title="' . esc_attr( $slide->post_title ) . '">' . $thumb . '</a></li>';
                    else
                        $html = "<li>{$thumb}</li>";

                    echo $html;
                } ?>
            </ul>
            <span id="custom-slider-prev">Anterior</span>
            <span id="custom-slider-next">Próximo</span>
        </div>
        <?php }
    }


}

/*
 *
register_activation_hook( __FILE__, array( 'KDM_Subscriber_Comments', 'activation' ) );
register_deactivation_hook( dirname( __FILE__ ) . '\index.php', array( 'KDM_Subscriber_Comments', 'deactivation' ) );
*/

add_action( 'plugins_loaded', array( 'KDM_Custom_Slider', 'init' ) );

function custom_slider( $args=array() )
{
    $defaults = array(
        'slider'=> '', // taxonomy
        'count' => 3 // quantidade de slides
    );
    $args = wp_parse_args( $args, $defaults );
    KDM_Custom_Slider::slider( $args );
}

// está sendo incorporado no header.php do tema
// além dos links, inserir textos outros efeitos de js

?>
