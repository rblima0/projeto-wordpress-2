<?php

/**
 *
 * Plugin Name: Custom Post Ratings
 * Description: Permite aos usuários cadastrados votar nos posts
 * Version: 1.0
 * Author: Rodrigo Banci
 * Author URI: http://rodrigobanci.com.br
 * 
 */

class KDM_Ratings
{

    function activation()
    {
    }

    function deactivation()
    {
    }

    function setup()
    {
        add_action( 'wp',                   array( 'KDM_Ratings', 'init' ) );
        add_action( 'wp_ajax_vote',         array( 'KDM_Ratings', 'vote' ) );
        
        add_shortcode( 'ratings',           array( 'KDM_Ratings', 'shortcode' ) );
        
        add_action( 'widgets_init',         array( 'KDM_Ratings', 'widget' ) );
        
        // Admin Bar
        add_action( 'admin_menu',           array( 'KDM_Ratings', 'admin_menu' ) );
        //add_action( 'admin_bar_menu',       array( 'KDM_Ratings', 'admin_bar' ), 250 );
        
        // WP_Screen
        add_filter( 'screen_settings',      array( 'KDM_Ratings', 'screen_options' ) );
        
        // Personalização do Dashboard
        add_action( 'wp_dashboard_setup',   array( 'KDM_Ratings', 'dashboard' ) );
        // remove_action( 'welcome_panel',     'wp_welcome_panel' );
        
        // Pointers
        add_action( 'wp_ajax_pointer_hide', array( 'KDM_Ratings', 'pointer_hide' ) );
        add_action( 'admin_enqueue_scripts',array( 'KDM_Ratings', 'admin_scripts' ) );
        
        // oEmbed
        wp_embed_register_handler( 'ted', '#http://www\.ted\.com/(.*)#i', array( 'KDM_Ratings', 'embed' ) );
        add_filter( 'embed_defaults',       array( 'KDM_Ratings', 'embed_defaults' ) );
        add_action( 'init',                 array( 'KDM_Ratings', 'init_embed' ) );
    }

    function init()
    {
        if ( is_user_logged_in() ) {
            // add_filter( 'the_content', array( 'KDM_Ratings', 'stars' ), 1, 1 );

            $url = plugins_url( CPR_DIR ) . '/';
            wp_register_script( 'cpr-script', $url . 'js/post-ratings.js', array( 'jquery' ), null );
            wp_register_style( 'cpr-style', $url . 'css/post-ratings.css', array(), null, 'screen' );

            if ( is_single() ) {
                wp_enqueue_style( 'cpr-style' );
                wp_enqueue_script( 'cpr-script' );
            }
        }
    }

    function stars( $content, $opt=null )
    {
        if ( is_admin() || !is_single() )
            return $content;

        global $post;
        $value = (float) get_post_meta($post->ID, 'cpr-avg', true);
        $int_value = round( $value );

        $html = '<p class="custom-post-ratings">';
            
        if ( isset( $opt[ 'title' ] ) )
            $html .= $opt[ 'title' ];
        
        $html .= '<span id="post-rating" class="ref-' . $post->ID . '">';

        for ( $i = 1; $i <= 5; $i++ ) {
            $html .= '<span id="star-' . $i . '"';

            if ( $int_value >= $i )
                $html .= ' class="on"';

            $html .= '></span>';
        }

        if ( isset( $opt[ 'avg' ] ) && $opt[ 'avg' ] )
            $html .= ' Média: <em id="cpr-avg">' . number_format( $value, 2, ',', '' ) . '</em>';

        if ( isset( $opt[ 'votes' ] ) && $opt[ 'votes' ] ) {
            $votes = (int) get_post_meta( $post->ID, 'cpr-votes', true );
            $html .= ' Total de votos: <em id="cpr-votes">' . $votes . '</em>';
        }

        $html .= '</span></p>';
        return $html . $content;
    }

    function vote() 
    {
        $rating = (int) $_POST[ 'rating' ];
        $post_id = (int) $_POST[ 'post_id' ];
        $votes = ( isset( $_COOKIE[ 'cpr-votes' ] ) ) ? json_decode( $_COOKIE[ 'cpr-votes' ] ) : array();

        if ( !$rating || !$post_id ) {
            $r = array(
                'error' => true,
                'msg'   => 'Voto não processado...'
            );
        } else if ( in_array( $post_id, $votes ) ) {
            $r = array(
                'error' => true,
                'msg'   => 'Seu voto já foi computado!'
            );
        } else {
            array_push( $votes, $post_id );
            setcookie( 'cpr-votes', json_encode( $votes ), time()+60*60*24, COOKIEPATH, COOKIE_DOMAIN, false );
            
            $count = (int) get_post_meta( $post_id, 'cpr-votes', true );
            $count++;
            
            $total = (int) get_post_meta( $post_id, 'cpr-total', true );
            $total += $rating;
            
            $avg = number_format( $total/$count, 2 );
            
            update_metadata( 'post', $post_id, 'cpr-avg', $avg );
            update_metadata( 'post', $post_id, 'cpr-votes', $count );
            update_metadata( 'post', $post_id, 'cpr-total', $total );

            $r = array(
                'error' => false,
                'msg'   => 'Voto computado com sucesso!',
                'avg'   => $avg,
                'stars' => (string) round( $avg ),
                'votes' => (string) $count
            );
        }

        echo json_encode( $r );
        die();
    }
    
    function shortcode( $atts, $content )
    {
        $a = shortcode_atts(
            array(
                'votos' => false,
                'media' => false
            ),
            $atts
        );
        
        $opt = array(
            'title' => $content,
            'avg'   => $a[ 'media' ],
            'votes' => $a[ 'votos' ]
        );
        return self::stars( null, $opt );
    }
    
    function widget()
    {
        register_widget( 'KDM_Ratings_Widget' );
    }
    
    // Admin Bar
    
    function admin_menu()
    {
        $admin_page = add_menu_page( 'Post Ratings', 'Post Ratings', 'administrator', 'cpr-menu', array( 'KDM_Ratings', 'admin_page' ) );
        add_action( 'load-' . $admin_page, array( 'KDM_Ratings', 'screen' ) );
    }
    
    function admin_page()
    {
        echo '<h2>Post Ratings</h2>'
        . '<p>Para inserir o formulário de votação em seus posts, utilize o shortcode '
        . '<b>[ratings votos="" media=""]Título[/ratings]</b> no local pretendido.</p>'
        . '<p>Lembre-se que somente usuários logados terão a permissão de voto.</p>';
    }
       
    function admin_bar()
    {
        global $wp_admin_bar;
        
        $wp_admin_bar->remove_node( 'wp-logo' );
        $wp_admin_bar->remove_node( 'comments' );
        $wp_admin_bar->remove_node( 'view-site' );
        $wp_admin_bar->remove_node( 'new-content' );
        
        $wp_admin_bar->add_node(
            array(
                'id'    => 'cpr-admin-bar',
                'title' => 'Post Ratings',
                'href'  => admin_url( 'admin.php?page=cpr-menu' )
            )
        );
        $wp_admin_bar->add_node(
            array(
                'parent'=> 'cpr-admin-bar',
                'id'    => 'cpr-subitem',
                'title' => 'Exemplo',
                'href'  => '#'
            )
        );
    }
    
    // WP_Screen
    
    function screen()
    {
        $s = get_current_screen();
        
        $s->add_help_tab(
            array(
                'id'        => 'cpr-tab1',
                'title'     => 'Aba #1',
                'content'   => 'Siga as intruções da pagina...'
            )
        );
        $s->add_help_tab(
            array(
                'id'        => 'cpr-tab2',
                'title'     => 'Aba #2',
                'callback'  => array( 'KDM_Ratings', 'help_tab' )
            )
        );
        
        $content = '<p><strong>Guia de ajuda</strong></p><p>Conteúdo de exemplo</p>';
        $s->set_help_sidebar( $content );
    }
    
    function help_tab()
    {
        echo 'Conteúdo da aba #2';
    }
    
    function screen_options()
    {
        return 'Minhas opções';
    }
    
    // Personalização do Dashboard

    function dashboard()
    {
        global $wp_meta_boxes;
        $wp_meta_boxes[ 'dashboard' ] = array();
        
        wp_add_dashboard_widget( 'cpr-dashboard', 'Post Ratings', array( 'KDM_Ratings', 'metabox' ), array( 'KDM_Ratings', 'metabox_save' ) );
    }
    
    function metabox()
    {
        echo 'Durante a edição de seus conteúdos lembre-se de inserir o shortcode de votação.';
    }
    
    function metabox_save()
    {
        if ( !empty( $_POST ) ) {
            // custom actions
        }
        
        echo '<p><label>Opção personalizada</label> '
         . '<input type="text" name="_text" /></p>';
    }
    
    // Pointers
    
    function admin_scripts()
    {
        wp_enqueue_style( 'wp-pointer' );
        wp_enqueue_script( 'wp-pointer' );
        
        add_action( 'admin_print_footer_scripts', array( 'KDM_Ratings', 'custom_script' ) );
    }
    
    function custom_script()
    {
        $u = wp_get_current_user();
        $hide = get_user_meta( $u->ID, 'pointer-hide', true );
        if ( !$hide ) {
            $content  = '<h3>Precisa de ajuda?</h3>';
            $content .= '<p>Acesse a tela de suporte do plugin para maiores detalhes</p>';
            ?>
            <script type="text/javascript">
            jQuery( document ).ready( function($){
                $( '#cpr-dashboard' ).pointer({
                    content: '<?php echo $content; ?>',
                    position: {
                        edge: 'top',
                        align: 'center'
                    },
                    close: function(){
                        $.post(
                            'admin-ajax.php',
                            {
                                action: 'pointer_hide'
                            }
                        );
                    }
                }).pointer( 'open' );
            });
            </script>
            <?php
        }
    }
    
    function pointer_hide()
    {
        $u = wp_get_current_user();
        update_user_meta( $u->ID, 'pointer-hide', true );
        exit;
    }
    
    // oEmbed
    
    function embed( $m )
    {
        require_once( ABSPATH . WPINC . '/class-oembed.php' );
        $oembed = _wp_oembed_get_object();
        return $oembed->get_html( $m[0] );
    }
    
    function embed_defaults()
    {
        return array(
            'width' => 712,
            'height'=> 534
        );
    }
    
    function init_embed()
    {
        wp_oembed_add_provider( '#http://(www.)?ted\.com/.*#i', 'http://www.ted.com/talks/oembed.{format}', true );
        /*
        $url = 'http://www.ted.com/talks/tim_berners_lee_on_the_next_web.html';
        require_once( ABSPATH . WPINC . '/class-oembed.php' );
        $oembed = _wp_oembed_get_object();
        
        $provider = 'http://www.ted.com/talks/oembed.json';
        $provider = add_query_arg( 'url', $url, $provider );
        $provider = add_query_arg( 'width', 480, $provider );
        $provider = add_query_arg( 'height', 360, $provider );
        var_dump( $oembed->fetch( $provider, $url ) );
        
        var_dump( wp_oembed_get( $url, array( 'width' => 480, 'height' => 360 ) ) );
        exit;
        */
    }
    
}

define( 'CPR_DIR',   basename( dirname( __FILE__ ) ) );
define( 'CPR_PATH',  WP_PLUGIN_DIR . '/' . CPR_DIR . '/' );

require_once( CPR_PATH . 'widget.php' );

//register_activation_hook( __FILE__, array( 'KDM_Ratings', 'activation' ) );
//register_deactivation_hook( __FILE__, array( 'KDM_Ratings', 'deactivation' ) );

add_action( 'plugins_loaded', array( 'KDM_Ratings', 'setup' ) );

?>