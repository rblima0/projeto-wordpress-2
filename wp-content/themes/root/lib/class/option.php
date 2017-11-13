<?php

/**
 *
 * Gerencia as telas de opções personalizadas
 *
 */
class Option extends Dashboard
{

    /**
     *
     * Lista das telas criadas
     *
     * @var array
     *
     */
    private static $screens;

    /**
     *
     * Lista das abas criadas
     *
     * @var array
     *
     */
    private static $tabs;

    /**
     *
     * Inicializador de variáveis, dispara os gatilhos necessários ao recurso
     *
     */
    private static function init()
    {
        if ( !isset( self::$screens ) ) {
            self::$screens = array();

            add_action( 'admin_menu',   array( 'Option', 'menu' ) );
            add_filter( 'form_field',   array( 'Option', 'fields_format' ), 2, 4 );

            self::$tabs = array();
        }
    }

    /**
     *
     * Insere itens ao menu do Dashboard
     *
     */
    public static function menu()
    {
        foreach ( self::$screens as $id => $s ) {
            if ( !isset( $s[ 'parent' ] ) || $s[ 'parent' ] == 'root' )
                add_menu_page( $s[ 'title' ], $s[ 'title' ], $s[ 'role' ], $id, array( 'Option', 'edit' ) );
            else
                add_submenu_page( $s[ 'parent' ], $s[ 'title' ], $s[ 'title' ], $s[ 'role' ], $id, array( 'Option', 'edit' ) );
        }
    }

    /**
     *
     * Insere uma nova tela administrativa
     *
     * @param string $screen_id Identificador da tela
     * @param string|array $role Permissão de acesso
     * @param array $args Atributos da tela
     *
     */
    public static function add_screen( $screen_id, $role, $args=array() )
    {
        self::init();

        $defaults = array(
            'title' => __r( 'Page options' )
        );

        $screen_opt = array(
            'role'  => $role,
            'form'  => new Form( admin_url( 'admin.php?page=' . $screen_id ) )
        );
        self::$screens[ $screen_id ] = array_merge( $screen_opt, wp_parse_args( $args, $defaults ) );
    }

    /**
     *
     * Insere campos a tela personalizada definida via parâmetro
     *
     * @param string $screen_id Identificador da tela
     * @param array $fields Lista de campos
     *
     */
    public static function add_fields( $screen_id, $fields )
    {
        self::$screens[ $screen_id ][ 'form' ]->add_fields( $fields, apply_filters( 'option_fields_prefix', '' ) );
    }

    /**
     *
     * Processa e renderiza o conteúdo das telas
     *
     */
    public static function edit()
    {
        $screen = $_GET[ 'page' ];
        if ( isset( self::$screens[ $screen ] ) ) {

            $s = self::$screens[ $screen ];

            echo '<div class="wrap">';
            screen_icon( 'options-general' );
            echo '<h2>' . $s[ 'title' ] . '</h2>';

            self::save();

            self::tabs( $screen );

            do_action( 'option_screen', $screen, $s );

            if ( isset( $s[ 'callback' ] ) && function_exists( $s[ 'callback' ] ) )
                call_user_func( $s[ 'callback' ] );

            self::render_form( $s[ 'form' ], $screen );

            echo '</div>';
        }
    }


    /**
     *
     * Filtro para formatação dos campos da tela administrativa
     *
     * @return string HTML formatado de acordo com os padrões do Dashboard
     *
     */
    public static function fields_format( $html, $html_field, $f, $has_html )
    {
        unset( $html );
        switch ( $f[ 'type' ] )
        {
            case 'sep':
                $format = ( $has_html ) ? '</table>' : '';
                $format .= '<h3 id="%s">%s</h3><table class="form-table">';
                return apply_filters( 'option_fields_sep_format', $format, $html_field, $f );
                break;
            case 'hidden':
                return $html_field;
                break;
            default:
                return apply_filters( 'option_fields_format', '<tr><th><label for="%s">%s</label></th><td>%s</td></tr>' );
                break;
        }
    }

    /**
     *
     * Salva em banco de dados os dados enviados através das telas administrativas
     *
     */
    public static function save()
    {
        $screen_names = array_keys( self::$screens );
        if ( !empty( $_POST ) && isset( $_POST[ 'option_referer' ] ) && isset( $_POST[ 'option_nonce' ] ) ) {
            $referer = $_POST[ 'option_referer' ];
            if ( !in_array( $referer, $screen_names ) )
                wp_die( __r( 'Sending unauthorized data...' ) );

            if ( !check_admin_referer( 'option_screen', 'option_nonce' ) )
                wp_die( __r( 'Cheatin&#8217; uh?!' ) );

            if ( isset( self::$screens[ $referer ][ 'form' ] ) )
                self::option_save( self::$screens[ $referer ][ 'form' ]->get_fields() );

            if ( isset( self::$tabs[ $referer ] ) && isset( $_GET[ 'tab' ] ) ) {
                $tab = $_GET[ 'tab' ];
                foreach ( self::$tabs[ $referer ] as $t ) {
                    if ( $t[ 'name' ] == $tab ) {
                        self::option_save( $t[ 'form' ]->get_fields() );
                        break;
                    }
                }
            }

            printf( '<div class="updated"><p>%s</p></div>', __r( 'Successfully updated options!' ) );
        }
    }

    /**
     *
     * Recupera os valores da base de dados e os exibe dentro dos respectivos formulários
     *
     * @param object $form Objeto Form do framework
     *
     */
    private static function set_values( $form )
    {
        $opt = array();
        $fields = $form->get_fields();
        foreach ( $fields as $f ) {
            if ( in_array( $f[ 'type' ], self::get_empty_types() ) )
                continue;

            $value = false;
            if ( isset( $f[ 'meta' ] ) ) {
                $meta = $f[ 'meta' ];
                if ( !isset( $opt[ $meta ] ) )
                    $opt[ $meta ] = get_option( $meta );

                if ( isset( $opt[ $meta ][ $f[ 'name' ] ] ) )
                    $value = $opt[ $meta ][ $f[ 'name' ] ];
            } else {
                $value = get_option( $f[ 'name' ] );
            }

            if ( $value )
                $form->set_field_value( $f[ 'name' ], $value );
        }
    }

    /**
     *
     * Adiciona abas a uma tela administrativa
     *
     * @param string $screen Identificador da tela
     * @param array $tabs Lista das abas
     *
     */
    public static function add_tabs( $screen, $tabs )
    {
        if ( count( $tabs ) > 1 ) {
            foreach ( $tabs as $k => $tab ) {
                if ( isset( $tab[ 'fields' ] ) ) {
                    $f = new Form( add_query_arg( 'tab', $tab[ 'name' ] ) );
                    $f->add_fields( $tab[ 'fields' ] );
                    $tabs[ $k ][ 'form' ] = $f;
                }
            }

            self::$tabs[ $screen ] = $tabs;
        }
    }

    /**
     *
     * Processa e renderiza as informações referentes as abas adicionadas
     *
     * @param strig $screen Identificador da tela administrativa
     *
     */
    private static function tabs( $screen )
    {
        if ( isset( self::$tabs[ $screen ] ) ) {
            $tabs = self::$tabs[ $screen ];
            $tab_cur = ( isset( $_GET[ 'tab' ] ) ) ? $_GET[ 'tab' ] : false;
            if ( !$tab_cur ) {
                $tab_cur = reset( $tabs );
                $tab_cur = $tab_cur[ 'name' ];
            }

            echo '<h2 class="nav-tab-wrapper">';
            foreach ( self::$tabs[ $screen ] as $t ) {
                $tab_class = '';
                if ( $t[ 'name' ] == $tab_cur ) {
                    $tab = $t;
                    $tab_class = ' nav-tab-active';
                }
                printf(
                    '<a href="%s" class="nav-tab%s">%s</a>',
                    add_query_arg( 'tab', $t[ 'name' ] ),
                    $tab_class,
                    $t[ 'title' ]
                );
            }
            echo '</h2>';

            if ( isset( $tab[ 'callback' ] ) )
                call_user_func( $tab[ 'callback' ] );

            if ( isset( $tab[ 'fields' ] ) )
                self::render_form( $tab[ 'form' ], $screen );
        }
    }

    /**
     *
     * Renderiza os formulários das telas personalizadas
     *
     * @param object $form Objeto From do framework
     * @param string $screen Identificador da tela
     *
     */
    private static function render_form( $form, $screen )
    {
        if ( $form->has_fields ) {
            echo apply_filters( 'option_open_form', '<table class="form-table">' );

            // nonce and referer
            $form->add_fields(
                array(
                    array(
                        'type'  => 'hidden',
                        'name'  => 'option_nonce',
                        'value' => wp_create_nonce( 'option_screen' )
                    ),
                    array(
                        'type'  => 'hidden',
                        'name'  => 'option_referer',
                        'value' => $screen
                    )
                )
            );

            // insere botão de envio dos dados caso não exista
            $fields = $form->get_fields();
            $has_submit = false;
            foreach ( $fields as $f ) {
                if ( $f[ 'type' ] == 'submit' ) {
                    $has_submit = true;
                    break;
                }
            }
            if ( !$has_submit ) {
                $submit = apply_filters(
                    'option_submit',
                    array(
                        'name'  => 'update',
                        'type'  => 'submit',
                        'value' => __r( 'Save changes' ),
                        'class' => 'button-primary'
                    )
                );
                if ( is_array( $submit ) )
                    $form->add_field( $submit );
            }

            self::set_values( $form );
            $form->render();

            echo apply_filters( 'option_close_form', '</table>' );
        }
    }

}

// @fix add_screen

?>