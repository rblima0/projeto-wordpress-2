<?php

function root_autoload( $class_name )
{
    if ( class_exists( $class_name ) )
        return false;

    $class_lower = strtolower( $class_name );
    $file_name   = str_replace( '_' , '-', $class_lower );

    $paths = array(
        SRC_PATH,
        SRC_PATH . 'screen/'
    );

    $file = false;
    foreach( $paths as $path ) {
        $f = $path . $file_name . '.php';
        if ( file_exists( $f ) ) {
            $file = $f;
            break;
        }
    }

    if ( $file )
        require $file;
}

if ( function_exists( 'spl_autoload_register' ) )
    spl_autoload_register( 'root_autoload' );