<?php

/**
 *
 * Tratamento dos Metaboxes
 *
 */
class Metabox extends Dashboard
{

    /**
     *
     * @var array Todos os metaboxes personalizados
     *
     */
    private static $boxes;

    /**
     *
     * Adiciona um novo metabox
     *
     * @param string $id Identificador único
     * @param string|array $post_type Tipos de posts ao qual o metabox será adicionado
     * @param array $args Propriedades adicionais do metabox http://codex.wordpress.org/Function_Reference/add_meta_box
     *
     */
    public static function add( $id, $post_type, $args=array() )
    {
        self::init();

        $defaults = array(
            'title'     => __r( 'Additional information' ),
            'context'   => 'normal',
            'priority'  => 'high',
            'args'      => null,
            'callback'  => null
        );
        $attr = array(
            'id'        => $id,
            'post_type' => $post_type,
            'form'      => new Form()
        );
        $box = array_merge( $defaults, $args, $attr );

        self::$boxes[ $id ] = apply_filters( 'metabox_register', $box );
    }

    /**
     *
     * Inicializador da classe, dispara os devidos gatilhos
     *
     */
    public static function init()
    {
        if ( !is_array( self::$boxes ) ) {
            self::$boxes = array();

            add_filter( 'form_field',   array( 'Metabox', 'fields_format' ), 2, 3 );

            add_action( 'admin_init',   array( 'Metabox', 'admin_init' ) );
            add_action( 'save_post',    array( 'Metabox', 'save' ) );
        }
    }

    /**
     *
     * Insere campos de formulário ao metabox
     *
     * @param string $metabox Identificador único do metabox
     * @param array $fields Lista de campos
     *
     */
    public static function add_fields( $metabox, $fields )
    {
        self::$boxes[ $metabox ][ 'form' ]->add_fields( $fields, apply_filters( 'metabox_fields_prefix', '' ) );
    }

    /**
     *
     * Inicializador do Dashboard, insere os metaboxes ao sistema
     *
     */
    public static function admin_init()
    {
        foreach ( self::$boxes as $b )
            add_meta_box( $b[ 'id' ], $b[ 'title' ], array( 'Metabox', 'edit' ), $b[ 'post_type' ], $b[ 'context' ], $b[ 'priority' ], $b[ 'args' ] );
    }

    /**
     *
     * Responsável pelo conteúdo do metabox
     *
     * $metabox = array(
     *  'id'        => '',
     *  'title      => '',
     *  'callback'  => '',
     *  'args'      => ''
     * );
     *
     * @param object $post Objeto Post do WordPress
     * @param array $metabox Atributos do metabox
     *
     */
    public static function edit( $post, $metabox )
    {
        $b = self::$boxes[ $metabox[ 'id' ] ];

        do_action( 'custom_metabox', $b );

        if ( $b[ 'callback' ] )
            call_user_func( $b[ 'callback' ], $post );

        if ( $b[ 'form' ]->has_fields ) {
            $b[ 'form' ]->add_field(
                array(
                    'name'  => 'root_box',
                    'type'  => 'hidden',
                    'value' => '1'
                )
            );
            echo apply_filters( 'metabox_open_form', '<table class="form-table">' );

            self::set_values( $post );

            $b[ 'form' ]->render();

            echo apply_filters( 'metabox_close_form', '</table>' );
        }
    }

    /**
     *
     * Filtro para formatação dos campos do metabox
     *
     * @return string HTML formatado de acordo com os padrões do Dashboard
     *
     */
    public static function fields_format( $html, $html_field, $f )
    {
        // %s = id, label, html_field
        unset( $html );
        if ( $f[ 'type' ] == 'sep' )
            return apply_filters( 'metabox_fields_sep_format', '<tr><th id="%s" colspan="2"><strong>%s</strong></th></tr>', $html_field, $f );
        else
            return apply_filters( 'metabox_fields_format', '<tr><th><label for="%s">%s</label></th><td>%s</td></tr>', $html_field, $f );
    }

    /**
     *
     * Verifica as condições do envio das informações e salva os campos como meta valores
     *
     * @param integer $post_id Identificador do Post
     *
     */
    public static function save( $post_id )
    {
        $post_type = ( isset( $_POST[ 'post_type' ] ) ) ? $_POST[ 'post_type' ] : '';

        if (
            $post_type &&
            current_user_can( 'edit_post', $post_id ) &&
            isset( $_POST[ 'root_box' ] )
        ) {
            $fields = array();
            foreach ( self::$boxes as $b ) {
                if ( $b[ 'post_type' ] == $post_type )
                    $fields = array_merge( $fields, $b[ 'form' ]->get_fields() );
            }
            $args = array(
                'object_type'   => 'post',
                'object_id'     => $post_id,
                'fields'        => $fields
            );
            do_action( 'metabox_save', $args[ 'object_type' ], $args[ 'object_id' ] );
            self::meta_save( $args );
        }
    }

    /**
     *
     * Define os valores dos campos quando existentes no banco de dados
     *
     * @param object $post Objeto Post do WordPress
     *
     */
    private static function set_values( $post )
    {
        if ( !in_array( $post->post_status, array( 'auto-draft', 'trash', 'inherit' ) ) ) {
            $fields = array();
            foreach ( self::$boxes as $b ) {
                if ( $b[ 'post_type' ] == $post->post_type )
                    $fields = array_merge( $fields, $b[ 'form' ]->get_fields() );
            }

            $args = array(
                'object_type'   => 'post',
                'object_id'     => $post->ID,
                'fields'        => $fields
            );
            $values = self::meta_values( $args );
            foreach ( self::$boxes as $b ) {
                if ( $b[ 'post_type' ] == $post->post_type ) {
                    foreach( $values as $f => $v )
                        $b[ 'form' ]->set_field_value( $f, $v );
                }
            }
        }
    }

}

?>