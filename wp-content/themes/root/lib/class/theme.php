<?php

/**
 *
 * Template tags personalizadas
 *
 */
class Theme
{

    private static $path;

    // {{{ Imagens

    /**
     *
     * Recupera a imagem de capa do post atual ou do parâmetro
     * Caso não exista, uma imagem padrão é exibida com as devidas dimensões
     *
     * @global object $post
     * @param string $size Nome do formato da imagem
     * @param integer $post_id ID do post para se recuperar a imagem destacada
     * @param string $alt Texto alternativo para imagem. Por padrão é o título do post.
     * @return string Tag HTML para inserção da imagem no site
     *
     */
    public static function get_thumb( $post_id=null, $size='thumbnail' )
    {
        $post_id = (int) $post_id;
        if ( !$post_id ) {
            global $post;
            $post_id = $post->ID;
        }

        $thumb = false;
        $thumb_id = (int) get_post_thumbnail_id( $post_id );
        if ( $thumb_id ) {
            $image = get_post( $thumb_id );
            list( $src, $alt ) = self::get_image_data( $image, $size );
            // if ( $src && $alt )
            // condição desnecessária pois já fora verificada a existência da imagem
            // logo o get_posts sempre retornará válido
            $thumb = '<img src="' . $src . '" alt="' . esc_attr( $alt ) . '" />';
        } else {
            global $_wp_additional_image_sizes;
            if ( isset( $_wp_additional_image_sizes[ $size ] ) )
                extract( $_wp_additional_image_sizes[ $size ] );

            // @fix Acertar comportamento se o formato for thumbnail, medium ou large
            if ( !isset( $width ) || !isset( $height ) ) {
                $width = 150;
                $height = 150;
            }

            $alt = __r( 'Image not available' );
            $img = sprintf( 'http://dummyimage.com/%sx%s/444/fff&text=%s', $width, $height, urlencode( $alt ) );
            $thumb = sprintf( '<img src="%s" alt="%s" />', $img, $alt );
            $thumb = apply_filters( 'default_image', $thumb, $img, $alt );
        }

        return $thumb;
    }


    /**
     *
     * Retorna o texto alternativo e a url da imagem de acordo com o formato solicitado
     *
     * @param object $image Post do tipo imagem
     * @param string|array $size Tamanho da imagem a ser recuperada
     * @return array URL e texto alternativo em array numérico
     *
     */
    private static function get_image_data( $image, $size )
    {
        if ( isset( $image->ID ) && isset( $image->post_title ) ) {
            $alt = get_post_meta( $image->ID, '_wp_attachment_image_alt', true );
            if ( !$alt ) $alt = $image->post_title;
            list( $src ) = wp_get_attachment_image_src( $image->ID, $size );
            return array( $src, $alt );
        }
        return false;
    }

    /**
     *
     * Recupera as imagens de um dado post
     *
     * @param array $args Argumentos para definir os posts a serem recuperados
     * @return array $posts As imagens do post
     *
     */
    public static function get_images( $args=array() )
    {
        $defaults = array(
            'post_type'         => 'attachment',
            'post_mime_type'    => 'image',
            'post_status'       => 'any',
            'numberposts'       => -1
        );
        $args = wp_parse_args( $args, $defaults );
        return get_posts( $args );
    }

    /**
     *
     * Exibe as imagens recuperadas com get_posts
     * O formato de retorno das imagens é dentro de tags de itens e links HTML
     * Estrutura: <li><a href="" title=""><img src="" alt="" /></a></li>
     *
     * @param array $images Posts de imagens
     * $param string|array Nome ou dimensões do tamanho de imagem a ser recuperado
     * @return string Marcação HTML para exibição das imagens em tela
     *
     */
    public static function list_images( $images, $size='gallery', $rel=null )
    {
        if ( !is_array( $images ) || !count( $images ) )
            return false;

        $html = '';
        foreach ( $images as $image ) {
            if ( isset( $image->ID ) && isset( $image->post_title ) ) {
                list( $src, $alt ) = self::get_image_data( $image, $size );
                $html .= sprintf(
                    '<li><a%s href="%s" title="%s"><img alt="%s" src="%s" /></a></li>',
                    ( is_string( $rel ) ) ? ' rel="' . $rel . '"' : '',
                    wp_get_attachment_url( $image->ID ),
                    $image->post_title,
                    $alt,
                    $src
                );
            }
        }
        return $html;
    }

    // }}}


    // {{{ Page Template

    /**
     *
     * Recupera nome limpo do arquivo de template
     * Usar template pages com o nome pg-{nome}.php
     *
     * @return string Nome do modelo de página
     *
     */
    public static function get_template()
    {
        if ( !is_page() )
            return false;

        return str_replace(
            array( '/' . 'pg-', '.php', TEMPLATEPATH ),
            '',
            get_page_template()
        );
    }

    /**
     *
     * Obtém o link da página com o modelo atribuído
     *
     * @global object $wpdb
     * @param string $template Nome do arquivo do modelo sem prefixo ou terminação
     * @return string Endereço âncora da referida página ou referência para o topo da página atual
     *
     */

    public static function get_template_permalink( $template='' )
    {
        $url = '#';
        if ( $template ) {
            global $wpdb;
            $page_id = (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT post_id FROM {$wpdb->postmeta} " .
                    "WHERE meta_key='_wp_page_template' AND meta_value=%s " .
                    "ORDER BY meta_id DESC LIMIT 1",
                    $template
                )
            );
            if ( $page_id )
                $url = get_permalink( $page_id );
        }
        return $url;
    }

    /**
     *
     * Recupera o objeto do Post com base no modelo passado como parâmetro
     *
     * @param string $template Nome do arquivo do modelo sem prefixo ou terminação
     * @return object|boolean $post Informações da página ou falso se nenhuma página encontrada
     *
     */
    public static function get_template_page( $template, $slug=null )
    {
        $pages = get_posts(
            array(
                'post_type'		=> 'page',
                'posts_per_page'=> -1,
                'meta_key'		=> '_wp_page_template',
                'meta_value'	=> $template
            )
        );

        $results = count( $pages );
        if ( is_array( $pages ) && $results ) {
            if ( ( $results > 1 ) && $slug )
                return get_page_by_path( $slug );
            else
                return array_shift( $pages );
        }
        return false;
    }

    /**
     *
     * Recupera nome do arquivo de template pelo ID da página
     *
     * @return int ID da página
     *
     */
    public static function get_template_by_id( $id )
    {
        $id = (int) $id;
        return get_post_meta( $id, '_wp_page_template', true );
    }

    // }}}

    // {{{ Navegação

    /**
     *
     * Paginação de resultados de modo personalizado.
     * Se não houver páginas adicionais, nada é exibido.
     *
     * $args = array(
     *  'wp_query'             => $wp_query
     *  'first_and_last'       => boolean
     *  'first_and_last_class' => string
     *  'first_label'          => string
     *  'last_label'           => string
     *  'container'            => 'nav'
     *  'stats'                => 'Page 1 of 3'
     * );
     *
     * @param array $args Definições da paginação de resultados
     *
     */
    public static function paginate( $args=array() )
    {
        global $wp_query;
        $defaults = array(
            'wp_query'              => $wp_query,
            'first_and_last'        => false,
            'first_and_last_class'  => '',
            'first_label'           => __r( 'First' ),
            'last_label'            => __r( 'Last' ),
            'container'             => 'nav',
            'container_class'       => 'pagination',
            'stats'                 => __r( 'Page %d of %d:' )
        );
        $args = wp_parse_args( $args, $defaults );

        $page_tot = $args[ 'wp_query' ]->max_num_pages;
        $page_cur = get_query_var( 'paged' );
        $posts = (int) $args[ 'wp_query' ]->found_posts;

        if ( ( $page_tot == 1 ) || !$posts )
            return false;

        if ( !$page_cur )
            $page_cur = 1;

        $html  = sprintf( '<%s class="%s">', $args[ 'container' ], $args[ 'container_class' ] );
        $html .= sprintf( $args[ 'stats' ], $page_cur, $page_tot ) . ' ';

        if ( $args[ 'first_and_last' ] && ( $page_cur > 1 ) ) {
            $html .= sprintf(
                '<a href="%1$s" title="%2$s"%3$s>%2$s</a>',
                get_pagenum_link(),
                $args[ 'first_label' ],
                ( $args[ 'first_and_last_class' ] ) ? ' class="' . $args[ 'first_and_last_class' ] . '"' : ''
            );
        }

        $links = paginate_links(
            array(
                'base'      => str_replace( $page_tot+1, '%#%', get_pagenum_link( $page_tot+1 ) ),
                'format'    => '?paged=%#%',
                'current'   => max( 1, $page_cur ),
                'total'     => $page_tot,
                'prev_next' => false,
                'end_size'  => 1,
                'mid_size'  => 2
            )
        );

        // Se desejar substituir o link primeiro e último da lista
        // $links = str_replace( '>1</a>', ' title="Primeira">Primeira</a>', $links );
        // $links = str_replace( ">{$page_tot}</a>", ' title="Última">Última</a>', $links );

        $html .= $links;

        if ( $args[ 'first_and_last' ] && ( $page_cur < $page_tot ) ) {
            $html .= sprintf(
                '<a href="%1$s" title="%2$s"%3$s>%2$s</a>',
                get_pagenum_link( $page_tot ),
                $args[ 'last_label' ],
                ( $args[ 'first_and_last_class' ] ) ? ' class="' . $args[ 'first_and_last_class' ] . '"' : ''
            );
        }

        $html .= sprintf( '</%s>', $args[ 'container' ] );
        echo $html;
    }

    /**
     *
     * Retorna os breadcrumbs, caminhos de navegação do site
     *
     * @global integer $page Parte do conteúdo do post
     * @global integer $paged Página atual dos resultados
     * @global object $post Objeto Post do WordPress
     *
     */
    public static function breadcrumbs( $args=array() )
    {
        self::$path = array();

        $defaults = array(
            'here'      => __r( 'You are here:' ),
            'home'      => __r( 'Home Page' ),
            'sep'       => ' > ',
            'part'      => __r( 'Part %d' ),
            'page'      => __r( 'Page %d' ),
            'search'    => __r( 'Search results' ),
            'pg404'     => __r( 'Invalid page' ),
            'archive'   => __r( 'Archive' ),
            'container' => 'nav',
            'class'     => 'breadcrumbs'
        );
        $l = wp_parse_args( $args, $defaults ); // labels

        if ( is_home() || is_front_page() ) {
            array_push( self::$path, $l[ 'home' ] );
        } else {
            array_push(
                self::$path,
                array(
                    'url'   => ROOT_URL,
                    'title' => SITE_NAME,
                    'label' => $l[ 'home' ]
                )
            );

            global $page, $paged;
            if ( is_singular() ) {
                global $post;
                $type = get_post_type_object( $post->post_type );
                if ( $post->post_type == 'post' ) {
                    self::breadcrumb_parents( 'post', $post->ID );
                } else if ( in_array( $post->post_type, array( 'page', 'attachment' ) ) ) {
                    $parent_id = $post->post_parent;
                    if ( $parent_id > 0 )
                        self::breadcrumb_parents( get_post_type( $parent_id ), $post->post_parent );
                } else if ( $type->has_archive ) {
                    array_push(
                        self::$path,
                        array(
                            'url'   => get_post_type_archive_link( $post->post_type ),
                            'label' => $type->label,
                            'type'  => 'post_type_single',
                            'object'=> $type
                        )
                    );
                } else {
                    $path = apply_filters( 'breadcrumbs_singular', null, $post, $type );
                    if ( is_array( $path ) ) {
                        foreach ( $path as $p )
                            array_push( self::$path, $p );
                    }
                }

                if ( $page > 1 ) {
                    array_push(
                        self::$path,
                        array(
                            'url'   => get_permalink( $post->ID ),
                            'label' => $post->post_title,
                            'type'  => 'single',
                            'object'=> $post
                        ),
                        sprintf( $l[ 'part' ], $page )
                    );
                } else {
                    array_push( self::$path, $post->post_title );
                }
            } else if ( is_search() ) {
                array_push( self::$path, $l[ 'search' ] );
            } else if ( is_404() ) {
                array_push( self::$path, $l[ 'pg404' ] );
            } else {
                $q = get_queried_object();
                if ( $q ) {
                    if ( is_post_type_archive() ) {
                        if ( $paged > 1 ) {
                            array_push(
                                self::$path,
                                array(
                                    'url'	=> get_post_type_archive_link( $q->name ),
                                    'label' => $q->labels->name,
                                    'type'  => 'post_type_archive',
                                    'object'=> $q
                                ),
                                sprintf( $l[ 'page' ], $paged )
                            );
                        } else {
                            array_push(
                                self::$path,
                                $q->labels->name
                            );
                        }
                    } else {
                        if ( isset( $q->parent ) ) {
                            $term_parent = (int) $q->parent;
                            if ( $term_parent ) {
                                do {
                                    $item = get_term( $term_parent, $q->taxonomy );
                                    array_push(
                                        self::$path,
                                        array(
                                            'url'   => get_term_link( $item->slug, $item->taxonomy ),
                                            'label' => $item->name,
                                            'type'  => 'term',
                                            'object'=> $item
                                        )
                                    );
                                    $term_parent = (int) $item->parent;
                                } while ( $term_parent > 0 );
                            }
                        }

                        $tax = get_taxonomy( $q->taxonomy );
                        if ( $paged > 1 ) {
                            array_push(
                                self::$path,
                                array(
                                    'url'	=> get_term_link( $q->slug, $q->taxonomy ),
                                    'label' => $tax->labels->singular_name . ': ' . $q->name,
                                    'type'  => 'term',
                                    'object'=> $q
                                ),
                                sprintf( $l[ 'page' ], $paged )
                            );
                        } else {
                            array_push(
                                self::$path,
                                $tax->labels->singular_name . ': ' . $q->name
                            );
                        }
                    }
                } else {
                    array_push(
                        self::$path,
                        'Arquivo'
                    );
                }
            }
        }

        $path = apply_filters( 'breadcrumbs_path', self::$path );
        if ( is_array( $path ) ) {
            printf(
                '<%s%s>%s ',
                $l[ 'container' ],
                ( $l[ 'class' ] ) ? ' class="' . $l[ 'class' ] . '"' : '',
                $l[ 'here' ]
            );
            foreach ( $path as $p ) {
                if ( is_array( $p ) ) {
                    if ( !isset( $p[ 'title' ] ) )
                        $p[ 'title' ] = $p[ 'label' ];

                    printf(
                        '<a href="%s" title="%s">%s</a>%s',
                        $p[ 'url' ],
                        $p[ 'title' ],
                        $p[ 'label' ],
                        $l[ 'sep' ]
                    );
                } else {
                    echo $p;
                }
            }
            printf( '</%s>', $l[ 'container' ] );
        }
    }

    /**
     *
     * Função auxiliar que recupera os links dos itens acima na hierarquia de navegação
     *
     * @param string $type Tipo de post
     * @param int $post_id ID do post do WordPress
     *
     */
    private static function breadcrumb_parents( $type, $post_id )
    {
        $path = array();
        switch ( $type )
        {
            case 'post':
                $cats = wp_get_post_categories( $post_id );
                if ( is_array( $cats ) ) {
                    // somente uma categoria
                    $cat_id = array_shift( $cats );
                    while ( $cat_id > 0 ) {
                        $cat = get_term( $cat_id, 'category' );
                        array_push(
                            $path,
                            array(
                                'url'   => get_category_link( $cat_id ),
                                'label' => $cat->name,
                                'type'  => 'category',
                                'object'=> $cat
                            )
                        );
                        $cat_id = (int) $cat->parent;
                    }
                }
                break;
            case 'page':
                $parent_id = $post_id;
                while ( $parent_id > 0 ) {
                    $p = get_post( $parent_id );
                    array_push(
                        $path,
                        array(
                            'url'   => get_permalink( $parent_id ),
                            'label' => $p->post_title,
                            'type'  => 'page',
                            'object'=> $p
                        )
                    );
                    $parent_id = $p->post_parent;
                }
                break;
        }
        self::$path = array_merge( self::$path, array_reverse( $path ) );
    }


    // }}}

    /**
     *
     * Recupera o resumo do post ou então um resumo do conteúdo
     *
     * @param object $post
     * @param integer $excerpt_size Tamanho do resumo em quantidade de palavras
     * @param string $more_symbol Símbolo para representar continuidade do texto
     * @return string $excerpt Resumo formatado
     *
     */
    public static function get_the_excerpt( $post=null, $excerpt_size=30, $more_symbol='...' )
    {
        if ( !is_object( $post ) )
            global $post;

        /*
        if ( post_password_required() )
            return __( 'There is no excerpt because this is a protected post.' ); */

        $excerpt = $post->post_excerpt;
        if ( !$excerpt ) {
            $excerpt = wp_trim_words(
                strip_shortcodes( strip_tags( $post->post_content ) ),
                $excerpt_size,
                $more_symbol
            );
        }

        return apply_filters( 'get_the_excerpt', $excerpt );
    }

}

// @fix Paginate

?>