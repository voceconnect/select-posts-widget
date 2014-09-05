<?php
/*
Plugin Name: Select Posts Widget
Contributors: matstars, voceplatforms
Description: Select Posts
Stable tag: 0.5.2
Tested up to: 4.0
Author URI: http://voceconnect.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

require __DIR__ . '/lib/class-select-posts-widget.php';

$select_posts_widget = new Select_Posts_Widget;
$select_posts_widget->init();
