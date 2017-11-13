<?php

class Rules
{

    public static function init()
    {
        add_action( 'template_redirect', array( 'Rules', 'template_redirect' ), 1 );
    }

    public static function template_redirect()
    {
        global $wp_query;

        $screen = 'index';
        if ( $wp_query->is_page ){
            $screen = 'page';
        } else if ( $wp_query->is_single && $wp_query->post->post_type ) {
            if ( $wp_query->post->post_type == 'post' ) {
                $screen = 'single';
            } else {
                $screen = 'single-' . $wp_query->post->post_type;
            }
        } else if ( $wp_query->is_author ) {
            $screen = 'author';
        } else if ( $wp_query->is_post_type_archive ) {
            $type = get_query_var( 'post_type' );
            if ( $type && $type !== 'post' ){
                $screen = 'archive-' . $type;
            } else {
                $screen = 'archive';
            }
        } else if ( $wp_query->is_tax ) {
            $tax = get_query_var( 'taxonomy' );
            if ( $tax ) {
                $screen = 'taxonomy-' . $tax;
            }
        } else if ( $wp_query->is_home ) {
            $screen = 'home';
        } else if ( $wp_query->is_search ) {
            $screen = 'search';
        } else if ( is_404() ) {
            $screen = 'error-404';
        }

        self::include_screen( $screen );
    }

    public static function include_screen( $path )
    {
        global $screen;

        $file_screen = SRC_PATH . 'screen/' . $path . '.php';
        if ( !file_exists( $file_screen ) )
            $path = 'index';

        // require feito no autoload
        $class = str_replace( '-', '_', $path );
        $screen = new $class();

        if ( method_exists( $screen, 'process' ) )
            $screen->process();

        if ( method_exists( $screen, 'the_item_screen' ) )
            add_filter( 'the_item_screen', array( $screen, 'the_item_screen' ), 1, 2 );
    }

}

