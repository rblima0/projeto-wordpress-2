<?php
define( 'PW_URL',       get_home_url() . '/' );
define( 'PW_URL_THEME', get_bloginfo( 'template_url' ) . '/' );
define( 'PW_SITE_NAME', get_bloginfo( 'name' ) );

add_action('after_setup_theme', 'pw_theme_setup');

function pw_theme_setup() {

    
}

?>