<?php


class Select_Posts_Widget extends WP_Widget {

	protected static $text_domain = 'select_posts_widget';
	protected static $ver = '0.7.0'; //for cache busting
	protected static $transient_limit = 600;

	/**
	 * Initialization method
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'widgets_init', array( __CLASS__, 'register_widget' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'enqueue_customizer' ) );
		add_action( 'wp_ajax_post_type_switcher', array( $this, 'post_type_switcher_callback' ) );
		add_action( 'admin_notices', array( __CLASS__, 'check_dependencies' ) );

	}

	/**
	 * Checks plugin dependencies
	 *
	 * @return void
	 */
	static function check_dependencies() {

		$dependencies = array(
			'Post Selection UI' => array(
				'url'   => 'https://github.com/voceconnect/post-selection-ui',
				'class' => 'Post_Selection_UI'
			)
		);

		foreach ( $dependencies as $plugin => $plugin_data ) {
			if ( ! class_exists( $plugin_data['class'] ) ) {
				$notice = sprintf( 'The Select Posts Widget cannot be utilized without the <a href="%s" target="_blank">%s</a> plugin.', esc_url( $plugin_data['url'] ), $plugin );
				self::add_admin_notice( __( $notice, 'select-posts-widget' ) );
			}
		}

	}

	/**
	 * Display admin notice message
	 * @param string $notice
	 *
	 * @return void
	 */
	static function add_admin_notice( $notice ) {

		echo '<div class="error"><p>' . $notice . '</p></div>';

	}

	/**
	 * Register widget with WordPress.
	 *
	 * @return void
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
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$template_file = plugin_dir_path( dirname( __FILE__ ) ) . 'views/widget.php';
		$template_file = apply_filters( 'spw_template', $template_file );
		$title         = ( ! empty( $instance['title'] ) ) ? $instance['title'] : null;
		$title         = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$posts = isset( $instance['post_ids'] ) ? explode( ',', $instance['post_ids'] ) : null;

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
		$instance              = array();
		$instance['title']     = esc_attr( $new_instance['title'] );
		$instance['post_ids']  = esc_attr( $new_instance['post_ids'] );
		$instance['post_type'] = esc_attr( $new_instance['post_type'] );
		delete_transient( $this->id );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * Filter 'spw_post_types' - restrict post types that can be used with this plugin
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

		$public_post_types = self::get_post_types();
		$post_type      = isset( $instance['post_type'] ) ? $instance['post_type'] : $public_post_types[0];
		$post_ids_array = isset( $instance['post_ids'] ) ? explode( ',', $instance['post_ids'] ) : array();


		?>

		<div class="spw-form" data-post-type="<?php echo $post_type; ?>">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', self::$text_domain ); ?></label>
				<input class="widefat title" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_name( 'post_type' ); ?>">Post Type:</label>
				<select class="post-type widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>"><?php
					foreach ( $public_post_types as $public_post_type ) {

						if ( 'attachment' !== $public_post_type->name ) { //let's not include attachments
							?>
							<option value="<?php echo $public_post_type->name; ?>" <?php selected( $post_type, $public_post_type->name ) ?>><?php echo $public_post_type->labels->name; ?></option>
						<?php
						}

					}
					?></select>
			</p>

			<div class="spinner"></div>
			<div class="post-selection-ui-wrap">
				<?php
				echo $this->post_selection_ui( $post_type, $post_ids_array );
				?>
			</div>

			<input type="hidden" class="security" value="<?php echo wp_create_nonce( 'select-posts-widget' ) ?>">


		</div>

	<?php
	}

	/**
	 * Get the post types
	 * @return array of post type objects
	 */
	public static function get_post_types(){

		return apply_filters( 'spw_post_types', get_post_types( array( 'public' => true ), 'objects', 'and' ) );

	}

	/**
	 * Returns the post selection ui
	 *
	 * @param       $post_type
	 * @param array $post_ids_array
	 *
	 * @return string
	 */
	public function post_selection_ui( $post_type, $post_ids_array = array() ) {

		ob_start();
		$post_type = (array) $post_type;

		if ( function_exists( 'post_selection_ui' ) ) {
			echo post_selection_ui( $this->get_field_name( 'post_ids' ), array(
					'post_type' => $post_type,
					'selected'  => $post_ids_array
				)
			);
		} else {
			?>
			<strong>Missing Post Selection UI Plugin</strong>
		<?php
		}

		return ob_get_clean();

	}

	/**
	 * Ajax callback for switching post type
	 *
	 * @return void
	 */
	public function post_type_switcher_callback() {

		if ( ! isset( $_POST['postType'] ) ) {
			die();
		}
		$posts    = $_POST['posts'];
		$post_ids = $posts ? explode( ',', $posts ) : array();
		check_ajax_referer( 'select-posts-widget', 'security' );
		$psui = array( 'psui' => $this->post_selection_ui( $_POST['postType'], $post_ids ) );
		wp_send_json( $psui );


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
				'post_type'           => 'any'
			);

			$posts = new WP_Query( $args );

			// let's cache this for some time, but not permanently since the post titles could theoretically change

			set_transient( $transient_key, $posts, self::$transient_limit );

		}

		return $posts;
	}

	/**
	 * Enqueue CSS and JS in admin
	 *
	 * @return void
	 */
	public static function enqueue() {

		if ( is_admin() ) {
			wp_enqueue_style( 'spw-admin', plugins_url( 'css/' . 'spw-admin.min.css', dirname( __FILE__ ) ), false, self::$ver );
			wp_enqueue_script( 'spw-admin', plugins_url( 'js/' . 'spw-admin.min.js', dirname( __FILE__ ) ), array( 'jquery' ), self::$ver, true );
		}

	}

	/**
	 * Enqueue CSS and JS in customizer
	 *
	 * @return void
	 */
	public function enqueue_customizer() {

		wp_enqueue_script( 'spw-customizer', plugins_url( 'js/' . 'spw-customizer.min.js', dirname( __FILE__ ) ), array(
			'jquery',
			'underscore'
		), self::$ver, true );

	}


	/**
	 * Perform the actual registration of the widget
	 *
	 * @return void
	 */
	public static function register_widget() {

		register_widget( "Select_Posts_Widget" );

	}

}
