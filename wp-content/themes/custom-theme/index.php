<?php

bloginfo( 'name' );

printf( '<p>%s</p>', get_bloginfo( 'description' ) );

// set_theme_mod( 'info', 'test' );
// remove_theme_mod( 'info' );
// remove_theme_mods();

echo '<pre>';
var_dump( get_theme_mods() );

echo 'Texto: ' . get_theme_mod( 'ct-text' ) . '<br />';

printf( '<p>Opção personalizada: %s</p>', get_option( 'ct-option' ) );

echo 'Opções: ';
print_r( get_option( 'ct-opt' ) );