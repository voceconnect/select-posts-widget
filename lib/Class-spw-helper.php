<?php

class Spw_helper {
    
    /**
     * Return the post types for this widget
     *
     *
     * Filter(s): 
     * 'spw_post_type' - filter what post types are included
     *
     */

    public static function post_types(){
        $default_post_types = array( 'post' );
        $post_types = apply_filters( 'spw_post_type', $default_post_types );        
        return $post_types;
    }

}