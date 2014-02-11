<?php

class Select_Posts_Widget_Tests extends WP_UnitTestCase {
 

    

    function test_scripts_loaded() {
        set_current_screen('widgets');
        $this->assertFalse( wp_script_is( 'spw-admin' ) );
        do_action( 'admin_print_scripts-widgets.php' );
        $this->assertTrue( wp_script_is( 'spw-admin' ) );
 
    } 

} 