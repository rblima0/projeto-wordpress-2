<?php

define( 'PW_URL',           get_home_url() . '/' );
define( 'PW_URL_THEME',     get_bloginfo( 'template_url' ) . '/' );
define( 'PW_SITE_NAME',     get_bloginfo( 'title' ) );

add_action( 'after_setup_theme', 'pw_theme_setup' );

function pw_theme_setup()
{
    register_nav_menu( 'menu-header', 'Menu do Cabeçalho' );

    add_action( 'wp_enqueue_scripts', 'pw_register_scripts_and_styles', 1 );
    add_action( 'wp_enqueue_scripts', 'pw_register_styles_hacks', 2 );
    add_action( 'wp_enqueue_scripts', 'pw_enqueue_scripts_and_styles', 3 );

    add_filter( 'show_admin_bar', 'pw_admin_bar' );
    add_filter( 'wp_title', 'pw_title', 10, 2 );

    add_action( 'wp_ajax_get_description',        'pw_get_description' );
    add_action( 'wp_ajax_nopriv_get_description', 'pw_get_description' );
}

function pw_admin_bar()
{
    return false;
}

function pw_register_scripts_and_styles()
{
    wp_register_style( 'be', PW_URL_THEME . 'style.css', false, '', 'screen' );
    wp_register_style( 'be-font', 'http://fonts.googleapis.com/css?family=Oswald', false, '', 'screen' );
    wp_register_style( 'be-ie', PW_URL_THEME . 'ie.css', false, array( 'be' ), 'screen' );

    wp_register_script( 'be', PW_URL_THEME . 'script.js', false, array( 'jquery' ), true );
}

function pw_enqueue_scripts_and_styles()
{
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'thickbox' );
    wp_enqueue_script( 'be' );

    wp_enqueue_style( 'thickbox' );
    wp_enqueue_style( 'be' );
    wp_enqueue_style( 'be-font' );
    wp_enqueue_style( 'be-ie' );
}

function pw_register_styles_hacks()
{
    global $wp_styles;
    $wp_styles->add_data( 'be-ie', 'conditional', 'lte IE 8' );
}

function pw_title( $title, $sep )
{
    if ( $title && !strpos( $title, $sep ) )
        $title .= $sep;

    $title .= PW_SITE_NAME;

    return $title;
}

function pw_get_description()
{
    bloginfo( 'description' );
    exit;
}

?>