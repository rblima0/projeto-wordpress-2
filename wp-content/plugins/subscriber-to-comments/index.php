<?php

/**
*
*	Plugin Name: Subscriber Comments
*	Description: Permite aos usúarios se inscrever na aba de comentários.
*	Version: 1.0
*	Author: Rodrigo Banci
*	Author URI: http://rodrigobanci.com.br/
*
**/

class KDM_Subscriber_Comments
{

    static $field_name  = 'kdm_subscribe';
    static $query_arg   = 'unsubscribe';

    function activation()
    {

    }

    function deactivation()
    {

    }

    function init()
    {
        add_action( 'comment_form', array( 'KDM_Subscriber_Comments', 'show_form' ) );
        add_action( 'comment_post', array( 'KDM_Subscriber_Comments', 'check_subscribe' ), 10, 2 );

        add_action( 'comment_unapproved_to_approved', array( 'KDM_Subscriber_Comments', 'notify' ) );

        add_action( 'wp_loaded', array( 'KDM_Subscriber_Comments', 'unsubscribe' ) );

        add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
    }

    function show_form()
    {
        ?>
        <label>
            <input type="checkbox" name="<?php echo self::$field_name; ?>" value="1" />
            Quero receber atualizações por e-mail de novos comentários para esse post.
        </label>
        <?php
    }

    function check_subscribe( $comment_id, $approved )
    {
        if ( isset( $_POST[ self::$field_name ] ) && $_POST[ self::$field_name ] ) {
            $comment = array(
                'comment_ID'    => $comment_id,
                'comment_karma' => 1
            );
            wp_update_comment( $comment );
        }

        if ( $approved ) {
            $c = get_comment( $comment_id );
            self::notify( $c );
        }
    }

    function notify( $comment )
    {
        $post_id = $comment->comment_post_ID;
        $permalink = get_permalink( $post_id );
        $message = sprintf(
            'O post <a href="%s" title="Ver post e novo comentário">"%s"</a> recebeu um novo comentário.' .
            'Para cancelar o recebimento de notificações desse post <a href="[unsubscribe]" title="Cancelar subscrição">clique aqui</a>.',
            $permalink,
            get_the_title( $post_id )
        );
        $comments = get_comments(
            array(
                'post_id'   => $post_id,
                'karma'     => 1
            )
        );
        foreach( $comments as $c ) {
            $to = $c->comment_author_email;
            if ( ( $c->comment_ID !== $comment->comment_ID ) && is_email( $to ) ) {
                $msg = str_replace( '[unsubscribe]', add_query_arg( self::$query_arg, $c->comment_ID, $permalink ), $message );
                wp_mail( $to, 'Notificação de comentário', $msg );
            }
        }
    }

    function unsubscribe()
    {
        $comment_id = ( isset( $_GET[ self::$query_arg ] ) ) ? (int) $_GET[ self::$query_arg ] : false;
        if ( is_single() && $comment_id ) {
            $c = get_comment( $comment_id );
            if ( $c->comment_karma ) {
                $comment = array(
                    'comment_ID'    => $comment_id,
                    'comment_karma' => false
                );
                wp_update_comment( $comment );
                $message = 'Você optou por não receber mais notificações de comentários desse post!';
            } else {
                $message = 'Você já fez o cancelamento de sua subscrição...';
            }

            global $post;
            $message = sprintf(
                '<h1>Notificação de comentários</h1>' .
                '<p>%s</p>' .
                '<p>Voltar ao post "<a href="%s" title="Voltar ao post">%s</a>".</p>',
                $message,
                get_permalink( $post->ID ),
                get_the_title( $post->ID )
            );
            wp_die( $message );
        }
    }

}

/*
// nesse exemplo talvez você queira zerar o campo karma, exportar os seguidores para uma opção ou arquivo externo csv
register_activation_hook( __FILE__, array( 'KDM_Subscriber_Comments', 'activation' ) );
register_deactivation_hook( dirname( __FILE__ ) . '\index.php', array( 'KDM_Subscriber_Comments', 'deactivation' ) );
*/

add_action( 'plugins_loaded', array( 'KDM_Subscriber_Comments', 'init' ) );

?>