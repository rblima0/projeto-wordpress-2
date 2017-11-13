<?php

/*
 *
 * Função para carregamento automático das classes do framework
 * @param string $class_name Recebe o nome da classe requisitada
 *
 */
function root_autoload( $class_name )
{
    // ordem alfabética
    $components = array(
        'code',
        'cpt',
        'dashboard',
        'form',
        'main',
        'metabox',
        'option',
        'term',
        'theme',
        'user'
    );
    $file_name = strtolower( $class_name );
    if ( in_array( $file_name, $components ) ) {
        $file = PATH_LIB . 'class/' . $file_name . '.php';
        if ( file_exists( $file ) )
            require_once $file;
    }
}

if ( function_exists( 'spl_autoload_register' ) )
    spl_autoload_register( 'root_autoload' );

?>