<?php
/**
 * Created by PhpStorm.
 * User: Rodrigo
 * Date: 05/03/2017
 * Time: 15:19
 */

/*
add_action('wp_head', 'custom_head');
function custom_head(){
    exit;
}
*/

/*
add_filter('template_directory_uri', 'custom_filter', 10, 3);
function custom_filter($template_dir_uri, $template, $theme_root_uri){
    var_dump( $template_dir_uri, $template, $theme_root_uri);
    exit;
}*/

/*
define('THEME_URL', get_bloginfo('template_url') . "/");

add_action('phantom', 'custom_phantom');

function custom_phantom(){
    echo apply_filters('phantom_teste', 'teste');
}

add_filter('phantom_teste', 'custom_teste');

function custom_teste($str){
    return $str . ' de filtroa ';
}

do_action('phantom');*/

define('THEME_URL', get_bloginfo('template_url') . "/");
define('SITE_NAME', get_bloginfo('name'));
define('SITE_URL', get_bloginfo('url'));

add_action('after_setup_theme', 'custom_setup');

function custom_setup(){
    register_nav_menus( array(
        'menu-header' => __( 'Cabeçalho' ),
        'menu-footer' => __('Rodapé')
    ) );
}

