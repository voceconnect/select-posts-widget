<?php

class Select_Posts_Widget_Tests extends WP_UnitTestCase {
 
    
	function test_scripts_loaded_on_dashboard_page_false() {
        set_current_screen('dashboard');
        do_action( 'admin_enqueue_scripts' );
        $enqueued = isset( $GLOBALS['wp_scripts']->registered['spw-admin'] );
        $this->assertFalse( $enqueued );
    } 
    

    function test_scripts_loaded_on_widget_page_true() {
        set_current_screen('widgets');
        do_action( 'admin_enqueue_scripts' );
        $enqueued = isset( $GLOBALS['wp_scripts']->registered['spw-admin'] );
        $this->assertTrue( $enqueued );
    } 

} 