<?php

add_theme_support('post-thumbnails');

function cadastrando_post_type_imoveis(){
$nomeSingular = "Imóvel";
$nomePlural = "Imóveis";
$description = 'Imóveis da imobiliária Temast';

$labels = array(
		'name' => 'Imóveis',
		'singular_name' => $nomeSingular,
		'menu_name' => $nomeSingular,
		'name_admin_bar' => $nomeSingular,
		'add_new' => 'Adicionar novo ' . $nomeSingular,
		'add_new_item' => 'Adicionar novo ' . $nomeSingular,
		'new_item' => 'Novo ' . $nomeSingular,
		'edit_item' => 'Editar ' . $nomeSingular,
		'view_item' => 'Visualizar ' . $nomeSingular,
		'all_items' => 'Todos os ' . $nomePlural,
		'search_items' => 'Procurar ' . $nomePlural,
		'parent_item_col' => 'Pagina pai',
		'not_found' => $nomeSingular . ' não encontrado',
		'not_found_in_trash' => 'Sem ' . $nomePlural
	);

$supports = array(
    'title',
    'editor',
    'thumbnail'
);

$args = array (
    'public' => true,
    'labels' => $labels,
    'description' => $description,
    'menu_icon' => 'dashicons-admin-home',
    'supports' => $supports
);
register_post_type('imovel', $args);
}

add_action('init', 'cadastrando_post_type_imoveis');


//CRIANDO MENU NAS PAGINAS
function registrar_menu_navegacao() {
    register_nav_menu('header-menu', 'main-menu');
}
add_action( 'init', 'registrar_menu_navegacao');

//CRIANDO TITULO DOS MENUS
function get_titulo() {
	if( is_home() ) {
		bloginfo('name');
	} else {
		bloginfo('name');
		echo ' | ';
		the_title();
	}
}

//CRIANDO TAXONOMIA
function taxonomia_localizacao(){
    $singular = 'Localização';
    $plural = 'Localizações';

    $labels = array(
        'name' => $plural,
        'singular_name' => $singular,
        'view_item' => 'Ver ' . $singular,
        'edit_item' => 'Editar ' . $singular,
        'new_item' => 'Novo ' . $singular,
        'add_new_item' => 'Adicionar novo ' . $singular
    );

    $args = array(
    		'labels' => $labels,
    		'public' => true,
    		'hierarchical' => true
		);

    register_taxonomy('localizacao', 'imovel', $args);
}

add_action('init', 'taxonomia_localizacao');

function is_selected_taxonomy($taxonomy, $search) {
	if($taxonomy->slug === $search) {
		echo 'selected';
	}
}

//ADICIONANDO METAINFO
function adicionar_meta_info_imovel() {
	add_meta_box(
		'informacoes_imovel',
		'Informações',
		'informacoes_imovel_view',
		'imovel',
		'normal',
		'high'
	);
}

add_action('add_meta_boxes', 'adicionar_meta_info_imovel');

function informacoes_imovel_view( $post ) {
	$imoveis_meta_data = get_post_meta( $post->ID ); ?>

	<style>
		.temast-metabox {
			display: flex;
			justify-content: space-between;
		}

		.temast-metabox-item {
			flex-basis: 30%;

		}

		.temast-metabox-item label {
			font-weight: 700;
			display: block;
			margin: .5rem 0;

		}

		.input-addon-wrapper {
			height: 30px;
			display: flex;
			align-items: center;
		}

		.input-addon {
			display: block;
			border: 1px solid #CCC;
			border-bottom-left-radius: 5px;
			border-top-left-radius: 5px;
			height: 100%;
			width: 30px;
			text-align: center;
			line-height: 30px;
			box-sizing: border-box;
			background-color: #888;
			color: #FFF;
		}

		.temast-metabox-input {
			height: 100%;
			border: 1px solid #CCC;
			border-left: none;
			margin: 0;
		}

	</style>
	<div class="temast-metabox">
		<div class="temast-metabox-item">
			<label for="temast-preco-input">Preço:</label>
			<div class="input-addon-wrapper">
				<span class="input-addon">R$</span>
				<input id="temast-preco-input" class="temast-metabox-input" type="text" name="preco_id"
				value="<?= number_format($imoveis_meta_data['preco_id'][0], 2, ',', '.'); ?>">
			</div>
		</div>

		<div class="temast-metabox-item">
			<label for="temast-vagas-input">Vagas:</label>
			<input id="temast-vagas-input" class="temast-metabox-input" type="number" name="vagas_id"
			value="<?= $imoveis_meta_data['vagas_id'][0]; ?>">
		</div>

		<div class="temast-metabox-item">
			<label for="temast-banheiros-input">Banheiros:</label>
			<input id="temast-banheiros-input" class="temast-metabox-input" type="number" name="banheiros_id"
			value="<?= $imoveis_meta_data['banheiros_id'][0]; ?>">
		</div>

		<div class="temast-metabox-item">
			<label for="temast-quartos-input">Quartos:</label>
			<input id="temast-quartos-input" class="temast-metabox-input" type="number" name="quartos_id"
			value="<?= $imoveis_meta_data['quartos_id'][0]; ?>">
		</div>

	</div>
<?php

}

function salvar_meta_info_imoveis( $post_id ) {
	if( isset($_POST['preco_id']) ) {
		update_post_meta( $post_id, 'preco_id', sanitize_text_field( $_POST['preco_id'] ) );
	}
	if( isset($_POST['vagas_id']) ) {
		update_post_meta( $post_id, 'vagas_id', sanitize_text_field( $_POST['vagas_id'] ) );
	}
	if( isset($_POST['banheiros_id']) ) {
		update_post_meta( $post_id, 'banheiros_id', sanitize_text_field( $_POST['banheiros_id'] ) );
	}
	if( isset($_POST['quartos_id']) ) {
		update_post_meta( $post_id, 'quartos_id', sanitize_text_field( $_POST['quartos_id'] ) );
	}
}

add_action('save_post', 'salvar_meta_info_imoveis');

function enviar_e_checar_email($nome, $email, $mensagem) {
		return wp_mail( 'rblima0@gmail.com', 'Email Temast', 'Nome: ' . $nome . "\n" . $mensagem  );
}
