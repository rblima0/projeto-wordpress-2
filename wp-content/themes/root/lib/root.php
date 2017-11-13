<?php

/**
 *
 * Base de todo o framework.
 * Começa com a declaração de constantes.
 *
 */
if ( !defined( 'THEME_VERSION' ) )
    define( 'THEME_VERSION', '1.0' );

// Caminhos

define( 'PATH_THEME',   TEMPLATEPATH . '/' );
define( 'PATH_LIB',     PATH_THEME . 'lib/' );
define( 'PATH_SRC',     PATH_THEME . 'src/' );

// Informações relevantes

define( 'ROOT_URL',     get_home_url() . '/' );
define( 'THEME_URL',    get_bloginfo( 'template_url' ) . '/' );
define( 'SITE_NAME',    get_bloginfo( 'name' ) );

// i18n

define( 'TEXT_DOMAIN', 'root' );

load_theme_textdomain( TEXT_DOMAIN, PATH_LIB . 'lang' );

/**
 * Carrega funções pluggable do sistema
 * Executa o autoload, carregamento das classes apenas quando necessário
 *
 */
require_once PATH_LIB . 'core.php';
require_once PATH_LIB . 'toolkit.php';
require_once PATH_LIB . 'autoload.php';

add_action( 'after_setup_theme', 'setup', 11 );

/**
 *
 * Configuração básica de um Tema
 *
 */
function setup()
{
    custom_locale();

    Main::init();

    do_action( 'root_setup' );
}

?>