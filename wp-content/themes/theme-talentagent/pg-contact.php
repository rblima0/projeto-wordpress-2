<?php

// Template Name: Página de contato

$msg = false;
if ( !empty( $_POST ) ) {
    if ( !wp_verify_nonce( $_POST[ 'hdn_hash' ], 'user_check' ) )
        wp_die( 'Trapaceando, é?!' );

    $_POST[ 'pw_email' ] = sanitize_email( $_POST[ 'pw_email' ] );
    $fields = array( 'name', 'phone', 'message' );
    foreach ( $fields as $field )
        $_POST[ 'pw_' . $field ] = sanitize_text_field( $_POST[ 'pw_' . $field ] );

    if ( $_POST[ 'pw_name' ] && $_POST[ 'pw_email' ] && $_POST[ 'pw_message' ] ) {
        if ( !is_email( $_POST[ 'pw_email' ] ) ) {
            $msg = 'Email inválido!';
        } else {
            $message = sprintf(
                'Mensagem enviada em %s <br />
                De "%s" [%s]<br />
                Telefone: (%s)<br /><br />
                %s',
                date( 'd/m/Y H:i:s' ),
                $_POST[ 'pw_name' ],
                $_POST[ 'pw_email' ],
                $_POST[ 'pw_phone' ],
                $_POST[ 'pw_message' ]
            );
            if ( wp_mail( get_option( 'admin_email' ), 'Mensagem de contato: ' . $_POST[ 'pw_subject' ], $message ) )
                $msg = 'Mensagem enviada com sucesso!';
            else
                $msg = 'Não foi possível enviar sua mensagem...';
        }
    } else {
        $msg = 'Preencha todos os campos obrigatórios!';
    }
}

get_header(); ?>

    <div id="page" class="container">
        <div id="content">
            <div class="post">
                <?php the_post(); ?>
                <h1><?php the_title(); ?></h1>
                <div class="post-content">
                    <?php the_content(); ?>

                    <?php if ( $msg ) echo "<p>{$msg}</p>"; ?>
                    <form action="<?php the_permalink(); ?>" method="post" class="contact">
                        <?php wp_nonce_field( 'user_check', 'hdn_hash' ); ?>
                        <fieldset>
                            <ul>
                                <li>Nome*<input type="text" name="pw_name" /></li>
                                <li>Email*<input type="text" name="pw_email" /></li>
                                <li>Telefone<input type="text" name="pw_phone" /></li>
                                <li>Assunto<input type="text" name="pw_subject" /></li>
                                <li>Mensagem*<textarea name="pw_message" rows="7" cols="100"></textarea></li>
                                <li><input type="submit" value="Enviar" /></li>
                            </ul>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
        <?php get_sidebar(); ?>
    </div>

<?php get_footer(); ?>