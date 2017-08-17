<?php

// Arquivo responsável por carregar o WordPress
$load_file = 'wp-load.php';
if ( !defined( 'ABSPATH' ) && file_exists( $load_file ) )
    require $load_file;

// add_action é uma das funções que são reconhecidas logo após o WP ter sido carregado
if ( !function_exists( 'add_action' ) ) {
    $r = array(
        'status'    => 'error',
        'content'   => 'Não foi possível carregar o WordPress...'
    );
} else {

    $s = ( isset( $_GET[ 's' ] ) ) ? sanitize_text_field( $_GET[ 's' ] ) : false;

    if ( !$s ) {
        $r = array(
            'status'    => 'error',
            'content'   => 'Informe quais os termos de sua busca.'
        );
    } else {
        $count = ( isset( $_GET[ 'count' ] ) ) ? (int) $_GET[ 'count' ] : 5;

        // o parâmetro s (search) define quais os termos a serem pesquisados
        $q = new WP_Query(
            array(
                'post_type'         => 'post',
                'posts_per_page'    => $count,
                's'                 => $s
            )
        );
        if ( !$q->have_posts() ) {
            $r = array(
                'status'    => 'error',
                'content'   => 'Nenhum resultado foi encontrado!'
            );
        } else {
            $r = array(
                'status'    => 'success',
                'content'   => array()
            );
            while ( $q->have_posts() ) {
                $q->the_post();
                $row = array(
                    'title' => get_the_title(),
                    'url'   => get_permalink(),
                    'date'  => get_the_date( 'Y-m-d H:i:s' )
                );
                array_push( $r[ 'content' ], $row );
            }
        }
    }

}

// retorno em formato JSON de um array com o status e o resultado da consulta ou mensagem de erro
echo json_encode( $r );
exit;