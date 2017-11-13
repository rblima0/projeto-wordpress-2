<?php

// {{{ i18n

/**
 *
 * Manipulação de strings aliada a tradução
 *
 * @return string Texto traduzido
 *
 */
function __r()
{
    return _rr( func_get_args() );
}

/**
 *
 * Exibe string manipulada e traduzida
 *
 */
function _er()
{
    echo _rr( func_get_args() );
}

/**
 *
 * Traduz os argumentos das funções
 *
 * @param array $args Parâmetros de manipulação de string
 * @return string Texto traduzido
 *
 */
function _rr( $args )
{
    $args[ 0 ] = __( $args[ 0 ], TEXT_DOMAIN );
    return ( count( $args ) > 1 ) ? call_user_func_array( 'sprintf', $args ) : $args[ 0 ];
}

/**
 *
 * Mantém a tradução para requisições AJAX
 *
 * @global string $locale Localização atual definida em wp-config.php
 * @global string $sitepress Referência ao plugin WPML
 * @return string Localização correta
 *
 */
function custom_locale()
{
    global $locale, $sitepress;
    if ( !session_id() )
        session_start();

    if ( !defined( 'DOING_AJAX' ) ) {
        $locale = get_locale();
        $_SESSION[ 'locale' ] = $locale;
    } else {
        $locale = $_SESSION[ 'locale' ];
        remove_filter( 'locale', array( $sitepress, 'locale' ) );
        add_filter( 'locale', function(){
            return $_SESSION[ 'locale' ];
        }, 1 );
    }
    return $locale;
}

// }}}

?>