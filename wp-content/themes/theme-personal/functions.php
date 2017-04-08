<?php
define( 'PW_URL',       get_home_url() . '/' );
define( 'PW_URL_THEME', get_bloginfo( 'template_url' ) . '/' );
define( 'PW_SITE_NAME', get_bloginfo( 'name' ) );
define( 'PW_SITE_DESCRIPTION', get_bloginfo( 'description' ) );

add_action('after_setup_theme', 'pw_theme_setup');

function pw_theme_setup() {

    add_filter('show_admin_bar', '__return_false');
    add_filter('wp_title', 'pw_title', 10, 2);

    add_action('wp_enqueue_scripts', 'pw_register_scripts_and_styles', '1');
    add_action('wp_enqueue_scripts', 'pw_register_styles_hacks', '2');
    add_action('wp_enqueue_scripts', 'pw_enqueue_scripts_and_styles', '3');

    add_action('wp_ajax_description', 'pw_get_description');
    add_action('wp_ajax_nopriv_get_description', 'pw_get_description');
}

function pw_title($title, $sep){
    if($title && !strpos($title, $sep)){
        $title .= $sep;
    }

    $title .= PW_SITE_NAME . " | " . PW_SITE_DESCRIPTION;
    return $title;
}

function pw_register_scripts_and_styles(){
    wp_register_style('be', PW_URL_THEME . 'assets/css/main.css', false, '', 'screen');
    wp_register_style('be-ie', PW_URL_THEME . 'assets/css/ie8.css', false, array('be'), 'screen');

    wp_register_script('be-html5shiv', PW_URL_THEME . 'assets/js/ie/html5shiv.js', false, array('html5shiv'), 'true');
    wp_register_script('be-jquery', PW_URL_THEME . 'assets/js/jquery.min.js', false, array('jquery'), 'false');
    wp_register_script('be-poptrox', PW_URL_THEME . 'assets/js/jquery.poptrox.min.js', false, array('poptrox'), 'false');
    wp_register_script('be-skel', PW_URL_THEME . 'assets/js/skel.min.js', false, array('skel'), 'false');
    wp_register_script('be-util', PW_URL_THEME . 'assets/js/util.js', false, array('util'), 'false');
    wp_register_script('be-respond', PW_URL_THEME . 'assets/js/ie/respond.min.js', false, array('respond'), 'false');
    wp_register_script('be-main', PW_URL_THEME . 'assets/js/main.js', false, array('main'), 'false');
}

function pw_register_styles_hacks(){
    global $wp_styles;
    $wp_styles->add_data('be-ie', 'conditional', 'lte IE 8');
}

function pw_enqueue_scripts_and_styles(){
    wp_enqueue_script( 'be-html5shiv' );
    wp_enqueue_script( 'be-jquery' );
    wp_enqueue_script( 'be-poptrox' );
    wp_enqueue_script( 'be-skel' );
    wp_enqueue_script( 'be-util' );
    wp_enqueue_script( 'be-respond' );
    wp_enqueue_script( 'be-main' );

    wp_enqueue_style( 'be' );
    wp_enqueue_style( 'be-ie' );
}

function pw_get_description(){
    bloginfo( 'description' );
    exit;
}

?>