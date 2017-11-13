<?php

do_action( 'root_core' );

if ( !function_exists( 'get_simple_data' ) ) {

    /**
     *
     * Recupera o conteúdo em formato de uma lista a ser usada em campos de formulário do tipo select
     *
     * @global object $wpdb Classe de banco de dados do WordPress http://codex.wordpress.org/Class_Reference/wpdb
     * @param string $post_type Tipo de post a ser recuperado
     * @param string $where Condições adicionais de pesquisa
     * @return array|boolean Lista com os resultados ou falso, caso não existam registros no banco de dados
     *
     */
    function get_simple_data( $post_type, $where='' )
    {
        global $wpdb;
        $data_fields = apply_filters( 'simple_data_fields', array( 'ID', 'post_title' ) );
        if ( is_array( $data_fields ) && ( count( $data_fields ) > 1 ) ) {
            $fields = implode( ', ', $data_fields );
            $orderby = apply_filters( 'simple_data_orderby', 'post_title' );

            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT {$fields} FROM {$wpdb->posts} WHERE post_type=%s AND post_status=%s %s ORDER BY {$orderby} ASC",
                    $post_type,
                    'publish',
                    $where
                )
            );
            if ( is_array( $rows ) && count( $rows ) ) {
                $r = array();
                foreach( $rows as $row )
                    $r[ $row->ID ] = $row->post_title;

                return $r;
            }
        }
        return false;
    }

}

if ( !function_exists( 'run_once' ) ) {

    /**
     *
     * Executa a função callback apenas uma vez, caso não exista a opção no banco de dados
     *
     * @param string $option_name Nome da opção a ser checada
     * @param function $function Função a ser disparada caso a opção não exista
     * @param mixed $value Valor a ser comparado
     *
     */
    function run_once( $option_name, $function, $value=false )
    {
        $opt = get_option( $option_name );
        if ( !$opt || ( $opt !== $value ) ) {
            update_option( $option_name, $value );
            if ( function_exists( $function ) )
                call_user_func( $function );
        }
    }

}

if ( !function_exists( 'get_term_meta' ) ) {

    /**
     *
     * Recupera meta dados de um termo
     *
     * @param int $term_id ID do termo.
     * @param string $key Chave a ser recupera, por padrão retorna todas
     * @param bool $single Se deseja (true) ou não (false) retornar um único valor
     * @return array|string Lista de itens ou apenas um valor se $single for verdadeiro
     *
     */
    function get_term_meta( $term_id, $key='', $single=false )
    {
        return get_metadata( 'term', $term_id, $key, $single );
    }

}

if ( !function_exists( 'add_term_meta' ) ) {

    /**
     *
     * Adiciona um meta dado a um termo
     *
     * @param int $term_id ID do termo
     * @param string $meta_key Chave do meta dado
     * @param mixed $meta_value Valor a ser adicionado
     * @param bool $unique Se verdadeiro e caso exista o termo, esse não será inserido
     * @return int|bool Meta ID ou falso
     *
     */
    function add_term_meta( $term_id, $meta_key, $meta_value, $unique=false )
    {
        return add_metadata( 'term', $term_id, $meta_key, $meta_value, $unique );
    }

}

if ( !function_exists( 'delete_term_meta' ) ) {

    /**
     *
     * Apaga os meta valores do termo passado como parâmetro
     *
     * @param int $term_id ID do termo
     * @param string $meta_key Chave do meta dado
     * @param mixed $meta_value Valor do campo
     * @return bool Sucesso ou falha
     *
     */
    function delete_term_meta( $term_id, $meta_key, $meta_value='' )
    {
        return delete_metadata( 'term', $term_id, $meta_key, $meta_value );
    }

}

if ( !function_exists( 'update_term_meta' ) ) {

    /**
     *
     * Atualiza os meta valores de um termo
     *
     * @param int $term_id ID do termo
     * @param string $meta_key Chave do meta dado
     * @param mixed $meta_value Valor atualizado do campo
     * @param mixed $prev_value Se preenchido, remove esse antigo valor
     * @return bool Sucesso ou falha
     *
     */
    function update_term_meta( $term_id, $meta_key, $meta_value, $prev_value='' )
    {
        return update_metadata( 'term', $term_id, $meta_key, $meta_value, $prev_value );
    }

}

if ( !function_exists( 'set_url' ) ) {

    /**
     *
     * Formata uma url com um protocolo de acesso
     *
     * @param string $url Endereço a ser formatado
     * @param string $protocol Protocolo a ser usado
     * @return string Url com o protocolo definido
     *
     */
    function set_url( $url, $protocol='http' )
    {
       if ( !$url )
           return '#';

       $protocols = apply_filters(
           'url_protocols',
           array(
               'http',
               'https',
               'ftp'
           )
        );

       if ( preg_match( '/' . implode( '|', $protocols ) . '/', $url ) )
           return $url;
       else
           return $protocol . '://' . $url;
    }

}

if ( !function_exists( 'in_localhost' ) ) {

    /**
     *
     * Verifica se aplicação está sendo executada em ambiente local
     *
     * @return boolean Verdadeiro ou falso
     *
     */
    function in_localhost()
    {
       $domains = array( 'localhost', '127.0.0.1' );
       return in_array( $_SERVER[ 'HTTP_HOST' ], $domains );
    }

}

if ( !function_exists( 'zerofill' ) ) {

    /**
     *
     * Preenche com '0' (zero) a esquerda o número passado como parâmetro
     *
     * @param integer $num Número a ser formatado
     * @param integer $zerofill Quantidade de zeros a inserir a esquerda do número
     * @return string Número formatado
     *
     */
    function zerofill( $num, $zerofill=2 )
    {
        return str_pad( $num, $zerofill, '0', STR_PAD_LEFT );
    }

}

if ( !function_exists( 'get_html_attribute' ) ) {

    /**
     *
     * Recupera atributos de uma tag HTML
     *
     * @version 1.0
     *
     * @param type $attr Atributo requerido
     * @param type $html_tag Tag HTML a ser pesquisada
     * @return string|boolean Valor do atributo ou falso
     */
    function get_html_attribute( $attr, $html_tag )
    {
        $re = '/' . preg_quote( $attr ) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
        if ( preg_match( $re, $html_tag, $match ) )
            return urldecode( $match[2] );

        return false;
    }

}

if ( !function_exists( 'date_french_to_english' ) ) {

    /**
     *
     * Converte datas do padrão francês (brasileiro) ao padrão inglês
     *
     * @param string $date_french Data formatada como dd/mm/YYYY
     * @return string|boolean Data sob a nova formatação ou falso no caso de data inválida
     *
     */
    function date_french_to_english( $date_french )
    {
        sscanf( $date_french, '%d/%d/%d', $d, $m, $y );

        $dt = sprintf( '%s-%s-%s', $y, zerofill( $m ), zerofill( $d ) );
        $date_check = date( 'd/m/Y', strtotime( $dt ) );
        if ( $date_french !== $date_check )
            $dt = false;

        return $dt;
    }

}

if ( !function_exists( 'datetime_br' ) ) {

    /**
     *
     * Formata data por extenso em português do Brasil
     *
     * @param string $f Formato a ser retornado http://php.net/manual/pt_BR/function.strftime.php
     * @param timestamp $time Tempo a ser formatado
     * @return string Data formatada
     *
     */
    function datetime_br( $f, $time )
    {
        setlocale( LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese" );
        return strftime( $f, $time );
    }

}

if ( !function_exists( 'wp_list_posts' ) ) {

    /**
     *
     * Exibe em tela uma lista com os links dos posts
     *
     * @param array $args Definições da consulta
     *
     */
    function wp_list_posts( $args=array() )
    {
        $defaults = array(
            'post_type'         => 'post',
            'posts_per_page'    => 10
        );
        $args = wp_parse_args( $args, $defaults );
        $rows = get_posts( $args );
        if ( count( $rows ) ) {
            printf( '<ul%s>', ( isset( $args[ 'class' ] ) ) ? ' class="' . $args[ 'class' ] . '"' : '' );
            foreach( $rows as $row ) {
                printf(
                    '<li><a href="%1$s" title="%2$s">%2$s</a></li>',
                    get_permalink( $row->ID ),
                    get_the_title( $row->ID )
                );
            }
            echo '</ul>';
        } else {
            printf( '<p>%s</p>', $args[ 'not_found' ] );
        }
    }

}

?>