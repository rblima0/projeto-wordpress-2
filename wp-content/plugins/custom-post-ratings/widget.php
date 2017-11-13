<?php

class KDM_Ratings_Widget extends WP_Widget
{
    
    function __construct()
    {
        parent::__construct( 'cpr-widget', 'Post Ratings' );
    }
    
    function form( $inst )
    {
        if ( !isset( $inst[ 'title' ] ) || !isset( $inst[ 'count' ] ) ) {
            $inst = array(
                'title' => '',
                'count' => ''
            );
        }
        ?>
        <p>
            <label>Título</label>
            <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $inst[ 'title' ]; ?>" class="widefat" />
        </p>
        <p>
            <label>Quantidade de posts</label>
            <input type="text" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $inst[ 'count' ]; ?>" size="5" />
        </p>
        <?php
    }
    
    function widget( $args, $inst )
    {
        $q = new WP_Query(
            array(
                'meta_key'              => 'cpr-avg',
                'orderby'               => 'meta_value_num',
                'order'                 => 'DESC',
                'ignore_sticky_posts'   => true,
                'posts_per_page'        => (int) $inst[ 'count' ]
            )
        );
        echo $args[ 'before_widget' ];
        
        if ( $inst[ 'title' ] )
            echo $args[ 'before_title' ] . $inst[ 'title' ] . $args[ 'after_title' ];
        
        if ( !$q->have_posts() ) {
            echo '<p>Nenhum post foi votado!</p>';
        } else {
            echo '<ul class="cpr-posts">';
            while ( $q->have_posts() ) {
                $q->the_post();
                global $post;
                printf(
                    '<li><a href="%1$s" title="%2$s">%2$s</a> [%3$s]</li>',
                    get_permalink(),
                    get_the_title(),
                    get_post_meta( $post->ID, 'cpr-avg', true )
                );
            } wp_reset_query();
            echo '</ul>';
        }
        
        echo $args[ 'after_widget' ];
    }
    
}