<?php

/**
 *
 * Meta valores para usuários
 *
 */
class User extends Dashboard
{

    /**
     *
     * @var array Lista de objetos Formulário do framework
     *
     */
    private static $forms;

    /**
     *
     * @var array Relação de campos de acordo com a permissão do usuário
     *
     */
    private static $role_fields;

    /**
     *
     * Inicializador da classe, dispara os gatilhos necessários
     *
     * @param string $place Define o local a exibir os novos campos
     *
     */
    private static function init()
    {
        if ( !is_array( self::$forms ) ) {
            self::$forms = array();
            self::$role_fields = array();

            add_filter( 'form_field', array( 'User', 'fields_format' ), 2, 4 );

            // New user
            add_action( 'user_new_form', array( 'User', 'edit' ) );
            add_action( 'user_register', array( 'User', 'save' ) );

            add_action( 'show_user_profile',        array( 'User', 'edit' ) );
            add_action( 'edit_user_profile',        array( 'User', 'edit' ) );

            add_action( 'personal_options_update',  array( 'User', 'save' ) );
            add_action( 'edit_user_profile_update', array( 'User', 'save' ) );
        }
    }

    /**
     *
     * Insere campos ao formulário de acordo com a permissão de acesso do usuário
     *
     * @param null|string|array $roles Permissão de acesso
     * @param array $fields Lista de campos
     *
     */
    public static function add_fields( $roles, $fields )
    {
        self::init();
        $roles = ( is_null( $roles ) ) ? array( 'all' ) : (array) $roles;
        foreach ( $roles as $role ) {
            self::$forms[ $role ] = new Form();
            self::$forms[ $role ]->add_fields( $fields, apply_filters( 'root_user_prefix', '' ) );
            self::$role_fields[ $role ] = self::$forms[ $role ]->get_fields_names();
        }
    }

    /**
     *
     * Renderiza o formulário para edição
     *
     * @param string|object $user Texto de novo usuário ou object User do WordPress
     *
     */
    public static function edit( $user )
    {
        if ( ( is_object( $user ) && self::check_roles( $user->roles ) ) || is_string( $user ) ) {
            do_action( 'user_edit', $user );

            echo apply_filters( 'user_open_form', '<table class="form-table">' );

            $roles = array( 'all' );
            if ( isset( $user->ID ) ) {
                self::set_values( $user->ID );
                $roles = array_merge( $roles, $user->roles );
            }

            foreach ( $roles as $r ) {
                if ( isset( self::$forms[ $r ] ) )
                    self::$forms[ $r ]->render();
            }

            echo apply_filters( 'user_close_form', '</table>' );
        }
    }


    /**
     *
     * Verifica se deve ou não exibir o formulário de acordo com a permissão que este possui
     *
     * @param array $roles Permissões para as quais o formulário deve ser renderizado
     * @return boolean Insere ou não os novos campos
     *
     */
    private static function check_roles( $user_roles )
    {
        $show = false;
        $roles = array_keys( self::$role_fields );
        if ( in_array( 'all', $roles ) ) {
            $show = true;
        } else {
            foreach( $roles as $r ) {
                if ( in_array( $r, $user_roles ) ) {
                    $show = true;
                    break;
                }
            }
        }
        return $show;
    }

    /**
     *
     * Formata a marcação do formulário de acordo com o padrão do Dashboard
     *
     * @return string HTML Formatado
     *
     */
    public static function fields_format( $html, $html_field, $f, $has_html )
    {
        unset( $html );
        if ( $f[ 'type' ] == 'sep' ) {
            $format = ( $has_html ) ? '</table>' : '';
            $format .= '<h3 id="%s">%s</h3><table class="form-table">';
            return apply_filters( 'user_fields_sep_format', $format, $html_field, $f );
        } else {
            return apply_filters( 'user_fields_format', '<tr><th><label for="%s">%s</label></th><td>%s</td></tr>' );
        }
    }

    /**
     *
     * Verifica a permissão e salva os meta dados no banco
     *
     * @param integer $user_id Identificador do usuário no WordPress
     * @return integer ID do usuário
     *
     */
    public static function save( $user_id )
    {
        if ( current_user_can( 'edit_user', $user_id ) ) {
            $args = array(
                'object_type'   => 'user', // post, comment, user
                'object_id'     => $user_id,
                'fields'        => self::get_fields( $user_id )
            );
            do_action( 'user_save', $args[ 'object_id' ] );
            self::meta_save( $args );
        }
        return $user_id;
    }

    /**
     *
     * Define os valores dos campos caso eles existam na base de dados
     *
     * @param integer $user_id Identificador do usuário no WordPress
     *
     */
    private static function set_values( $user_id )
    {
        if ( is_int( $user_id ) ) {
            $args = array(
                'object_type'   => 'user',
                'object_id'     => $user_id,
                'fields'        => self::get_fields( $user_id )
            );
            $values = self::meta_values( $args );
            foreach( $values as $f => $v ) {
                foreach ( self::$role_fields as $role => $fields ) {
                    if ( in_array( $f, $fields ) )
                        self::$forms[ $role ]->set_field_value( $f, $v );
                }
            }
        }
    }

    /**
     *
     * Retorna o conjunto de todos os campos personalizados
     *
     * @param int $user_id ID do usuário do WordPress
     * @return array Lista de campos
     *
     */
    private static function get_fields( $user_id )
    {
        $fields = array();
        $u = get_userdata( $user_id );
        array_push( $u->roles, 'all' );
        foreach ( $u->roles as $r ) {
            if ( isset( self::$forms[ $r ] ) )
                $fields = array_merge( $fields, self::$forms[ $r ]->get_fields() );
        }
        return $fields;
    }

    /**
     *
     * Adiciona novas colunas a lista de resultados
     *
     * @param array $cols Lista com as novas colunas
     *
     */
    public static function add_cols( $cols )
    {
        if ( !isset( self::$cols ) )
            self::$cols = array();

        self::$cols[ 'user' ] = $cols;

        add_filter( 'manage_users_columns',         array( 'User', 'cols' ) );
        add_filter( 'manage_users_custom_column',   array( 'User', 'cols_content' ), 10, 3 );

        if ( self::has_cols_order( $cols ) ) {
            add_filter( 'manage_users_sortable_columns',    array( 'User', 'cols_order' ) );
            add_filter( 'pre_user_query',                   array( 'User', 'cols_request' ) );
        }
    }

    /**
     *
     * Insere novas colunas a listagem de resultados
     *
     * @param array $cols Colunas atuais em exibição
     * @return array Colunas personalizadas
     *
     */
    public static function cols( $cols )
    {
        if ( isset( self::$cols[ 'user' ] ) ) {
            $custom = array();
            foreach( self::$cols[ 'user' ] as $c )
                $custom[ $c[ 'id' ] ] = $c[ 'label' ];

            $cols = array_merge( $cols, $custom );
        }

        return $cols;
    }

    /**
     *
     * Define os conteúdos para as colunas personalizdas
     *
     * @param string deprecated $value ''
     * @param string $col Coluna para a qual o conteúdo será inserido
     * @param integer $user_id ID do usuário no WordPress
     * @return string Informação a ser exibida nos resultados
     *
     */
    public static function cols_content( $value, $col, $user_id )
    {
        unset( $value ); // deprecated
        $user = get_userdata( $user_id );
        switch ( $col )
        {
            case 'url':
                return $user->user_url;
                break;
            case 'description':
                return $user->user_description;
                break;
            default:
                $meta = get_user_meta( $user_id, $col, true );
                return apply_filters( 'user_cols_content', $meta, $col, $user );
                break;
        }
    }

    /**
     *
     * Define colunas passíveis de ordenação
     *
     * @param array $cols Lista com as colunas atuais
     * @return array Lista com as novas colunas adicionadas
     *
     */
    public static function cols_order( $cols )
    {
        return array_merge( $cols, self::get_cols_order() );
    }

    /**
     *
     * Verifica a requisição no Dashboard e personaliza a exibição dos resultados das colunas personalizadas
     *
     * @global object $wpdb Classe de banco de dados do WordPress http://codex.wordpress.org/Class_Reference/wpdb
     * @param object $wp_user Classe para consulta de usuários do WordPress http://codex.wordpress.org/Class_Reference/WP_User_Query
     *
     */
    public static function cols_request( $wp_user )
    {
        if ( is_admin() ) {
            $s = get_current_screen();
            if ( $s->base == 'users' ) {
                $vars = $wp_user->query_vars;
                $cols = array_values( self::get_cols_order() );
                if (
                    isset( $vars[ 'orderby' ] ) &&
                    in_array( $vars[ 'orderby' ], $cols )
                ) {
                    $meta_key = $vars[ 'orderby' ];

                    global $wpdb;
                    $order = strtoupper( $_REQUEST[ 'order' ] );
                    if ( !in_array( $order, array( 'ASC', 'DESC' ) ) )
                        $order = 'ASC';

                    $wp_user->query_vars[ 'orderby' ] = 'meta_value';
                    $wp_user->query_fields = "SQL_CALC_FOUND_ROWS u.ID, replace( um.meta_value, ',', '.' ) AS mv";
                    $wp_user->query_from = "FROM {$wpdb->users} u LEFT JOIN {$wpdb->usermeta} um ON u.ID=um.user_id";
                    $wp_user->query_where = sprintf(
                        "WHERE 1=1 AND um.meta_key='%s'",
                        $meta_key
                    );

                    $type = self::get_order_type( $meta_key );
                    $cast = ( $type == 'numeric' ) ? "mv+0" : 'mv';
                    $wp_user->query_orderby = sprintf(
                        "ORDER BY {$cast} {$order}",
                        $meta_key
                    );
                }
            }
        }
    }

}

?>
