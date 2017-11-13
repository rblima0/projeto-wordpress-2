<?php

class Sidebar extends Screen
{
    protected function prepare_items( $type )
    {
        $data = array();
        switch ( $type ) {
            case 'posts':
                $data = get_posts(
                    array(
                        'posts_per_page' => 2
                    )
                );
                break;
            case 'pages':
                $data = get_posts(
                    array(
                        'posts_per_page' => 3,
                        'post_type'      => 'page'
                    )
                );
                break;
            case 'info':
                $data = array(
                    (object) array(
                        'icon'      => 'fa-envelope-o',
                        'content'   => '<a href="#">contato@contato.com.br</a>'
                    ),
                    (object) array(
                        'icon'      => 'fa-phone',
                        'content'   => '(00) 0 0000-0000'
                    ),
                    (object) array(
                        'icon'      => 'fa-home',
                        'content'   => 'Endereço, Numero<br>Bairro, Cidade 00000-000'
                    )
                );
                break;
        }
        return $data;
    }

    public function the_item_screen( $item, $type )
    {
        switch ( $type ) {
            case 'posts':
                if ( has_post_thumbnail( $item ) )
                    $thumb = get_the_post_thumbnail( $item, 'thumbnail' );
                else
                    $thumb = sprintf(
                        '<img src="%simages/pic01.jpg" alt="%s" />',
                        THEME_URL, esc_attr( $item->post_title )
                    );

                $item->thumb = $thumb;
                break;
            case 'pages':
                $item->subtitle = get_post_meta( $item->ID, 'subtitle', true );
                break;
        }

        if ( $type !== 'info' )
            $item->permalink = get_permalink( $item );

        return $item;
    }

}