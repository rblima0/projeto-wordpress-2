<?php

/**
*
*	Plugin Name: Get Remote Posts
*	Description: Recupera informações remotas.
*	Version: 1.0
*	Author: Rodrigo Banci
*	Author URI: http://rodrigobanci.com.br/
*
**/

class KDM_Posts{

	static $prefix = 'grp_';

    function activation(){
    	add_option( self::$prefix . 'search', 'wordpress', false, 'no');
    	add_option( self::$prefix . 'count', 3, false, 'no');
    }

    function deactivation(){
    	delete_option(self::$prefix . 'search');
    	delete_option(self::$prefix . 'count');
    }

    function init(){
		add_action('admin_menu', array('KDM_Posts', 'admin_menu'));
    }

    function admin_menu(){
    	add_menu_page('Dados Remotos', 'Dados Remotos', 'administrator', 'grp-data', array('KDM_Posts', 'options_form'));
    	//add_submenu_page('grp-data', 'Submenu', 'Submenu', 'administrator', 'grp-subdata', array('KDM_posts', 'subcontent'));
    }

    /*function subcontent(){
    	echo 'Formulário com opções secundarias...';
    }*/

    function options_form(){
    	$fields = self::options_save();
    	if(!$fields['search'] || !$fields['count'])
    	$fields = array(
    		'search' => get_option(	self::$prefix . 'search' ),
    		'count' => get_option(	self::$prefix . 'count'	)
		);
		?>

		<div class="wrap get-remote-posts">
			<h2>Configurações do Plugin</h2>
			<form method="post">
				<table class="form-table">
					<tr>
						<th><label for="grp-search">Termos de Busca</label></th>
						<td><input type="text" size="20" name="_search" value="<?php echo $fields['search']; ?>" id="grp-search" /></td>
					</tr>
					<tr>
						<th><label for="grp-count">Quantidade de Posts</label></th>
						<td><input type="text" size="10" name="_count" value="<?php echo $fields['count']; ?>" id="grp-count" /></td>
					</tr>
				</table>
				<p><input type="submit" name="submit" value="Salvar" class="button-primary" /></p>
			</form>
		</div>

    	<?php
    }

    function options_save(){
    	$fields = array(
    		'search' => '',
    		'count' => ''
		);

		if(!empty($_POST)){
			foreach ($fields as $f => $v) {
				$field = self::$prefix . $f;
				
				$new = false;
				$old = get_option( $field);

				if(isset($_POST['_' . $f]))
					$new = $_POST['_'. $f];

				$fields[$f] = $new;
				if($new && ($new !== $old))
					update_option($field, $new);
				else
					delete_option($field);
			}
			echo '<div><p>Configurações Atualizadas !</p></div>';
		}
		return $fields;
    }
}

register_activation_hook(__FILE__, array('KDM_Posts', 'activation'));
register_deactivation_hook(__FILE__, array('KDM_Posts', 'deactivation'));
add_action('plugins_loaded', array('KDM_Posts', 'init'));
?>