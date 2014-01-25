<?php

class Select_posts_widget extends WP_Widget {

    protected static $text_domain = 'select_posts_widget';
    protected static $ver = '0.4'; //for cache busting
    protected static $transient_limit = 60;
    
    /**
     * Initialization method
     */
    public static function init(){
        add_action( 'widgets_init', create_function( '', 'register_widget( "Select_posts_widget" );' ) );
        add_action( 'admin_print_scripts-widgets.php', array( __CLASS__, 'enqueue' ) );
    }

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'select_posts_widget', // Base ID
            'Select Posts Widget', // Name
            array( 'description' => __( 'Select & Customize Post Widgets', self::$text_domain ), ) // Args
        );
    }


    /**
     * Front-end display of widget.
     *
     * Filter 'spw_template' - template allowing a theme to use its own template file
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        
        $template_file = apply_filters( 'spw_template', plugin_dir_path( dirname( __FILE__ ) ) . 'views/widget.php' );
        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        $posts = json_decode( $instance['post-order'] );
        if ( ! is_array( $posts ) || ! count( $posts ) ) {
            return;
        }
        $posts = self::get( $posts, $this->id );
        ?>
        <?php extract( $args ); ?>
        <?php echo $before_widget; ?>
        <?php include( $template_file ); ?>
        <?php echo $after_widget; ?>
        <?php
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
        $instance = array();
        $instance['title'] = esc_attr( $new_instance['title'] );
        $instance['post-order'] = esc_attr( $new_instance['post-order'] );
        delete_transient( $this->id );
        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $post_order = $selected_posts = '';
        if ( !isset( $instance[ 'title' ] ) ) {
            $instance['title'] = __( 'Posts', self::$text_domain );
        }
        if ( isset( $instance['post-order'] ) && $instance['post-order'] && count( json_decode( $instance['post-order'] ) ) ){
            $post_order = json_decode( $instance['post-order'] );
            $posts = self::get( $post_order, $this->id );
            
            if ( $posts->have_posts() ) :
                while ( $posts->have_posts() ) : $posts->the_post(); ?>
                        <?php $selected_posts .= '<div class="selected-post" data-post-id="' . get_the_ID() . '"><div class="spw-minus"> - </div> ' . get_the_title() . '</div>'; ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
                <?php
            else :
                $post_order = '';
            endif; ?>
        
        <?php } ?>
        <div class="spw-form">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', self::$text_domain ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
            </p>
            <p>
                <label for="search"><?php _e( 'Search:', self::$text_domain ); ?></label> 
                <input class="widefat spw-search" name="search" type="text" placeholder="Search..." />
            </p>
            <p class="loading"></p>
            <div class="search-results clearfix">
                
            </div>
            <hr>
            <p>Posts:</p>
            <div class="selected-posts">
                <?php echo $selected_posts; ?>
            </div>


            <input type="hidden" class="post-list" value="<?php echo json_encode( $post_order ) ?>" id="<?php echo $this->get_field_id( 'post-order' ); ?>" name="<?php echo $this->get_field_name( 'post-order' ); ?>" >
        </div>
        <script>

            if (typeof(setSpwSortable) == typeof(Function)) {
                setSpwSortable();
            }
            if (typeof(spwAttachEvents) == typeof(Function)) {
                spwAttachEvents();
            }
        
        </script>
        <?php 
    }


   /**
     * Get the posts
     *
     * Filter(s): 
     * 'spw_post_type' - filter what post types are included
     * 'spw_get_args' - filter args in WP_Query for getting posts
     *
     * @param array $post_ids List of posts to be retrieved
     * @param string $transient_key to keep transients consistent use the id of the widget as the transient key
     *
     * @return array $posts array of post objects
     */    

    public static function get( $post_ids, $transient_key ){
        $post_type = apply_filters( 'spw_post_type', Spw_helper::post_types() );        
        if ( is_array( $post_ids ) ) {
            $post_ids = array_unique( $post_ids );
        } else {
            return; // we do not want anything that doesn't have posts
        }
        $args = array(
                'ignore_sticky_posts'=>true,
                'post__in' => $post_ids,
                'post_type' => $post_type,
                'orderby' => 'post__in',
                'posts_per_page' => -1
        );
        $args = apply_filters( 'spw_get_args', $args );
        $posts = get_transient( $transient_key );
        if ( ! $posts ) {
            $posts = new WP_Query( $args );    
            set_transient( $transient_key, $posts, 60 );
        }
        return $posts;
    }

    /**
     * Enqueue CSS and JavaScripts
     */
    public static function enqueue(){
        if ( is_admin() ) {
            wp_enqueue_style( 'spw-admin', plugins_url( 'css/' . 'spw-admin.min.css', dirname( __FILE__ ) ), false, self::$ver );
            wp_enqueue_script( 'spw-admin', plugins_url( 'javascripts/' . 'spw-admin.min.js', dirname( __FILE__ ) ), array( 'jquery' ), self::$ver, true );
        }   
    }

} 