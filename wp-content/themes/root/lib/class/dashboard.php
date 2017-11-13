<?php

/**
 *
 * Manipula a inserção e recuperação de valores no Dashboard
 *
 */
abstract class Dashboard
{

    /**
     *
     * @var array Colunas ordenáveis na listagem de resultados
     *
     */
    protected static $cols;

    /**
     *
     * Executa a atualização das regras de reescrita de URL's de acordo com a versão do tema
     * As regras somente são reescritas caso a versão do tema seja diferente daquela armazenada no banco de dados
     *
     */
    protected function flush()
    {
        run_once( 'root_flush_rules', 'flush_rewrite_rules', THEME_VERSION );
    }

    /**
     *
     * Trata os dados antes de serem salvos
     *
     * @param array $fields Campos a serem processados
     * @return array Lista de valores formatada
     *
     */
    protected static function prepare_save( $fields )
    {
        $meta = array();
        if ( is_array( $fields ) && !empty( $fields ) ) {
            $not_save = self::get_empty_types();
            foreach ( $fields as $key => $field ) {
                if ( in_array( $field[ 'type' ], $not_save ) )
                    continue;

                $v = false;
                $field_name = $field[ 'name' ];
                if ( isset( $_POST[ $field_name ] ) )
                    $v = ( isset( $field[ 'html' ] ) ) ? wp_kses( $_POST[ $field_name ] ) : sanitize_text_field( $_POST[ $field_name ] );

                $fields[ $key ][ 'value' ] = $v;

                $m = ( isset( $field[ 'meta' ] ) ) ? $field[ 'meta' ] : $field[ 'name' ];
                if ( !isset( $meta[ $m ] ) )
                    $meta[ $m ] = array();

                $meta[ $m ][ $field_name ] = $v;
            }
        }
        return $meta;
    }

    /**
     *
     * Verifica se o valor (serializado ou não) está vazio
     *
     * @param array $value Valor em inspeção
     * @return boolean Vazio ou preenchido
     *
     */
    private static function is_empty_value( $value )
    {
        $blank = true;
        if ( count( $value ) > 1 ) {
            foreach( $value as $v ) {
                if ( $v ) {
                    $blank = false;
                    break;
                }
            }
        } else {
            $value = array_shift( $value );
            if ( $value )
                $blank = false;
        }
        return $blank;
    }

    /**
     *
     * Salva os meta dados em forma única ou serializada
     *
     * $args = array(
     *  'object_type'   => 'post', 'comment', 'user', 'term'
     *  'object_id'     => $object_id,
     *  'fields'        => $fields
     * );
     *
     * @param array $args Recebe o tipo do objeto, ID e os campos a serem processados
     * @return int|boolean ID do objeto em caso de sucesso, do contrário false
     *
     */
    protected static function meta_save( $args )
    {
        $meta = self::prepare_save( $args[ 'fields' ] );
        // meta = array( meta_key => array( name => value ) )
        foreach ( $meta as $key => $value ) {
            if ( !self::is_empty_value( $value ) )
                update_metadata( $args[ 'object_type' ], $args[ 'object_id' ], $key, self::the_value( $value, $key ) );
            else // if ( $blank  && $meta_old )
                delete_metadata( $args[ 'object_type' ], $args[ 'object_id' ], $key );
        }

        return $args[ 'object_id' ];
    }

    /**
     *
     * Salva as opções de modo único ou serializado
     *
     * @param array $fields Campos a serem processados
     *
     */
    protected static function option_save( $fields )
    {
        $meta = self::prepare_save( $fields );
        foreach ( $meta as $key => $value ) {
            if ( !self::is_empty_value( $value ) )
                update_option( $key, self::the_value( $value, $key ) );
            else
                delete_option( $key );
        }
    }

    private static function the_value( $value, $key )
    {
        return ( isset( $value[ $key ] ) ) ? array_shift( $value ) : $value;
    }

    /**
     *
     * Recupera os meta valores de um objeto
     *
     * $args = array(
     *  'object_type'   => 'post', 'comment', 'user', 'term'
     *  'object_id'     => $object_id,
     *  'fields'        => $fields
     * );
     *
     *
     * @param array $args Tipo de objeto, ID e os campos a serem recuperados
     * @return array|boolean Os valores do objeto, ou false
     *
     */
    protected static function meta_values( $args )
    {
        if ( is_array( $args[ 'fields' ] ) && !empty( $args[ 'fields' ] ) ) {
            $values = array();
            $fields = $args[ 'fields' ];
            $meta = get_metadata( $args[ 'object_type' ], $args[ 'object_id' ] );

            foreach( $fields as $field ) {
                $f = $field[ 'name' ];
                $key = ( isset( $field[ 'meta' ] ) ) ? $field[ 'meta' ] : $field[ 'name' ];
                if ( isset( $meta[ $key ] ) ) {
                    $v = maybe_unserialize( $meta[ $key ][0] );
                    if ( is_array( $v ) && isset( $v[ $f ] ) )
                        $v = $v[ $f ];

                    $values[ $f ] = $v;
                }
            }
            return $values;
        }
        return false;
    }

    /**
     *
     * Retorna o objeto em edição
     *
     * @return string Nome da taxonomia, do tipo de post ou o tipo de dado usuário
     *
     */
    protected static function get_screen_object()
    {
        $object = false;
        $s = get_current_screen();
        switch ( $s->base )
        {
            case 'edit-tags':
                $object = $s->taxonomy;
                break;
            case 'edit':
                $object = $s->post_type;
                break;
            case 'users':
                $object = 'user';
                break;
        }
        return $object;
    }

    /**
     *
     * Recupera quais as colunas passíveis de ordenação de acordo com a tela atual
     *
     * @return array Colunas que podem ser ordenadas
     *
     */
    protected static function get_cols_order()
    {
        $c = array();
        $obj = self::get_screen_object();
        if ( isset( self::$cols[ $obj ] ) ) {
            $custom_cols = self::$cols[ $obj ];
            foreach( $custom_cols as $col ) {
                if ( isset( $col[ 'order' ] ) )
                    $c[ $col[ 'id' ] ] = $col[ 'id' ];
            }
        }
        return $c;
    }

    /**
     *
     * Identifica se existem colunas a serem ordenadas de modo personalizado
     *
     * @param array $cols Colunas a serem checadas
     * @return boolean Sucesso ou falha
     *
     */
    protected static function has_cols_order( $cols )
    {
        $has_order = false;
        foreach ( $cols as $c ) {
            if ( isset( $c[ 'order' ] ) ) {
                $has_order = true;
                break;
            }
        }
        return $has_order;
    }

    /**
     *
     * Define o tipo de ordenação (texto, numérica) de acordo com a coluna escolhida
     *
     * @param string $key Coluna a ser ordenada
     * @return string Tipo do campo
     *
     */
    protected static function get_order_type( $key )
    {
        $type = 'string';
        $obj = self::get_screen_object();
        $custom_cols = self::$cols[ $obj ];
        foreach( $custom_cols as $col ) {
            if ( ( $col[ 'id' ] == $key ) && isset( $col[ 'type' ] ) ) {
                $type = $col[ 'type' ];
                break;
            }
        }
        return $type;
    }

    /**
     *
     * Retorna os campos que não precisam de nome
     *
     * @return array id dos campos
     *
     */
    protected static function get_empty_types()
    {
        return array( 'sep', 'submit', 'reset' );
    }

}

// @todo filters on the wp_list_table of the all lists

?>