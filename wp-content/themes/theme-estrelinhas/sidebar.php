<div id="sidebar">
                    <?php if ( !is_user_logged_in() ) { ?>
                    <ul>
						<li>
							<h2>Entrar no sistema</h2>
							<?php wp_login_form( 'remember=' ); ?>
                            <?php wp_register(); ?>
							<div style="clear: both;">&nbsp;</div>
						</li>
                    <?php } else { $user = wp_get_current_user(); ?>
                    <p>
                        <strong>Olá, <?php echo $user->display_name; ?></strong><br />
                        <a href="<?php echo wp_logout_url(); ?>" title="Sair">Fazer logoff</a>
                    </p>
					<ul>
						<li>
							<h2>Encontre aqui</h2>
							<div id="search" >
								<form method="get" action="<?php echo PW_URL; ?>">
									<div>
										<input type="text" name="s" id="search-text" placeholder="Pesquisar" />
									</div>
								</form>
							</div>
							<div style="clear: both;">&nbsp;</div>
						</li>
						<li>
							<h2>Aliquam tempus</h2>
							<p>Mauris vitae nisl nec metus placerat perdiet est. Phasellus dapibus semper consectetuer hendrerit.</p>
						</li>
						<li>
							<h2>Categorias</h2>
							<ul>
								<?php
                                wp_list_categories( 'title_li=' ); ?>
							</ul>
						</li>
					</ul>
                    <?php } ?>
				</div>
				<div style="clear: both;">&nbsp;</div>
			</div>
		</div>
	</div>
	<!-- end #page -->
</div>