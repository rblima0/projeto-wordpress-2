<?php

/**
 *
 * Configura o comportamento, as propriedades base de um Tema
 *
 */
class Main
{

    private static $avatars;

    /**
     *
     * Inicializador dispara os gatilhos necessários
     *
     */
    public static function init()
    {
        self::supported();

        if ( !defined( 'ROOT_SUPPORTED' ) ) {
            self::unsupported( 'wp_head' );
            self::unsupported( 'admin_bar' );
            self::unsupported( 'update_core' );
            self::unsupported( 'update_plugins' );
        }

        self::custom_login();
        self::custom_dashboard();

        remove_shortcode( 'gallery' );

        Code::add(
            'gallery',
            array( 'Code', 'gallery' ),
            array(
                array(
                    'name'  => 'ids',
                    'req'   => true
                ),
                array(
                    'name'  => 'orderby',
                    'std'   => null
                )
            )
        );
    }

    /**
     *
     * Suporta recursos ao Tema
     *
     */
    private static function supported()
    {
        add_post_type_support( 'page', 'excerpt' );

        add_theme_support( 'post-thumbnails' );

        do_action( 'root_supported' );
    }

    /**
     *
     * Remove ações e filtros dispensáveis
     *
     */
    public static function unsupported( $item )
    {
        switch ( $item )
        {
            case 'wp_head':
                remove_action( 'wp_head', 'wp_generator' );
                remove_action( 'wp_head', 'rsd_link' );
                remove_action( 'wp_head', 'wlwmanifest_link' );
                remove_action( 'wp_head', 'index_rel_link' );
                remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
                remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
                remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
                remove_action( 'wp_head', 'feed_links', 2 );
                remove_action( 'wp_head', 'feed_links_extra', 3 );
                break;
            case 'admin_bar':
                add_filter( 'show_admin_bar', '__return_false' );
                break;
            case 'update_core':
                add_filter( 'pre_site_transient_update_core', '__return_null' );
                break;
            case 'update_plugins':
                add_filter( 'pre_site_transient_update_plugins', '__return_null' );
                break;
        }

        do_action( 'root_unsupported' );
    }

    /**
     *
     * Personalização da tela de login
     *
     */
    private static function custom_login()
    {
        add_action( 'login_head',           array( 'Main', 'login_image' ) );
        add_filter( 'login_url',            create_function( null, 'return "' . ROOT_URL . 'login/";' ) );
        add_filter( 'login_headerurl',      create_function( null, 'return "' . ROOT_URL . '";' ) );
        add_filter( 'login_headertitle',    create_function( null, 'return "' . SITE_NAME . '";' ) );
    }

    /**
     *
     * Define a screenshot como título da tela de login
     * Caso a screenshot não exista, exibe o título do site
     *
     */
    public static function login_image()
    {
        if ( !file_exists( PATH_THEME . 'screenshot.png' ) ) {
            echo '<style>
            .login h1 a {
                content: "' . SITE_NAME . '";
                background: none;
                overflow: none;
                text-indent: 0;
                text-align: center;
                text-decoration: none;
                width: 300px;
                height: auto;
                padding-top: 15px;
                line-height: 1.1em;
                margin: -10px 0 20px 14px;
            }</style>';
        } else {
            /**
             * h1 { width: 320px;  }
             * form { margin-left: 8px; }
             *
             * width-max: 328px
             * margin-left: ( width-max - width ) / 2
             *
             */
            echo '<style>
            .login h1 a {
                background: url( "' . THEME_URL . 'screenshot.png" ) no-repeat center;
                background-size: 100%;
                width: 300px;
                height: 225px;
                margin: -20px 0 25px 14px;
            }</style>';
        }
    }

    /**
     *
     * Personalização do Dashboard
     * Oculta os textos de rodapé
     *
     */
    private static function custom_dashboard()
    {
        add_filter( 'admin_footer_text',    '__return_null' );
        add_filter( 'update_footer',        create_function( null, 'return "' . SITE_NAME . ' ' . THEME_VERSION . '";' ), 15 );
    }

    /**
     *
     * Adiciona novos formatos de imagens
     *
     * @param array $sizes Lista de formatos
     * array( name, width, height, crop )
     *
     */
    public static function add_sizes( $sizes )
    {
        foreach( $sizes as $s ) {
            if ( !isset( $s[ 'crop' ] ) )
                $s[ 'crop' ] = true;

            add_image_size( $s[ 'name' ], $s[ 'width' ], $s[ 'height' ], $s[ 'crop' ] );
        }
    }

    /**
     *
     * Adiciona avatares personalizados
     *
     * @param array $avatars Lista de avatares
     *
     */
    public static function add_avatar( $avatars )
    {
        if ( !isset( self::$avatars ) )
            self::$avatars = array();

        self::$avatars = array_merge( self::$avatars, $avatars );

        add_filter( 'avatar_defaults', array( 'Main', 'avatar' ) );
    }

    /**
     *
     * Incorpora os avatares ao WordPress
     *
     * @param array $a Avatares atuais
     * @return array Avatares atualizados
     *
     */
    public static function avatar( $a )
    {
        foreach ( self::$avatars as $avatar )
            $a[ $avatar[ 'img' ] ] = $avatar[ 'name' ];

        return $a;
    }

}

?>