<?php

/**
 *
 * Plugin Name: Login and Register
 * Description: Gerenciamento de usuários e permissões em uma nova área de login e registro.
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */

class KDM_LR
{

    function activation()
    {
        global $wp_roles;
        if ( !isset( $wp_roles->roles[ ROLE_CLIENT ] ) ) {
            $caps = array(
                'read' => true,
                'read_private_posts' => true,
                'read_private_pages' => true
            );
            add_role( ROLE_CLIENT, 'Cliente', $caps );
        }
    }

    function deactivation()
    {
        remove_role( ROLE_CLIENT );
    }
    
    function role_update()
    {
        $c = get_role( ROLE_CLIENT );
        if ( is_a( $c, 'WP_Role' ) ) {
            $c->remove_cap( 'read' );
            if ( !$c->has_cap( 'custom-cap' ) )
                $c->add_cap( 'custom-cap' );
        }
    }
    
    function client_update()
    {
        // $u = wp_get_current_user();
        $u = new WP_User( 2 );
        if ( $u->exists() ) {                   
            $u->remove_role( 'subscriber' );
            $u->add_role( ROLE_CLIENT );
            
            $u->add_cap( 'custom-cap' );
            $u->remove_cap( 'custom-cap' );
            
            $all_caps = $u->get_role_caps();
            if ( isset( $all_caps[ ROLE_CLIENT ] ) )
                echo '<p>O usuário é um cliente.</p>';
            
            if ( !$u->has_cap( 'subscriber' ) )
                echo '<p>Sem permissão de acesso ao Dashboard!</p>';
                       
            if ( user_can( $u, ROLE_CLIENT ) )
                echo '<p>Esse é um dos nossos clientes!</p>';
            
            if ( user_can( $u, 'custom-cap' ) )
                echo '<p>O cliente pode fazer algo personalizado...</p>';
        }
    }

    function setup()
    {
        add_filter( 'rewrite_rules_array',  array( 'KDM_LR', 'rules' ) );
        add_filter( 'query_vars',           array( 'KDM_LR', 'vars' ) );
        add_filter( 'template_redirect',    array( 'KDM_LR', 'redirect' ) );
        
        add_filter( 'login_url',            array( 'KDM_LR', 'login_url' ) );        
        add_filter( 'register_url',         array( 'KDM_LR', 'register_url' ) );
        add_action( 'login_init',           array( 'KDM_LR', 'login_redirect' ) );
        
        add_action( 'custom_login',         array( 'KDM_LR', 'authenticate' ) );
        add_action( 'custom_register',      array( 'KDM_LR', 'register' ) );
        
        add_action( 'custom_login',         array( 'KDM_LR', 'logged' ) );
        add_action( 'custom_register',      array( 'KDM_LR', 'logged' ) );
        
        add_action( 'init',                 array( 'KDM_LR', 'init' ) );
    }
    
    function rules( $rules )
    {
        $r = array(
            "entrar/?$"     => "index.php?login=1",
            "registrar/?$"  => "index.php?register=1"
        );
        return array_merge( $r, $rules );
    }

    function vars( $qv )
    {
        array_push( $qv, 'login' );
        array_push( $qv, 'register' );
        return $qv;
    }

    function redirect()
    {
        global $wp_query;
        $file = false;
        $custom = array( 'login', 'register' );
        foreach( $custom as $c ) {
            if ( $wp_query->get( $c ) ) {
                $file = $c . '.php';
                break;
            }
        }            
        
        if ( $file ) {
            require LR_PATH . $file;
            exit;
        }
    }
    
    function login_url()
    {
        return LR_URL . 'entrar/';
    }
    
    function register_url()
    {
        return LR_URL . 'registrar/';
    }
    
    function login_redirect()
    {
        if ( isset( $_GET[ 'action' ] ) && ( $_GET[ 'action' ] == 'logout' ) ) {
            wp_logout();
            wp_redirect( wp_login_url() );
            exit;
        }
        require LR_PATH . 'login.php';
        exit;
    }
    
    function authenticate()
    {
        if ( empty( $_POST ) || !isset( $_POST[ '_nonce' ] ) )
            return false;
        
        $error = self::check_nonce( 'login' );
        if ( !$error ) {
            global $user;
            $user = ( isset( $_POST[ '_user' ] ) ) ? sanitize_text_field( $_POST[ '_user' ] ) : '';
            $pass = ( isset( $_POST[ '_pass' ] ) ) ? sanitize_text_field( $_POST[ '_pass' ] ) : '';

            if ( !$user || !$pass ) {
                $error = 'Preencha seu nome e senha para continuar!';                    
            } else {
                $u = wp_signon(
                    array(
                        'user_login'    => $user,
                        'user_password' => $pass
                    )
                );
                if ( is_wp_error( $u ) ) {
                    $error = 'Dados inválidos';
                } else {
                    wp_redirect( admin_url( 'profile.php' ) );
                    exit;
                }
            }
        }
        
        if ( $error )
            printf( '<p class="error">%s</p>', $error );
    }

    private static function check_nonce( $action )
    {
        $error = false;
         if ( !wp_verify_nonce( $_POST[ '_nonce' ], 'lr-nonce-' . $action ) )
            $error = 'Não foi possível processar sua requisição...';
         
         return $error;
    }
    
    function register()
    {
        if ( empty( $_POST ) || !isset( $_POST[ '_nonce' ] ) )
            return false;
        
        global $values;
        $error = self::check_nonce( 'register' );
        if ( !$error ) {
            $blank = false;
            $keys = array_keys( $values );
            foreach ( $keys as $k ) {
                $values[ $k ] = ( isset( $_POST[ '_' . $k ]   ) ) ? sanitize_text_field( $_POST[ '_' . $k ] ) : '';
                if ( !$values[ $k ] ) {
                    $blank = true;
                    break;
                }
            }

            if ( $blank ) {
                $error = 'Preencha todos os campos para continuar!';
            } else if ( !is_email( $values[ 'email' ] ) ) {
                $error = 'E-mail inválido!';
            } else if ( $values[ 'pass1' ] !== $values[ 'pass2' ] ) {
                $error = 'As senhas não conferem!';
            } else {
                $u = wp_insert_user(
                    array(
                        'user_email'    => $values[ 'email' ],
                        'user_login'    => $values[ 'user' ],
                        'user_pass'     => $values[ 'pass1' ],
                        'role'          => ROLE_CLIENT
                    )
                );
                if ( is_wp_error( $u ) ) {
                    $error = $u->get_error_message();
                } else if ( !is_int( $u ) ) {
                    $error = 'Não foi possível realizar o registro...';
                } else {
                    wp_mail( $values[ 'email' ], 'Bem-vindo', 'Obrigado por se cadastrar em nosso site!' );
                    printf(
                        '<p class="success">Cadastro realizado com sucesso! <a href="%s" title="Entrar">Entrar</a></p>',
                        wp_login_url()
                    );
                    exit;
                }
            }
        }
        
        if ( $error )
            printf( '<p class="error">%s</p>', $error );
    }
    
    function init()
    {
        $version = '1.0';
        if ( get_option( 'lr-rules' ) !== $version ) {
            update_option( 'lr-rules', $version );
            flush_rewrite_rules();
        }
    }
    
    function logged()
    {
        if ( is_user_logged_in() ) {
            if ( current_user_can( 'read' ) ) {
                wp_redirect( admin_url( 'profile.php' ) );
                exit;
            } else {
                wp_die( 'Você está logado!' );
            }
        }
    }
    
}

if ( !function_exists( 'wp_mail' ) ) {
    
    function wp_mail( $to, $subject, $message )
    {
        return ( @mail( $to, $subject, $message ) );        
    }

}

function custom_title( $sep='|' )
{
    $screen = ( get_query_var( 'login' ) ) ? 'Entrar' : 'Registrar';
    printf( 
        '%s %s %s',
        get_bloginfo( 'name' ),
        $sep,
        $screen
    );
}

define( 'LR_URL',       site_url() . '/' );

define( 'LR_DIR',       basename( dirname( __FILE__ ) ) );
define( 'LR_PATH',      WP_PLUGIN_DIR . '/' . LR_DIR . '/' );

define( 'ROLE_CLIENT',  'client' );

define( 'LR_PATH_URL',  plugins_url( LR_DIR ) . '/' );

register_activation_hook( __FILE__, array( 'KDM_LR', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'KDM_LR', 'deactivation' ) );

add_action( 'plugins_loaded', array( 'KDM_LR', 'setup' ) );