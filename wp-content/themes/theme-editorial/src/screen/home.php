<?php

class Home extends Index
{

    protected function prepare_items( $type )
    {
        unset( $type );

        $data = get_posts(
            array(
                'posts_per_page' => 4
            )
        );
        return $data;
    }

}