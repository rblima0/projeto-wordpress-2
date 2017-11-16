<?php

/**
 *
 * Plugin Name: Custom Products
 * Description: Gerenciamento de produtos personalizados.
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */

class KDM_Products
{

    function activation()
    {
        global $wpdb;
        self::setup_table();
        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->products}` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(128) NOT NULL,
          `price` float unsigned NOT NULL,
          PRIMARY KEY (`id`)
        )";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    function deactivation()
    {
        global $wpdb;
        self::setup_table();
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->products}" );
    }
    
    private static function setup_table()
    {
        global $wpdb;
        $wpdb->products = $wpdb->prefix . 'products';
        array_push( $wpdb->tables, $wpdb->products );
    }
    
    function setup()
    {
        add_action( 'init', array( 'KDM_Products', 'init' ) );
        
        add_action( 'admin_menu',           array( 'KDM_Products_List_table', 'admin_menu' ) );
        add_filter( 'set-screen-option',    array( 'KDM_Products_List_table', 'set_option' ), 10, 3 );
    }
    
    function init()
    {
        self::setup_table();
    }
   
}

define( 'LR_DIR',       basename( dirname( __FILE__ ) ) );
define( 'LR_PATH',      WP_PLUGIN_DIR . '/' . LR_DIR . '/' );

require LR_PATH . 'list-table.php';

register_activation_hook( __FILE__, array( 'KDM_Products', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'KDM_Products', 'deactivation' ) );

add_action( 'plugins_loaded', array( 'KDM_Products', 'setup' ) );