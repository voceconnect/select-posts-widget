<?php


class Spw_ajax {
    
    public static function init(){
        add_action( 'wp_ajax_spw_search', array( __CLASS__, 'search_callback' ) );
    }

    public static function search_callback() {
        if ( !isset( $_POST['query'] )) {
            die();
        }
        $notInArray = array();
        $post_type = apply_filters( 'spw_post_type', array('post') );
        if ( isset( $_POST['alreadySelected'] ) ){
            $notInArray = json_decode( $_POST['alreadySelected'] );
        }
        $query = esc_attr( $_POST['query'] );
        $args = array(
            's' => $query,
            'post__not_in' => $notInArray,
            'post_type' => $post_type
        );


        $posts = new WP_Query( $args );
        if ( $posts->have_posts() ) :
            while ( $posts->have_posts() ) : $posts->the_post(); ?>
                    <?php echo '<div class="search-result" data-post-id="' . get_the_ID() . '"><div class="spw-plus"> + </div> ' . get_the_title() . '</div>'; ?>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p class="no-results">No results</p>
        <?php endif; ?>

        <?php die();  ?>
    <?php }    
}

Spw_ajax::init();