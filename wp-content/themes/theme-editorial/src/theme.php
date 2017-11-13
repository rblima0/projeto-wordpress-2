<?php
//Call back para retornar arquivos post na busca.
class Theme
{

    public static function pre_get_posts( $q )
    {
        if ( $q->is_search() )
            $q->set( 'post_type', 'post' );

        return $q;
    }

}