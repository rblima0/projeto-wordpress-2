<?php

/**
 *
 * Plugin Name: Custom Editor
 * Description: Personalizar o editor
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */

add_action( 'plugins_loaded', 'ce_setup' );

function ce_setup()
{
    add_action( 'admin_print_footer_scripts',   'ce_quicktags' );
    
    add_filter( 'mce_buttons',          'register_button' );
    add_filter( 'mce_external_plugins', 'add_tinymce_plugin' );
}

function ce_quicktags()
{
    if ( wp_script_is( 'quicktags' ) ) {
    ?>
    <script type="text/javascript">
    QTags.addButton( 'ce_pre', 'pre', '<pre lang="php">', '</pre>', 'q', 'Texto pré-formatado', 130 );
    QTags.addButton( 'ce_alert', 'alerta', custom_fn, false, false, 'Alerta' );
    function custom_fn() {
        alert( 'Teste de botão' );
    }
    </script>   
    <?php
    } 
}

function register_button( $buttons )
{
    array_push( $buttons, 'custom_button' );
    return $buttons;
}

function add_tinymce_plugin( $plugins ) 
{
    $plugins[ 'custom_button' ] = plugin_dir_url( __FILE__ ) . 'ce.js';
    return $plugins;
}