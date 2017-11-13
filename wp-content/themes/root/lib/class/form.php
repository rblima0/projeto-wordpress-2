<?php

/**
 *
 * Manipulação de formulários
 *
 */
class Form
{

    /**
     *
     * @var string Marcação HTML do form
     *
     */
    private $html;

    /**
     *
     * @var array Lista dos campos do formulário
     *
     */
    private $fields;

    /**
     *
     * @var array Estabelece se o formulário possui campos vinculados a ele
     *
     */
    public $has_fields;

    /**
     *
     * Construtor do formulário
     *
     * @param string $action Endereço destino da requisição
     * @param string $method Método de envio GET|POST
     * @param array $attr Atributos adicionais
     *
     */
    public function __construct( $action=null, $method='post', $attr=array() )
    {
        $attr[ 'type' ] = 'form';

        if ( is_null( $action ) ) {
            $html = '';
        } else {
            $html = sprintf(
                '<form action="%s" method="%s"%s>',
                $action,
                $method,
                $this->print_attr( $attr )
            );
        }

        $this->html = $html;
        $this->fields = array();
        $this->has_fields = false;

        add_filter( 'form_field', array( $this, 'fields_format' ), 1, 3 );
    }

    /**
     *
     * Checa se o atributo eviado possui um valor permitido
     *
     * @param string $type Tipo de campo
     * @param string $attr Atributo a ser inserido
     * @return boolean Permitido ou não o uso do atributo para o tipo de campo específico
     *
     */
    private function is_attr( $type, $attr )
    {
        $attrs = array(
            'type',
            'name',
            'value',
            'class',
            'id',
            'required',
            'tabindex'
        );
        switch ( $type ) {
            case 'form':
                $attr_del = array( 'type', 'name', 'value', 'required' );
                array_push( $attrs , 'enctype' );
                break;
            case 'text':
                array_push( $attrs , 'placeholder' );
                break;
            case 'textarea':
                $attr_del = array( 'type', 'value' );
                array_push( $attrs , 'placeholder', 'cols', 'rows' );
                break;
            case 'select':
                $attr_del = array( 'type', 'value' );
                array_push( $attrs , 'size', 'multiple' );
                break;
            case 'radio':
                array_push( $attrs , 'checked' );
                break;
            case 'checkbox':
                $attr_del = array( 'value' );
                array_push( $attrs , 'checked' );
                break;
            case 'reset':
            case 'submit':
                $attr_del = array( 'required' );
                break;
        }

        if ( isset( $attr_del ) ) {
            foreach ( $attr_del as $a ) {
                $k = array_search( $a, $attrs );
                unset( $attrs[ $k ] );
            }
        }

        return ( in_array( $attr, $attrs ) );
    }

    /**
     *
     * Insere atributos nas referidas tags dos campos
     *
     * @param array $f Relação de atributos e seus respectivos valores
     * @return string Marcação HTML dos atributos
     *
     */
    private function print_attr( $f )
    {
        $html = '';
        foreach ( $f as $a=>$v ) {
            if ( $v && $this->is_attr( $f[ 'type' ], $a ) ) {
                $html .= sprintf( ' %s="%s"', $a, $v );
            }
        }
        return $html;
    }

    /**
     *
     * Campo do tipo input
     *
     * @param array $f Dados do campo
     * @return string Marcação HTML
     *
     */
    private function input( $f )
    {
        $html = sprintf(
            '<input%s />',
            $this->print_attr( $f )
        );
        return $html;
    }

    /**
     *
     * Campo do tipo textarea
     *
     * @param array $f Dados do campo
     * @return string Marcação HTML
     *
     */
    private function textarea( $f )
    {
        $size = array(
            'cols'  => 50,
            'rows'  => 5
        );
        $keys = array_keys( $size );
        foreach ( $keys as $s ) {
            if ( !isset( $f[ $s ] ) )
                $f[ $s ] = $size[ $s ];
        }
        $html = sprintf(
            '<textarea%s>%s</textarea>',
            $this->print_attr( $f ),
            $f[ 'value' ]
        );
        return $html;
    }

    /**
     *
     * Campo do tipo select
     *
     * @param array $f Dados do campo
     * @return string Marcação HTML
     *
     */
    private function select( $f )
    {
        if ( is_array( $f[ 'opt' ] ) ) {
            $value = array( $f[ 'value' ] );

            $mult = false;
            if ( isset( $f[ 'mult' ] ) ) {
                unset( $f[ 'mult' ] );
                $mult = true;
            } else if ( isset( $f[ 'multiple' ] ) ) {
                $mult = true;
            }
            if ( $mult ) {
                $f[ 'multiple' ] = 'multiple';
                $f[ 'name' ] .= '[]';
            }

            $html = sprintf(
                '<select%s>%s</select>',
                $this->print_attr( $f ),
                $this->select_options( $f[ 'opt' ], $value )
            );
        }
        else {
            $html = __r( 'No options to the select field.' );
        }
        return $html;
    }

    /**
     *
     * Define opções ao campo do tipo select
     *
     * @param array $opions Relação de valores e rótulos
     * @param array $values Valores a serem selecionados
     * @return string Marcação HTML
     *
     */
    private function select_options( $options, $values )
    {
        $html = '';
        foreach ( $options as $v => $l ) { // value, label
            $html .= '<option value="' . $v . '"';
            foreach ( $values as $value ) {
                if ( $v == $value ) {
                    $html .= ' selected="selected"';
                    break;
                }
            }
            $html .= '>' . $l . '</option>';
        }

        return $html;
    }

    /**
     *
     * Campo do tipo radio
     *
     * @param array $f Dados do campo
     * @return string Marcação HTML
     *
     */
    private function radio( $f )
    {
        $html = '';
        if ( count( $f[ 'opt' ] ) > 1 ) {
            $value = $f[ 'value' ];
            foreach ( $f[ 'opt' ] as $v => $l ) { // value, label
                $f[ 'value' ] = $v;
                if ( $value == $v ) {
                    $f[ 'checked' ] = 'checked';
                } else if ( isset( $f[ 'checked' ] ) ) {
                    unset( $f[ 'checked' ] );
                }

                $input = sprintf(
                    '<input%s />%s',
                    self::print_attr( $f ),
                    $l
                );
                $opt = sprintf(
                    '<label>%s</label>',
                    $input
                );
                $html .= $opt;
            }
        }
        return $html;
    }

    /**
     *
     * Campo do tipo checkbox
     *
     * @param array $f Dados do campo
     * @return string Marcação HTML
     *
     */
    private function checkbox( $f )
    {
        $html = '';
        if ( isset( $f[ 'label' ] ) ) {
            if ( isset( $f[ 'checked' ] ) || ( $f[ 'value' ] == 'on' ) )
                $f[ 'checked' ] = 'checked';

            $input = sprintf(
                '<input%s />%s',
                self::print_attr( $f ),
                ( isset( $f[ 'desc' ] ) ) ? $f[ 'desc' ] : ''
            );
            $opt = sprintf( '<label>%s</label>', $input );
            $html .= $opt;
        }
        return $html;
    }

    /**
     *
     * Insere um campo ao formulário
     * São permitidos campos do tipo:
     * text, textarea, select, radio, checkbox, reset, submit, password, number, date, time, email, hidden
     *
     * $field = array(
     *  'label' => '',
     *  'name'  => '',
     *  'value' => '',
     *  'type'  => '', // Defina o tipo do campo
     *  'id'    => ''
     * );
     *
     * Além desses atributos básicos acima são permitidos os atributos HTML da tag;
     * por exemplo class, required, placeholder (ph), etc
     *
     * Atributos são aceitos de acordo com o tipo também:
     * checkbox aceita o parâmetro checked
     * select aceita o parâmetro multiple (mult)
     * textarea cols, rows
     *
     * Os tipos radio e select devem possuir o parâmetro opt com as opções desejadas
     * opt é um array associativo com label => value
     * em um select de cores o array opt seria algo como
     * 'opt' => array( 'red' => 'Red', 'lima-green' => 'Lima Green' );
     *
     * @param array $f Atributos do campo
     * @return string|boolean Mensagens de erro ou verdadeiro caso o campo tenha sido adicionado
     *
     */
    public function add_field( $f )
    {
        foreach ( $this->fields as $field ) {
            if ( isset( $f[ 'name' ] ) && ( $f[ 'name' ] == $field[ 'name' ] ) )
                return __r( 'There is already a field with the same name!' );
        }

        $field = array(
            'label' => '',
            'name'  => '',
            'value' => '',
            'type'  => '',
            'id'    => ''
        );
        $f = wp_parse_args( $f, $field );
        if ( !in_array( $f[ 'type' ], $this->get_fields_anonymous() ) && !$f[ 'name' ] ) {
            return __r( 'You need to define a name to the field!' );
        } else {
            if ( isset( $f[ 'req' ] ) ) {
                $f[ 'required' ] = 'required';
                unset( $f[ 'req' ] );
            }

            if ( !$f[ 'name' ] )
                $f[ 'name' ] = 'field-' . count( $this->fields );

            if ( !$f[ 'type' ] )
                $f[ 'type' ] = 'text';

            if ( !$f[ 'id' ] )
                $f[ 'id' ] = $this->get_field_id( $f );

            if ( isset( $f[ 'ph' ] ) ) {
                $f[ 'placeholder' ] = $f[ 'ph' ];
                unset( $f[ 'ph' ] );
            }
            array_push( $this->fields, $f );
            $this->has_fields = true;
        }
        return true;
    }

    /**
     *
     * Filtro para formatação de campos específicos
     *
     * @param string $html Marcação HTML padrão
     * @param string $html_field Marcação HTML do campo
     * @param array $f Atributos do campo
     * @return string Marcação HTML atualizada
     *
     */
    public function fields_format( $html, $html_field, $f )
    {
        switch ( $f[ 'type' ] )
        {
            case 'radio':
            case 'checkbox':
                unset( $html );
                return sprintf(
                    "<div><span>%s</span> %s</div>",
                    ( isset( $f[ 'label' ] ) ) ? $f[ 'label' ] : 'Escolha: ',
                    $html_field
                );
                break;
            case 'sep':
                unset( $html, $html_field );
                return sprintf(
                    "<div><strong>%s</strong></div>",
                    ( isset( $f[ 'label' ] ) ) ? $f[ 'label' ] : ''
                );
                break;
            case 'hidden':
                unset( $html );
                return $html_field;
                break;
            default:
                return $html;
                break;
        }
    }

    /**
     *
     * Insere vários campos de uma única vez
     *
     * @param array $fields Lista com todos os campos
     * @param string $prefix Define um prefixo ao nome do campo
     *
     */
    public function add_fields( $fields, $prefix=null )
    {
        if ( is_array( $fields ) && count( $fields ) ) {
            foreach ( $fields as $f ) {
                if ( $prefix )
                    $f[ 'name' ] = $prefix . $f[ 'name' ];

                $this->add_field( $f );
            }
        }
    }

    /**
     *
     * Define um id para o campo
     *
     * @param array $field Atributos do campo
     * @return string Identificador como [tipo do campo]-[nome do campo]
     *
     */
    private function get_field_id( $field )
    {
        // @fix usar expressão regular ao invés de str_replace
        $id = $field[ 'type' ] . '-' . str_replace( '_', '-', $field[ 'name' ] );
        return str_replace( '--', '-', $id );
    }

    /**
     *
     * Lista de campos que não precisam ter um nome definido
     *
     * @return array Lista dos campos
     *
     */
    private function get_fields_anonymous()
    {
        return array(
            'sep',
            'reset',
            'submit'
        );
    }

    /**
     *
     * Exibe em tela o formulário formatado com os campos inseridos
     *
     */
    public function render()
    {
        $html = apply_filters( 'form_header', $this->html );
        echo $html;

        do_action( 'before_form_render' );

        echo apply_filters( 'form_render', $this->render_html(), $this->fields );

        do_action( 'after_form_render' );

        if ( $html )
            echo apply_filters( 'form_footer', '</form>' );
    }

    /**
     *
     * Transforma cada campo em sua devida marcação HTML
     *
     */
    private function render_html()
    {
        if ( is_array( $this->fields ) ) {
            $html = '';
            foreach ( $this->fields as $f ) {
                switch ( $f[ 'type' ] ) {
                    case 'text':
                    case 'password':
                    case 'number':
                    case 'date':
                    case 'time':
                    case 'email':
                    case 'reset':
                    case 'submit':
                    case 'hidden':
                        $html_field = $this->input( $f );
                        break;
                    case 'textarea':
                        $html_field = $this->textarea( $f );
                        break;
                    case 'select':
                        $html_field = $this->select( $f );
                        break;
                    case 'radio':
                        $html_field = $this->radio( $f );
                        break;
                    case 'checkbox':
                        $html_field = $this->checkbox( $f );
                        break;
                    default:
                        $html_field = __r( 'Invalid field...' );
                        break;
                }

                $html .= sprintf(
                    apply_filters( 'form_field', '<div><label for="%s">%s</label>%s</div>', $html_field, $f, ( $html ) ),
                    $f[ 'id' ],
                    $f[ 'label' ],
                    $html_field
                );
            }
            return $html;
        }
    }

    /**
     *
     * Retorna todos os campos adicionados ao formulário
     *
     * @return array Lista dos campos
     *
     */
    public function get_fields()
    {
        return $this->fields;
    }

    /**
     *
     * Retorna todos os nomes dos campos adicionados ao formulário
     *
     * @return array Lista dos campos
     *
     */
    public function get_fields_names()
    {
        $names = array();
        foreach ( $this->fields as $f )
            array_push( $names, $f[ 'name' ] );

        return $names;
    }

    /**
     *
     * Permite inserir um valor ao campo depois de ter sido adicionado ao formulário
     *
     * @param string $field Nome do campo
     * @param mixed $value Valor do campo
     *
     */
    public function set_field_value( $field, $value )
    {
        foreach ( $this->fields as $k => $f ) {
            if ( $f[ 'name' ] == $field ) {
                $this->fields[ $k ][ 'value' ] = $value;
                break;
            }
        }
    }

    /**
     *
     * Remove um campo da lista de campos personalizados
     *
     * @param string $field_name Nome do campo
     *
     */
    public function delete_field( $field_name )
    {
        foreach ( $this->fields as $k => $f ) {
            if ( $f[ 'name' ] == $field_name ) {
                unset( $this->fields[ $k ] );
                break;
            }
        }
    }

}

// @todo trabalhar com fieldset, legend, button, upload, mult-ulpload, select optgroup

?>