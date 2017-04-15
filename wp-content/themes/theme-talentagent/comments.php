<?php if ( comments_open() ) { ?>

    <h2 id="comentarios">Comentários</h2>

    <ul id="comments" class="comment-list">
        <?php wp_list_comments( array( 'callback' => 'pw_list_comments' ) ); ?>
    </ul>

    <div id="responder">
        <h3>Deixe seu comentário</h3>
        <p>
            <a rel="nofollow" id="cancel-comment-reply-link" href="#responder" style="display: none;">Cancelar Resposta</a>
        </p>

        <form action="<?php echo PW_URL; ?>wp-comments-post.php" method="post">
            <fieldset>
                <?php if ( is_user_logged_in() ) { $current_user = wp_get_current_user(); ?>
                    <p>
                        Conectado como <a href="<?php echo admin_url( 'profile.php' ); ?>" title="Edite seu perfil"><?php echo $current_user->display_name; ?></a>.
                        <a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="Sair do sistema">Desconectar</a>
                    </p>
                <?php } else { ?>
                    <p><input name="author" type="text" placeholder="Nome" /></p>
                    <p><input name="email" type="text" placeholder="Email" /></p>
                    <p><input name="url" type="text" placeholder="Endereço URL (Opcional)" /></p>
                <?php } ?>
                <p><textarea name="comment" cols="45" rows="8" placeholder="Comentário"></textarea></p>
                <p><input type="submit" value="Comentar" /></p>
                <input type="hidden" name="comment_post_ID" value="<?php global $post; echo $post->ID; ?>" id="comment_post_ID" />
                <input type="hidden" name="comment_parent" id="comment_parent" value="<?php echo ( $_GET[ 'replytocom' ] ) ? $_GET[ 'replytocom' ] : 0; ?>" />
            </fieldset>
        </form>
    </div>

<?php } ?>

<!--<div id="responder">
    <?php /*comment_form(); */?>
</div>-->
