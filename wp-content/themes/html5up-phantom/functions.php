<?php
/**
 * Created by PhpStorm.
 * User: Rodrigo
 * Date: 05/03/2017
 * Time: 15:19
 */

/*
add_action('wp_head', 'custom_head');
function custom_head(){
    exit;
}
*/

/*
add_filter('template_directory_uri', 'custom_filter', 10, 3);
function custom_filter($template_dir_uri, $template, $theme_root_uri){
    var_dump( $template_dir_uri, $template, $theme_root_uri);
    exit;
}*/

/*
define('THEME_URL', get_bloginfo('template_url') . "/");

add_action('phantom', 'custom_phantom');

function custom_phantom(){
    echo apply_filters('phantom_teste', 'teste');
}

add_filter('phantom_teste', 'custom_teste');

function custom_teste($str){
    return $str . ' de filtroa ';
}

do_action('phantom');*/

/*add_action( 'phpmailer_init', 'set_smtp_credentials' );
function set_smtp_credentials( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'servidor.host.com.br';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = 'email@email.com.br';
    $phpmailer->Password = 'senha';
}*/

define('THEME_URL', get_bloginfo('template_url') . "/");
define('SITE_NAME', get_bloginfo('name'));
define('SITE_URL', get_bloginfo('url'));

//CRIANDO MENU
add_action('after_setup_theme', 'custom_setup');

function custom_setup(){
    register_nav_menus( array(
        'menu-header' => __( 'Cabeçalho' ),
        'menu-footer' => __('Rodapé')
    ) );
}

//COLOCANDO OPÇÃO DE INSERIR THUMBNAIL
add_theme_support("post-thumbnails");

//DIMINUINDO QUANTIDADE DE CARACTERES
add_filter('excerpt_length', 'custom_excerpt_length');
add_filter('excerpt_more', 'custom_excerpt_more');

function custom_excerpt_length(){
    return 10;
}

function custom_excerpt_more(){
    return " ... ";
}

add_image_size('cover', 353, 326, true);

//FILTRO PARA RETIRAR TAMANHO
add_filter('post_thumbnail_html', 'custom_thumbnail_html');

function custom_thumbnail_html($html){
    return preg_replace(
        array(
            '/height="\d*"\s/',
            '/width="\d*"\s/'
        ),
        '',
        $html
    );
}
add_action('init', 'custom_contact');

function custom_contact(){
    global $contact_error;
        if (isset($_POST['contact'])){
            $values = array();
            $fields = array('name', 'email', 'message');
            foreach ($fields as $f){
                $value = (isset($_POST[$f])) ?
                    sanitize_text_field($_POST[$f]) : false;
                if(!$value){
                    $contact_error = 'Preencha todos os campos !';
                    break;
                }
                $values[$f] = $value;
            }

            if(!is_email($values['email'])){
                $contact_error = 'Email inválido !';
            } else if(!$contact_error){
                $to = 'email@email.com';
                $subject = 'Site - Formulário de Contato';
                $message = sprintf(
                    'Nome: %s' . PHP_EOL .
                    'Email: %s' . PHP_EOL .
                    'Mensagem: %s',
                    $values['name'],
                    $values['email'],
                    $values['message']
                );
                if(!wp_mail($to, $subject, $message)){
                    $contact_error = 'Não foi possivel enviar a mensagem...';
                } else {
                    wp_die('Mensagem Enviada com sucesso');
                }

                wp_die('Mensagem enviada com sucesso !');
            }
        }
}