<?php

class Index extends Screen
{

    public function process()
    {
        $title = ( is_search() ) ? 'Resultados de busca' : 'Arquivo';
        $this->title = $title;
    }

    protected function prepare_items( $type )
    {
        unset( $type );

        global $wp_query;
        $data = $wp_query->posts;
        return $data;
    }

    public function the_item_screen( $item, $type )
    {
        // unset( $type );
/*         if ( $type )
            return $item; */

        $item->permalink = get_permalink( $item );

        if ( has_post_thumbnail( $item ) )
            $thumb = get_the_post_thumbnail( $item, 'full');
        else
            $thumb = sprintf(
                '<img src="%simages/pic01.jpg" alt="%s" />',
                THEME_URL, esc_attr( $item->post_title )
            );

        $item->thumb = $thumb;

        $excerpt = $item->post_excerpt;
        if ( !$excerpt ) {
            $excerpt = wp_trim_words(
                strip_shortcodes( strip_tags( $item->post_content ) ),
                30, '...'
            );
        }
        $item->excerpt = $excerpt;

        return $item;
    }

}
