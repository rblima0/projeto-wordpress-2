<?php

define( 'PW_URL',           get_home_url() . '/' );
define( 'PW_URL_THEME',     get_bloginfo( 'template_url' ) . '/' );
define( 'PW_SITE_NAME',     get_bloginfo( 'title' ) );

add_action( 'after_setup_theme', 'pw_theme_setup' );

function pw_theme_setup()
{
    register_nav_menu( 'menu-header', 'Menu do Cabeçalho' );

    add_action( 'wp_enqueue_scripts', 'pw_register_scripts_and_styles' );
    add_action( 'wp_enqueue_scripts', 'pw_enqueue_scripts_and_styles' );

    add_filter( 'show_admin_bar', '__return_false' );
    add_filter( 'wp_title', 'pw_title', 10, 2 );

    add_action( 'personal_options',                 'pw_user_edit' );
    add_action( 'personal_options_update',          'pw_user_save' );
    add_action( 'edit_user_profile_update',         'pw_user_save' );

    global $wp_roles;
    if ( !isset( $wp_roles->roles[ 'reader' ] ) ) {
        $caps = array(
            'read' => true,
            'read_private_posts' => true,
            'read_private_pages' => true
        );
        add_role( 'reader', 'Leitor', $caps );
    }
}


function pw_register_scripts_and_styles()
{
    wp_register_style( 'leading', PW_URL_THEME . 'style.css', false, '', 'screen' );
    wp_register_style( 'leading-font1', 'http://fonts.googleapis.com/css?family=Roboto+Condensed|Open+Sans:400,300,700|Yesteryear', false, '', 'screen' );
}

function pw_enqueue_scripts_and_styles()
{
    wp_enqueue_style( 'leading' );
    wp_enqueue_style( 'leading-font1' );

    wp_enqueue_script( 'comment-reply' );
}

function pw_title( $title, $sep )
{
    if ( $title && !strpos( $title, $sep ) )
        $title .= $sep;

    $title .= PW_SITE_NAME;

    return $title;
}

function pw_paginate_links()
{
    global $wp_query;
    $page_tot = $wp_query->max_num_pages;
    $page_cur = $wp_query->get( 'paged' );

    if ( $page_tot <= 1 )
        return false;

    if ( !$page_cur )
        $page_cur = 1;

    echo '<nav class="pagination">Página ' . $page_cur . ' de ' . $page_tot . ': ' .
    paginate_links( array(
            'base'      => str_replace( $page_tot, '%#%', get_pagenum_link( $page_tot ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, $page_cur ),
            'total'     => $page_tot,
            'prev_next' => false,
            'end_size'  => 1,
            'mid_size'  => 2
        )
    ) . '</nav>';
}

function pw_user_edit( $user )
{
    if ( current_user_can( 'reader' ) ) { ?>
    <table class="form-table">
        <tr>
            <th>Empresa</th>
            <td><input type="text" name="pw_company" value="<?php echo get_user_meta( $user->ID, 'pw_company', true ); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php
    }
}

function pw_user_save( $user_id )
{
    if ( isset( $_POST[ 'pw_company' ] ) && current_user_can( 'edit_user', $user_id ) )
        update_user_meta( $user_id, 'pw_company', $_POST[ 'pw_company' ] );
}

function pw_get_template( $template )
{
    global $wpdb;
    $page_id = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT p.ID FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID=pm.post_id
            WHERE pm.meta_key='_wp_page_template'
            AND pm.meta_value=%s
            AND p.post_type='page'
            ORDER BY p.post_date DESC
            LIMIT 1",
            $template
        )
    );
    return get_page( $page_id );
}

function get_templates( $args )
{
    $defaults = array(
        'posts_per_page'    => 1,
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
        'post_status'       => 'private',
        'template'          => ''
    );
    $args = wp_parse_args( $args, $defaults );

    $pages = get_pages( array(
            'number'        => $args[ 'posts_per_page' ],
            'sort_column'   => $args[ 'orderby' ],
            'sort_order'    => $args[ 'order' ],
            'post_status'   => $args[ 'status' ],
            'meta_key'      => '_wp_page_template',
            'meta_value'    => $args[ 'template' ]
        )
    );
    return $pages;
}

?>