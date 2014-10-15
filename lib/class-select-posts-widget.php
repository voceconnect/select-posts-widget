<?php


class Select_Posts_Widget extends WP_Widget {

	protected static $text_domain = 'select_posts_widget';
	protected static $ver = '0.7.0'; //for cache busting
	protected static $transient_limit = 600;

	/**
	 * Initialization method
	 */
	public function init() {
		add_action( 'widgets_init', array( __CLASS__, 'register_widget' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'enqueue_customizer') );

	}

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'select_posts_widget', // Base ID
			'Select Posts Widget', // Name
			array( 'description' => __( 'Select & Customize Post Widgets', self::$text_domain ), )
		);
	}


	/**
	 * Front-end display of widget.
	 *
	 * Filter 'spw_template' - template allowing a theme to use its own template file
	 * Filter 'widget_title' - this is a WordPress core filter see http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters for more information.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$template_file = plugin_dir_path( dirname( __FILE__ ) ) . 'views/widget.php';
		$template_file = apply_filters( 'spw_template', $template_file, $this->id );
		$title         = ( ! empty( $instance['title'] ) ) ? $instance['title'] : null;
		$title         = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$posts         = isset( $instance['post_ids'] ) ? explode( ',', $instance['post_ids'] ) : null;

		if ( ! is_array( $posts ) || ! count( $posts ) ) {
			return;
		}
		$posts = $this->get( $posts, $this->id );

		extract( $args );
		echo $args['before_widget'];
		include( $template_file );
		echo $args['after_widget'];

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = array();
		$instance['title']    = esc_attr( $new_instance['title'] );
		$instance['post_ids'] = esc_attr( $new_instance['post_ids'] );
		delete_transient( $this->id );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = __( 'Posts', self::$text_domain );
		}

		$post_ids_array = isset($instance['post_ids']) ? explode( ',', $instance['post_ids'] ) : array();

		?>
		<div class="spw-form">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', self::$text_domain ); ?></label>
				<input class="widefat title" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
			</p>



			<?php
			$post_types = $this->post_types( $this->id );

			if ( function_exists( 'post_selection_ui' ) ) {
				echo post_selection_ui( $this->get_field_name( 'post_ids' ), array(
						'post_type' => $post_types,
						'selected'  => $post_ids_array
					)
				);
			} else {
				?>
				<strong>Missing Post Selection UI Plugin</strong>
			<?php
			}


			$output_post_type = null;
			foreach ( $post_types as $post_type ) {
				if ( $output_post_type ) {
					$output_post_type .= ', ';
				}
				$output_post_type .= $post_type;
			}

			?>
			<p>This widget is registered to display the following post type<?php echo count( $post_types ) > 1 ? 's' : ''; ?>:
				<strong><?php echo $output_post_type; ?> </strong></p>
			<hr>

		</div>

	<?php
	}


	/**
	 * Get the posts
	 *
	 * @param array  $post_ids      List of posts to be retrieved
	 * @param string $transient_key to keep transients consistent use the id of the widget as the transient key
	 *
	 * @return array $posts array of post objects
	 */
	public function get( $post_ids, $transient_key ) {

		$posts = get_transient( $transient_key );
		if ( ! $posts ) {

			$post_types = $this->post_types( $this->id );
			if ( is_array( $post_ids ) ) {
				$post_ids = array_unique( $post_ids );
			} else {
				return; // we do not want anything that doesn't have posts
			}
			$args = array(
				'ignore_sticky_posts' => true,
				'post__in'            => $post_ids,
				'orderby'             => 'post__in',
				'posts_per_page'      => - 1,
				'post_status'         => 'publish',
				'post_type'           => $post_types
			);

			$posts = new WP_Query( $args );

			// let's cache this for some time, but not permanently since the post titles could theoretically change

			set_transient( $transient_key, $posts, self::$transient_limit );

		}

		return $posts;
	}

	/**
	 * Enqueue CSS and JS in admin
	 */
	public static function enqueue() {

		if ( is_admin() ) {
			wp_enqueue_style( 'spw-admin', plugins_url( 'css/' . 'spw-admin.min.css', dirname( __FILE__ ) ), false, self::$ver );
		}

	}

	/**
	 * Enqueue CSS and JS in customizer
	 */
	public function enqueue_customizer(){

		wp_enqueue_script( 'spw-admin', plugins_url( 'js/' . 'spw-admin.min.js', dirname( __FILE__ ) ), array(
			'jquery',
			'underscore'
		), self::$ver, true );

	}


	/**
	 * Return the post types for this widget
	 *
	 * Filter(s):
	 * 'spw_post_types' - filter what post types are included
	 *
	 * @param $id
	 *
	 * @return mixed|void
	 */
	public function post_types( $id ) {

		$default_post_types = array( 'post' );
		$post_types         = apply_filters( 'spw_post_types', $default_post_types, $id );

		return $post_types;
	}


	/**
	 * Perform the actual registration of the widget
	 */
	public static function register_widget() {

		register_widget( "Select_Posts_Widget" );

	}

}
