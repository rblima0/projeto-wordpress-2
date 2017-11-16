<?php

if ( !get_option( 'users_can_register' ) )
    wp_die( 'Cadastro de usuários não habilitado!' );

require LR_PATH . 'header.php';

global $values;

$values = array(
    'email'  => '',
    'user'   => '',
    'pass1'  => '',
    'pass2'  => ''
);

do_action( 'custom_register' ); ?>

<p>Já possui cadastro? <a href="<?php echo wp_login_url(); ?>" title="Entrar">Faça seu login</a>.</p>

<form method="post" action="<?php echo wp_registration_url(); ?>">
    <?php wp_nonce_field( 'lr-nonce-register', '_nonce' ); ?>
    <div>
        <label>Login</label>
        <input type="text" name="_user" value="<?php echo $values[ 'user' ]; ?>" />
    </div>
    <div>
        <label>E-mail</label>
        <input type="text" name="_email" value="<?php echo $values[ 'email' ]; ?>" />
    </div>
    <div>
        <label>Senha</label>
        <input type="password" name="_pass1" value="" />
    </div>
    <div>
        <label>Confirmar senha</label>
        <input type="password" name="_pass2" value="" />
    </div>
    <div>
        <input type="submit" value="Registrar" />
    </div>
</form>

<?php require LR_PATH . 'footer.php'; ?>