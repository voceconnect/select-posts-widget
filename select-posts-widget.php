<?php
/*
Plugin Name: Select Posts Widget
Contributors: matstars, voceplatforms
Description: An easy to use (and extend) widget that allows you to selectively curate posts using a simple drag and drop interface.
Stable tag: 0.6.0
Tested up to: 4.0
Author URI: http://voceconnect.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

require __DIR__ . '/lib/class-select-posts-widget.php';

$select_posts_widget = new Select_Posts_Widget;
$select_posts_widget->init();
