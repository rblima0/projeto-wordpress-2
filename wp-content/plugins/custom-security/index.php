<?php

/**
 *
 * Plugin Name: Custom Security
 * Description: Trabalhar as funções de validação do WordPress
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */

class KDM_Security
{

    public static function setup()
    {
        add_action( 'admin_menu', array( 'KDM_Security', 'admin_menu' ) );
    }
    
    public static function admin_menu()
    {
        add_menu_page( 'Validação', 'Validação', 'administrator', 'cs', array( 'KDM_Security', 'options' ) );
    }

    public static function options()
    {       
        // Nonces em URL
        /*
        $url = wp_nonce_url( admin_url( 'admin.php?page=cs' ), 'cs_nonce' );
        $n = $_GET[ '_wpnonce' ];
        
        if ( wp_verify_nonce( $n, 'cs_nonce' ) )
            echo 'Válido!';
        */
        
        // Nonces em sessions, cookies e arquivos externos
        /*
        if ( !session_id() )
            session_start();
        
        if ( !isset( $_SESSION[ 'custom_nonce' ] ) ) {
            $_SESSION[ 'custom_nonce' ] = wp_create_nonce( 'cs_nonce' );
        }
        
        $n = $_SESSION[ 'custom_nonce' ];
        
        if ( wp_verify_nonce( $n, 'cs_nonce' ) )
                echo 'Nonce válido!';
        */
        
        // Nonces em campos de formulário
        $n = ( isset( $_POST[ 'cs_nonce' ] ) ) ? $_POST[ '_nonce' ] : false;
        if ( $n && wp_verify_nonce( $n, 'cs_nonce' ) )
            echo 'Tudo ok...';

        // Verificação da origem de envio dos dados
        /*
        $referer = wp_get_referer();
        $s = 'http://' . $_SERVER[ 'SERVER_NAME' ];
        $pos = strpos( $referer, $s );
        if ( !is_int( $pos ) || $pos > 0 )
            echo 'Origem inválida...';
        else
            echo 'Origem válida';
        */
        
        // Nonces dentro do Dashboard
        if ( $n && !check_admin_referer( 'cs_nonce', '_nonce' ) )
            wp_die( 'Interrompendo o envio das informações...' );
        
        if ( !empty( $_POST ) ) {
            if ( $_POST[ '_str' ] ) {
                echo '<p>Apenas texto: ' . $_POST[ '_str' ] . '</p>';
                
                printf(
                    '<p>Apenas texto: %s</p>',
                    sanitize_text_field( $_POST[ '_str' ] )
                );
            }
            
            if ( $_POST[ '_attr' ] ) {
                echo '<p>Uso de um atributo <a href="#" title="' . $_POST[ '_attr' ] . '">nesse link</a> e nada mais.</p>';
                
                printf(
                    '<p>Uso de um atributo <a href="#" title="%s">nesse link</a> e nada mais.</p>',
                    esc_attr( $_POST[ '_attr' ] )
                );
            }
            
            if ( $_POST[ '_email' ] ) {
                echo '<p>E-mail: ' . $_POST[ '_email' ] . '</p>';
                
                $email = sanitize_email( $_POST[ '_email' ] );
                printf(
                    '<p>E-mail: %s</p>',
                    $email
                );
                
                printf(
                    '<p>E-mail: %s</p>',
                    antispambot( $email )
                );
                
                printf(
                    'Esse e-mail %s é válido!',
                    ( is_email( $email ) ) ? 'sim, ' : 'não'
                );
            }
            
            if ( $_POST[ '_html' ] ) {
                echo '<p>HTML: ' . $_POST[ '_html' ] . '</p>';
                
                printf(
                    '<p>HTML: %s</p>',
                    esc_html( $_POST[ '_html' ] )
                );
            }
            
            if ( $_POST[ '_text' ] ) {
                echo '<p>Texto: ' . $_POST[ '_text' ] . '</p>';
                
                $allowed_tags = array( 
                    'b' => array(),
                    'a' => array(
                        'href' => array()
                    ) 
                );
                printf(
                    '<p>Texto: %s</p>',
                    wp_kses( $_POST[ '_text' ], $allowed_tags, array( 'http', 'https' ) )
                );
            }
            
            if ( $_POST[ '_editor' ] ) {
                echo '<p>Editor: ' . $_POST[ '_editor' ] . '</p>';
                
                printf(
                    '<p>Editor: %s</p>',
                    wp_strip_all_tags( $_POST[ '_editor' ], true )
                );
            }
        }
        ?>
        <h2>Validação de dados</h2>
        <form method="post" action="<?php echo admin_url( 'admin.php?page=cs' ); ?>">
            <?php wp_nonce_field( 'cs_nonce', '_nonce' ); ?>
            <table class="form-table">
                <tr>
                    <th>Apenas texto</th>
                    <td><textarea name="_str" cols="50" rows="5"></textarea></td>
                </tr>
                <tr>
                    <th>Atributo</th>
                    <td><input type="text" name="_attr" /></td>
                </tr>
                <tr>
                    <th>E-mail</th>
                    <td><input type="text" name="_email" /></td>
                </tr>
                <tr>
                    <th>Tag HTML</th>
                    <td><input type="text" name="_html" /></td>
                </tr>
                <tr>
                    <th>Texto</th>
                    <td><textarea name="_text" cols="50" rows="5"></textarea></td>
                </tr>
                <tr>
                    <th>Editor HTML</th>
                    <td><?php wp_editor( '', '_editor', array( 'media_buttons' => false ) ); ?></td>
                </tr>
                <tr>
                    <td colspan="2"><?php submit_button(); ?></td>
                </tr>
            </table>
        </form>
        <?php
    }
    
}

add_action( 'plugins_loaded', array( 'KDM_Security', 'setup' ) );