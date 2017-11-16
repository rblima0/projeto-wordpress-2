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

    //CONTROLA A QUANTIDADE DE REVISÕES DIRETO NO TEMA
    add_filter('wp_revisions_to_keep', 'limit_revisions', 10, 2);

    //REMOVENDO O AUTOSAVE
    add_action('admin_init', 'remove_autosave');

}

function limit_revisions( $limit, $post ){
    if( $post->post_type == 'post' )
        $limit = 2;

    return $limit;
}

function remove_autosave(){
    wp_deregister_script('autosave');
}