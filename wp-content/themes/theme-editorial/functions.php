<?php

require TEMPLATEPATH . '/src/core.php';

add_action( 'core_setup', 'custom_setup' );

// add_action( 'core_public_setup', 'public_setup' );
// add_action( 'core_admin_setup', 'admin_setup' );

function custom_setup()
{
    Rules::init();

    add_theme_support( 'post-thumbnails' );

    add_filter( 'pre_get_posts', array( 'Theme', 'pre_get_posts' ) );

    register_nav_menu('menu-sidebar', 'Menu da Sidebar');
}