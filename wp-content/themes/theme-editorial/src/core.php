<?php

define( 'THEME_PATH',   TEMPLATEPATH . '/' );
define( 'SRC_PATH',     THEME_PATH . 'src/' );

define( 'SITE_URL',     get_home_url() . '/' );
define( 'THEME_URL',    get_template_directory_uri() . '/' );

define( 'SITE_NAME',    get_bloginfo( 'name' ) );

require SRC_PATH . 'autoload.php';

add_action( 'after_setup_theme', 'core_setup', 1 );

function core_setup()
{
    do_action( 'core_setup' );

    if ( is_admin() )
        do_action( 'core_admin_setup' );
    else
        do_action( 'core_public_setup' );
}