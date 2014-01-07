<?php
/*
Plugin Name: Select Posts Widget
Description: Select Posts
Version: 0.1
Author: Mat Gargano
Author URI: http://www.matgargano.com
*/

foreach ( glob( plugin_dir_path(__FILE__) . "lib/*.php" ) as $filename ) {
  include $filename;
}

Select_posts_widget::init();
