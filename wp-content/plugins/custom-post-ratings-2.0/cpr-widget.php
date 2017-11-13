<?php

class KDM_Ratings_Widget extends WP_Widget
{

    private $prefix = 'kdm_';

    public function KDM_Ratings_Widget()
    {
        parent::WP_Widget( 'cpr-widget', $name='Post Ratings' );
    }

    public function widget( $args, $inst )
    {
		$opt = array(
			'meta_key' 		=> $this->prefix . 'ratings',
			'posts_per_page'=> (int) $inst[ 'count' ],
			'orderby'		=> 'meta_value_num',
			'order'			=> $inst[ 'order' ]
		);

		if ( $inst[ 'date' ] == 'month' ) {
			$opt[ 'monthnum' ] = date( 'n' );
			$opt[ 'year' ] = date( 'Y' );
		}

        echo $args[ 'before_widget' ];
        echo $args[ 'before_title' ] . $inst[ 'title' ] . $args[ 'after_title' ];

		$q = new WP_Query( $opt );
		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();
				global $post;
				$title = esc_attr( get_the_title() );
				echo '<li><a href="' . get_permalink() . '" title="' . $title . '">' . $title . '</a> ['
					. get_post_meta( $post->ID, $this->prefix . 'ratings', true ) . ']</li>';
			}
		} else {
			echo '<li>Nenhum post foi votado...</li>';
		}

        echo $args[ 'after_widget' ];
    }

    public function update( $new, $old )
    {
        return array_merge( $old, $new );
    }

    public function form( $inst ) {
		if ( $inst )
			$opt = array(
				'title' => esc_attr( $inst[ 'title' ] ),
				'count' => (int) $inst[ 'count' ],
				'date'  => $inst[ 'date' ],
				'order' => $inst[ 'order' ]
			);
		else
			$opt = array(
				'title' => 'Melhores votados',
				'count' => 5,
				'date'  => 'month',
				'order' => 'DESC'
			);
		?>
        <p><label>Título: <input name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $opt[ 'title' ]; ?>" /></label></p>
		<p><label>Qtd. de posts: <input name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo $opt[ 'count' ]; ?>" /></label></p>
		<p>
			<label>Limitar por data:
			<select name="<?php echo $this->get_field_name( 'date' ); ?>">
				<?php
				$dates = array(
					'month' => 'Mensal',
					'none'  => 'Nenhuma'
				);
				foreach ( $dates as $key=>$value ) {
					echo '<option value="' . $key . '"';

					if ( $key == $opt[ 'date' ] )
						echo ' selected="true"';

					echo '>' . $value . '</option>';
				} ?>
			</select>
			</label>
		</p>
		<p>
			<label>Ordem:
			<select name="<?php echo $this->get_field_name( 'order' ); ?>">
				<?php
				$orders = array( 'DESC', 'ASC' );
				foreach ( $orders as $order ) {
					echo '<option value="' . $order . '"';

					if ( $order == $opt[ 'order' ] )
						echo ' selected="true"';

					echo '>' . $order . '</option>';
				} ?>
			</select>
			</label>
		</p>
        <?php
    }
}

?>