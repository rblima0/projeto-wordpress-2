<?php
/*
Plugin Name: Custom Post Ratings 2.0
Description: Permite aos usuários cadastrados no site votar nos posts
Version: 2.0
Author: Rodrigo Banci
Author URI: http://rodrigobanci.com.br
*/

register_activation_hook( __FILE__, array( 'KDM_Ratings', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'KDM_Ratings', 'deactivate' ) );

class KDM_Ratings
{

	private static $opt;
	private static $table;
	private static $url;

    private static $prefix = 'kdm_';
    private static $cap = 'manage_options';

    function set_table_name()
    {
        if ( !isset( self::$table ) || !self::$table ) {
            global $wpdb;
            self::$table = $wpdb->prefix . 'postratings';
        }
    }

	function activate()
    {
		self::set_table_name();

		$sql = "CREATE TABLE `" . self::$table . "` (
			`rating_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`post_id` BIGINT UNSIGNED NOT NULL ,
			`user_id` BIGINT UNSIGNED NOT NULL ,
			`rating` TINYINT( 1 ) UNSIGNED NOT NULL ,
			`rating_date` DATETIME NOT NULL
		)";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		self::$opt = array(
            'title' => '',
			'show'  => '1',
			'place' => 'before',
			'votes' => false,
			'avg' => false
		);
		add_option( self::$prefix . 'ratings', self::$opt, null, 'no' );
	}

	public static function deactivate()
    {
		global $wpdb;
        self::set_table_name();

		$wpdb->query(
            $wpdb->prepare( 'DROP TABLE %s', self::$table )
        );

        $wpdb->query(
            $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key=%s", self::$prefix . 'ratings' )
        );

		delete_option( self::$prefix . 'ratings' );
	}

    function setup()
    {
        add_action( 'init',         array( 'KDM_Ratings', 'init' ) );
        add_action( 'admin_menu',   array( 'KDM_Ratings', 'menu' ) );
        add_action( 'wp_ajax_vote', array( 'KDM_Ratings', 'vote' ) );
        add_action( 'widgets_init', array( 'KDM_Ratings', 'widgets' ) );

        add_shortcode( 'KDM_Ratings', array( 'KDM_Ratings', 'shortcode' ) );
    }

	public static function menu()
    {
        add_menu_page( 'Post Ratings - Opções', 'Post Ratings', self::$cap, 'pr-menu', array( 'KDM_Ratings', 'admin_page' ) );
	}

	public static function admin_page() {
		if ( !current_user_can( self::$cap ) )
			wp_die( 'Você não tem permissões suficientes para acessar essa página!' );

		if ( !self::$opt )
			self::$opt = get_option( self::$prefix . 'ratings' );

		if ( isset( $_POST[ 'nonce-rating' ] ) ) {
			if ( check_admin_referer( 'nonce-save', 'nonce-rating' ) ) {
                self::$opt = array();
                $fields = array( 'title', 'show', 'place', 'votes', 'avg' );
                foreach( $fields as $f ) {
                    $field = self::$prefix . $f;
                    self::$opt[ $f ] = ( isset( $_POST[ $field ] ) ) ? $_POST[ $field ] : false;
                }
				update_option( self::$prefix . 'ratings', self::$opt );
				echo '<div class="updated"><p>Configurações atualizadas com sucesso!</p></div>';
			}
		}
		?>
		<div class="wrap">
			<?php screen_icon( 'plugins' ); ?>
			<h2>Post Ratings</h2>
			<p>Página de opções do Plugin Post Ratings</p>
			<form method="post">
				<?php wp_nonce_field( 'nonce-save', 'nonce-rating' ); ?>
				<table class="form-table">
                    <tr>
                        <th>Título do formulário</th>
                        <td>
                            <input name="<?php echo self::$prefix; ?>title" type="text" value="<?php echo self::$opt[ 'title' ]; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>Exibição dos votos</th>
                        <td>
                            <label>
                                <input name="<?php echo self::$prefix; ?>show" type="checkbox" value="1"<?php if ( self::$opt[ 'show' ] == '1' ) echo ' checked="true"'; ?> />
                                Forçar exibição automática da média dos votos
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Local de exibição</th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="radio" name="<?php echo self::$prefix; ?>place" value="before"<?php if ( self::$opt[ 'place' ] == 'before' ) echo ' checked="true"'; ?> />
                                    <span>Antes do conteúdo do post</span>
                                </label><br />
                                <label>
                                    <input type="radio" name="<?php echo self::$prefix; ?>place" value="after"<?php if ( self::$opt[ 'place' ] == 'after' ) echo ' checked="true"'; ?>  />
                                    <span>Após o conteúdo do post</span>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th>Exibição de valores</th>
                        <td>
                            <fieldset>
                                <label>
                                    <input name="<?php echo self::$prefix; ?>votes" type="checkbox" value="1"<?php if ( self::$opt[ 'votes' ] == '1' ) echo ' checked="true"'; ?> />
                                    Exibir total de votos
                                </label><br />
                                <label>
                                    <input name="<?php echo self::$prefix; ?>avg" type="checkbox" value="1"<?php if ( self::$opt[ 'avg' ] == '1' ) echo ' checked="true"'; ?> />
                                    Exibir média da votação
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" value="Salvar Alterações" class="button-primary" /></td>
                    </tr>
				</table>
			</form>
		</div>
	<?php }

	public static function shortcode( $atts )
    {
		extract( shortcode_atts( array(
			'votos' => 'false',
			'media' => 'false',
            'titulo' => ''
		), $atts ) );
		$opt = array(
			'votes' => $votos,
			'avg' => $media,
            'title' => $titulo
		);
		return self::stars( null, $opt );
	}

	public static function stars( $content, $opt=null ) {
		if ( !is_single() ) return $content;

		if ( $opt == null ){
			if ( !self::$opt )
				self::$opt = get_option( self::$prefix . 'ratings' );

			$opt = self::$opt;
		}

		global $post;
		$value = (float) get_post_meta( $post->ID, self::$prefix . 'ratings', true );
		$int_value = round( $value );

        $html = '<p class="custom-post-ratings">' . $opt[ 'title' ] .
            '<span id="post-rating" class="ref-' . $post->ID . '">';

		for ( $i=1; $i<=5; $i++ ) {
			$html .= '<span id="star-' . $i . '"';

			if ( $int_value >= $i )
				$html .= ' class="on"';

			$html .= '></span>';
		}

		if ( ( $opt[ 'avg' ] == '1' ) || ( $opt[ 'avg' ] == 'true' ) )
			$html .= ' Média: ' . number_format( $value, 2, ',', '' );

		if ( ( $opt[ 'votes' ] == '1' ) || ( $opt[ 'votes' ] == 'true' ) ) {
			global $wpdb;
            self::set_table_name();
			$votes = (int) $wpdb->get_var(
                $wpdb->prepare( 'SELECT COUNT(rating_id) FROM ' . self::$table . ' WHERE post_id=%d', $post->ID )
            );
			$html .= ' Total de votos: ' . $votes;
		}

		$html .= '</span></p>';

		if ( isset( $content ) && $content !== null ) {
			if ( $opt[ 'place' ] == 'before' )
				return $html . $content;
			else
				return $content . $html;
		} else {
			return $html;
		}
	}

	public static function init()
    {
		self::set_table_name();

		self::$url = plugins_url( CPR_DIR ) . '/';
		self::$opt = get_option( self::$prefix . 'ratings' );

		if ( self::$opt[ 'show' ] == '1' )
			add_filter( 'the_content', array( 'KDM_Ratings', 'stars' ), 10, 1 );

		wp_register_script( 'cpr-script', self::$url . 'js/post-ratings.js', array( 'jquery' ), null );
		wp_register_style( 'cpr-style', self::$url . 'css/post-ratings.css', array(), null, 'screen' );

		if ( !is_admin() ) {
			wp_enqueue_style( 'cpr-style' );
			if ( is_user_logged_in() && current_user_can( 'subscriber' ) )
				wp_enqueue_script( 'cpr-script' );
		}
	}

	public static function vote()
    {
		if ( !current_user_can( 'subscriber' ) || !isset( $_POST[ 'post_id' ] ) || !isset( $_POST[ 'rating' ] ) )
			die();

		global $wpdb;
		self::set_table_name();

		$rating = $_POST[ 'rating' ];
		$post_id = $_POST[ 'post_id' ];
		$user_id = get_current_user_id();
		$vote = (int) $wpdb->get_var(
            $wpdb->prepare( 'SELECT rating_id FROM ' . self::$table . ' WHERE post_id=%d AND user_id=%d', $post_id, $user_id )
        );

		if ( !$vote ) {
			$values = array(
				'post_id'		=> $post_id,
				'user_id'		=> $user_id,
				'rating'		=> $rating,
				'rating_date'	=> date( 'Y-m-d H:i:s' )
			);
			$wpdb->insert( self::$table, $values );

			$count = (int) $wpdb->get_var(
                $wpdb->prepare( 'SELECT COUNT(rating_id) FROM ' . self::$table . ' WHERE post_id=%d', $post_id )
            );
			$votes = $wpdb->get_results(
                $wpdb->prepare( 'SELECT rating FROM ' . self::$table . ' WHERE post_id=%d', $post_id )
            );
			$total = 0;

			foreach ( $votes as $vote )
				$total += $vote->rating;

			$avg = number_format( $total / $count, 2 );
			update_metadata( 'post', $post_id, self::$prefix . 'ratings', $avg );

			echo round( $avg );
		}

		die();
	}

    function widgets()
    {
        register_widget( 'KDM_Ratings_Widget' );
    }

}

add_action( 'plugins_loaded', array( 'KDM_Ratings', 'setup' ) );

define( 'CPR_DIR',  basename( dirname( __FILE__ ) ) );
define( 'CPR_PATH', WP_PLUGIN_DIR . '/' . CPR_DIR . '/' );

require_once( CPR_PATH . 'cpr-widget.php' );

function post_ratings( $args ){
	$defaults = array(
		'media' => 'false',
		'votos' => 'false'
	);
	$args = wp_parse_args( $args, $defaults );
	echo KDM_Ratings::stars( null, $args );
}

?>
