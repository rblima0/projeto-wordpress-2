<?php

/**
 *
 * Plugin Name: Custom Notifier
 * Description: Agendar tarefas com o cron do WordPress
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */

class KDM_Notifier
{
    
    function activation()
    {              
        $when = strtotime( date( '2014-02-07 17:00' ) );
        $args = array( 
            'topic' => 'amanhã',
            'text'  => 'Amanhã ocorre o evento.'
        );
        wp_schedule_single_event( $when, 'notifier_event', $args );
        
        $when = strtotime( date( '2014-02-08 17:00' ) );
        wp_schedule_single_event( $when, 'notifier_event' );

        $ts = wp_next_scheduled( 'notifier_comments' );
        if ( !$ts )
            wp_schedule_event( time(), 'daily', 'notifier_comments' );
    }

    function deactivation()
    {
        wp_clear_scheduled_hook( 'notifier_event' );
        $args = array( 
            'topic' => 'amanhã',
            'text'  => 'Amanhã ocorre o evento.'
        );
        wp_clear_scheduled_hook( 'notifier_event', $args );
        
        wp_clear_scheduled_hook( 'notifier_comments' );
    }
    
    function setup()
    {
        add_action( 'notifier_event',       array( 'KDM_Notifier', 'event' ) );
        add_action( 'notifier_comments',    array( 'KDM_Notifier', 'comments' ) );
        
        add_filter( 'cron_schedules',       array( 'KDM_Notifier', 'schedules' ) );
        
        add_action( 'init',                 array( 'KDM_Notifier', 'test' ) );
        
        load_plugin_textdomain( 'notifier', false, CN_DIR . 'lang/' );
    }
    
    function event( $args=false )
    {
        if ( !is_array( $args ) ) {
            $args = array( 
                'topic' => 'hoje',
                'text'  => 'O evento é HOJE!'
            );
        }
        file_put_contents( CN_PATH . 'cron.log', $args[ 'text' ], FILE_APPEND );
        // wp_mail( get_option( 'admin_email' ), 'Notificador', $args[ 'text' ] );
    }
    
    function comments()
    {
        global $wpdb;
        $amount = (int) $wpdb->get_var(
            "SELECT COUNT( comment_ID ) FROM {$wpdb->comments} c "
            . "INNER JOIN {$wpdb->posts} p ON c.comment_post_ID=p.ID "
            . "WHERE c.comment_approved<>'1' AND p.post_status='publish'"
        );
        
        $html = sprintf( 'O site possui %s comentários para moderação.', $amount );
        file_put_contents( CN_PATH . 'cron.log', $html, FILE_APPEND );
        // wp_mail( get_option( 'admin_email' ), 'Notificador', $html );
    }
    
    function schedules( $s )
    {
        $s[ 'weekly' ] = array(
            'interval'  => 60*60*24*7,
            'display'   => 'Semanalmente'
        );
        return $s;
    }
    
    function test()
    {
        if ( is_admin() )
            return true;
        
        // echo 'Testando o plugin para agendamento de tarefas através do CRON do WordPress.';
        
        echo __( 'Translatable text ', 'notifier' ) . '<br />';
        echo _n( 'Singular', 'Plural', 1, 'notifier' ) . '<br />';
        
        echo _x( 'Text', 'button', 'notifier' ) . '<br />';
        echo _x( 'Text', 'header', 'notifier' ) . '<br />';
        echo _nx( 'Singular', 'Plural', 2, 'reference', 'notifier' ) . '<br />';
        
        _e( 'Example', 'notifier' ); echo '<br />';
        echo __( 'Example', 'notifier' ) . '<br />';
        _ex( 'Example', 'test only', 'notifier' ) . '<br />';
        
        // Tipos de periodicidade
        // var_dump( wp_get_schedules() );
        
        // Tipo de agendamento para o gancho específico
        // var_dump( wp_get_schedule( 'notifier_comments' ) );
        
        // Remover agendamento de uma ocorrência do evento
        // $ns = wp_next_scheduled( 'notifier_comments' );
        // wp_unschedule_event( $ns, 'notifier_comments' );
        // wp_schedule_event( time(), 'hourly', 'notifier_comments' );
        // var_dump( date( 'd/m/Y H:i:s', $ns ) );
        
        exit;
    }
    
}

define( 'CN_DIR',   basename( dirname( __FILE__ ) ) . '/' );
define( 'CN_PATH',  WP_PLUGIN_DIR . '/' . CPR_DIR . '/' );

register_activation_hook(   __FILE__, array( 'KDM_Notifier', 'activation'   ) );
register_deactivation_hook( __FILE__, array( 'KDM_Notifier', 'deactivation' ) );

add_action( 'plugins_loaded', array( 'KDM_Notifier', 'setup' ) );