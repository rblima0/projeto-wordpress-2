<?php

/**
 *
 * Manipulação dos shortcodes de modo seguro
 *
 */
class Code
{

    /**
     *
     * @var array $codes Armazena as tags dos shortcodes criados
     *
     */
    private static $codes;

    /**
     *
     * Filtra os atributos passados ao shortcode
     *
     * @param array $attr Os atributos
     * @param string $content Conteúdo delimitado pelas tags
     * @param string $tag Shortcode
     * @return string Resultado do shortcode de acordo com a função callback
     *
     *
     */
    private static function controller( $attr, $content, $tag )
    {
        $code = ( isset( self::$codes[ $tag ] ) ) ? self::$codes[ $tag ] : false;
        if ( $code ) {
            $response = true;
            if ( isset( $code[ 'attr' ] ) ) {
                $attr_default = array();
                foreach ( $code[ 'attr' ] as $a )
                    $attr_default[ $a[ 'name' ] ] = ( isset( $a[ 'std' ] ) ) ? $a[ 'std' ] : null;

                $attr = shortcode_atts( $attr_default, $attr );

                foreach ( $code[ 'attr' ] as $a ) {
                    if ( isset( $a[ 'req' ] ) && !$attr[ $a[ 'name' ] ] ) {
                        $response = false;
                        break;
                    }
                }
            }

            if ( $response )
                return call_user_func( $code[ 'cb' ], $attr, $content );
        }
    }

    /**
     *
     * Adiciona um novo shortcode
     *
     * @param string $tag Shortcode
     * @param function $callback Função de retorno
     * @param array $attr Quais os atributos aceitos e suas características
     *
     */
    public static function add( $tag, $callback, $attr=null )
    {
        self::$codes[ $tag ] = array(
            'cb'    => $callback,
            'attr'  => $attr
        );
        add_shortcode( $tag, array( 'Code', 'controller' ) );
    }

    /**
     *
     * [gallery]
     *
     */
    public static function gallery( $attr )
    {
        $attr[ 'include' ] = $attr[ 'ids' ];
        unset( $attr[ 'ids' ] );

        $html = '';
        $images = Theme::get_images( $attr );
        if ( is_array( $images ) && count( $images ) > 0 )
            $html = sprintf(
                apply_filters( 'gallery_container', '<ul class="gallery">%s</ul>' ),
                Theme::list_images( $images )
            );

        return $html;
    }

}

?>