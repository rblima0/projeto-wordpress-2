<?php

class Screen
{

    private $items, $type, $args, $item;

    public function __get( $key )
    {
        $value = false;
        if ( isset( $this->args[ $key ] ) )
            $value = $this->args[ $key ];

        return $value;
    }

    public function __set( $key, $value )
    {
        $this->args[ $key ] = $value;
    }

    protected function prepare_items( $type )
    {
        unset( $type );
        return array();
    }

    public function have_items( $type=false )
    {
        if ( !$type ) $type = 'default';

        $this->items[ $type ] = $this->prepare_items( $type );
        if ( $this->type && ( $type !== $this->type ) )
            next( $this->items );

        $this->type = $type;
        $this->item = null;
        $this->count= count( $this->items[ $type ] );

        return ( is_array( $this->items[ $type ] ) && !empty( $this->items[ $type ] ) );
    }

    public function the_item()
    {
        if ( !isset( $this->items[ $this->type ] ) )
            return false;

        if ( !isset( $this->item ) || is_null( $this->item ) )
            $i = current( $this->items[ $this->type ] );
        else
            $i = next( $this->items[ $this->type ] );

        if ( $i ) $i = apply_filters( 'the_item_screen', $i, $this->type );
        $this->item = $i;

        global $item;
        $item = $this->item;

        $key = key( $this->items[ $this->type ] );
        $have_item = ( is_int( $key ) || is_string( $key ) );
        if ( !$have_item ) {
            prev( $this->items );
            $this->type = key( $this->items );
            if ( $this->type === null )
                $this->type =  $this->items = null;
        }

        return $have_item;
    }

}