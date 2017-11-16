<?php

if( !class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class KDM_Products_List_Table extends WP_List_Table
{

    function __construct()
    {
        parent::__construct(
            array(
                'singular'  => 'Produto',
                'plural'    => 'Produtos'
            )
        );
    }
    
    function no_items()
    {
        echo 'Nenhum produto cadastrado...';
    }
    
    // Colunas
    
    function get_columns()
    {
        $cols = array(
            'cb'    => '<input type="checkbox" />',
            'title' => 'Produto',
            'price' => 'Preço'
        );
        return $cols;
    }
    
    // Ordenação
    
    function get_sortable_columns()
    {
        $cols = array(
            'title' => array( 'title', false ),
            'price' => array( 'price', true )
        );
        return $cols;
    }
    
    // Conteúdo

    function column_default( $item, $col_name )
    {
        return $item[ $col_name ];
    }
    
    /*
    function column_price( $item )
    {
        return 'R$ ' . number_format( $item[ 'price' ], 2, ',', '' );
    } */

    function column_cb( $item )
    {
        return sprintf(
            '<input type="checkbox" name="product_id[]" value="%s" />',
            $item[ 'id' ]
        );
    }
    
    function column_title( $item )
    {
        $actions = array(
            'edit' => sprintf(
                '<a href="?page=%s&action=%s&product_id=%d">Editar</a>',
                'cp-products',
                'edit',
                $item[ 'id' ]
            ),
            'delete' => sprintf(
                '<a href="?page=%s&action=%s&product_id=%d">Excluir</a>',
                'cp-products',
                'delete',
                $item[ 'id' ]
            )
        );
        return sprintf( '%1$s %2$s', $item[ 'title' ], $this->row_actions( $actions ) );
    } 
    
    // Ações em massa

    function get_bulk_actions()
    {
        return array(
            'delete' => 'Excluir'
        );
    }

    function process_bulk_action()
    {
        $action = $this->current_action();
        switch ( $action )
        {
            case 'delete':
                $ids = false;
                if ( isset( $_POST[ 'product_id' ] ) )
                    $ids = implode( ',', $_POST[ 'product_id' ] );
                else if ( isset( $_GET[ 'product_id' ] ) )
                    $ids = $_GET[ 'product_id' ];

                if ( $ids ) {
                    global $wpdb;
                    /*
                    $wpdb->delete( 
                        $wpdb->products,
                        array( 'id' => 1 ),
                        array( '%d' )
                    ); */
                    $wpdb->query( "DELETE FROM {$wpdb->products} WHERE id IN ({$ids})" );
                }
                break;
        }
    }
    
    // Consulta SQL

    function prepare_items()
    {
        global $wpdb;
        $columns  = $this->get_columns();
        $hidden   = get_hidden_columns( get_current_screen() );
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $this->process_bulk_action();

        // conteúdo do banco de dados
        $query = "SELECT * FROM {$wpdb->products}";

        if ( isset( $_POST[ 's' ] ) ) {
            $q = sanitize_text_field( $_POST[ 's' ] );
            $query .= ' WHERE title LIKE "%'.$q.'%"';
        }

        $orderby = !empty( $_GET[ 'orderby' ] ) ? esc_attr( $_GET[ 'orderby' ] ) : 'title';       
        $order = !empty( $_GET[ 'order' ] ) ? sanitize_sql_orderby( $_GET[ 'order' ] ) : 'ASC';
	
        $query.= " ORDER BY {$orderby} {$order}";

        $items_total = $wpdb->query( $query );
        $items_per_page = $this->get_items_per_page( 'products_per_page' );
        
        $paged = !empty( $_GET[ 'paged' ] ) ? (int) $_GET[ 'paged' ] : 1;
        if( !$paged )
            $paged = 1;

        $pages = ceil( $items_total/$items_per_page );
        $offset = ( $paged-1 ) * $items_per_page;
        $query .= sprintf(
            ' LIMIT %d, %d',
            (int) $offset,
            (int) $items_per_page
        );

        $this->set_pagination_args(
            array(
                'total_pages'   => $pages,
                'total_items'   => $items_total,
                'per_page'      => $items_per_page,
            )
        );

        $this->items = $wpdb->get_results( $query, ARRAY_A );
    }
    
    // Exibição dos resultados
    
    function render()
    {
        global $products;
        $action = $products->current_action();
        if ( in_array( $action, array( 'insert', 'edit' ) ) ) {
            $products->form();
        } else {
            if ( $action == 'delete' ) {
                $msg = 'Registros excluídos';
                if ( isset( $_GET[ 'product' ] ) ) $msg = str_replace( 'os', 'o', $msg );
                echo '<div class="updated"><p>'.$msg.' com sucesso!</p></div>';
            }

            global $products;
            echo '<div class="wrap">'
                . '<h2>Lista de Produtos '
                . '<a href="admin.php?page=cp-products&action=insert" class="add-new-h2">Adicionar Novo</a>'
                . '</h2>';

            $search = ( isset( $_POST[ 's' ] ) ) ? sanitize_text_field( $_POST[ 's' ] ) : false;
            if ( $search ) {
                printf(
                    '<p><span>Resultados da pesquisa por "%s"</span> <a href="%s">Limpar Busca</a></p>',
                    $search,
                    $_SERVER[ 'REQUEST_URI' ]
                );
            }

            $products->prepare_items();
            echo '<form method="post">';
            $products->search_box( 'Pesquisar', 'custom-search' );
            $products->display();
            echo '</form></div>';
        }
    }
    
    function form()
    {
        global $wpdb;

        if ( !isset( $_GET[ 'product_id' ] ) ) {
            $id = 0;
            $action = 'insert';
            $action_label = 'Adicionar';
        } else {
            $id = (int) $_GET[ 'product_id' ];
            $action = "edit&product_id={$id}";
            $action_label = 'Alterar';
        }
        
        if ( !empty( $_POST ) && isset( $_POST[ '_cp_nonce' ] ) ) {
            $title = sanitize_text_field( $_POST[ '_title' ] );
            $price = (float) sanitize_text_field( str_replace( ',', '.', $_POST[ '_price' ] ) );
            if ( !wp_verify_nonce( $_POST[ '_cp_nonce' ], 'cp-form' ) ) {
                $error = 'Não foi possível processar sua requisição...';
            } else if ( !$title || !$price ) {
                $error = 'Preencha todos os campos!';
            } else {
                if ( !$id ) {
                    $wpdb->insert(
                        $wpdb->products,
                        array(
                            'title' => $title,
                            'price' => $price
                        ),
                        array(
                            '%s',
                            '%f'
                        )
                    );
                    $msg = 'Produto inserido com sucesso!';
                } else {
                    $wpdb->update(
                        $wpdb->products,
                        array(
                            'title' => $title,
                            'price' => $price
                        ),
                        array(
                            'id' => $id
                        ), 
                        array(
                            '%s',
                            '%f'
                        ),
                        array(
                            '%d'
                        )
                    );
                    $msg = 'Produto alterado com sucesso!';
                }
            }
            
            if ( !isset( $error ) ) {
                $msg_class = 'updated';
            } else {
                $msg = $error;
                $msg_class = 'error';
            }
        }
        
        $v = array();
        if ( $id ) {
            $v = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT title, price FROM {$wpdb->products} WHERE id=%d",
                    $id
                ), ARRAY_A
            );
        }
        
        if ( !isset( $v[ 'title' ] ) || !isset( $v[ 'price' ] ) ) {
            $v = array(
                'title' => '',
                'price' => ''
            );
        } ?>
        <div class="wrap">
            <h2>
                Produtos - <?php echo $action_label; ?>
                <a href="admin.php?page=cp-products" class="add-new-h2">Voltar</a>
            </h2>
            
            <?php
            if ( isset( $msg ) && isset( $msg_class ) ) {
                printf(
                    '<div class="%s"><p>%s</p></div>',
                    $msg_class,
                    $msg
                );
            }
            ?>
            
            <form action="admin.php?page=cp-products&action=<?php echo $action; ?>" method="post">
                <?php wp_nonce_field( 'cp-form', '_cp_nonce' ); ?>
                <table class="form-table">
                    <tr>
                        <th>Produto</th>
                        <td><input type="text" value="<?php echo $v[ 'title' ]; ?>" name="_title" size="30" /></td>
                    </tr>
                    <tr>
                        <th>Preço</th>
                        <td><input type="text" value="<?php echo $v[ 'price' ]; ?>" name="_price" size="10" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><?php submit_button(); ?></td>
                    </tr>
                </table>
            </form>
        </div>
        <?php
    }

    // Página administrativa
    
    function admin_menu()
    {
        $hook = add_menu_page( 'Produtos', 'Produtos', 'administrator', 'cp-products', array( 'KDM_Products_List_table', 'render' ) );
        add_action( "load-$hook", array( 'KDM_Products_List_table', 'add_options' ) );
        
        add_submenu_page( 'cp-products', 'Adicionar novo', 'Adicionar novo', 'administrator', 'cp-products-form', array( 'KDM_Products_List_table', 'form' ) );
    }

    function add_options()
    {
        global $products;
        $products = new KDM_Products_List_table();
        
        $option = 'per_page';
        $args = array(
            'label'     => 'Produtos',
            'option'    => 'products_per_page',
            'default'   => 10
        );
        add_screen_option( $option, $args );
    }

    function set_option( $status, $option, $value )
    {
        if ( $option == 'products_per_page' )
            $status = $value;
        
        return $status;
    }

}