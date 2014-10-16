=== Select Posts Widget ===

Contributors: matstars  
Tags: post, widget  
Tested up to: 4.0
Requires at least: 3.5  
Stable tag: 0.7.0
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
An easy to use (and extend) widget that allows you to selectively curate posts using a simple drag and drop interface.

The following filters are available:

* spw_template to override the output template for the widget, see FAQ below for more information.
* spw_post_types to override the post types available for this widget, see FAQ below for more information.
* widget_title this is a WordPress core filter [see here](http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters) for more information.

== Installation ==
> See [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

== Frequently Asked Questions ==

= How do you use a custom template? =

Simply extend the plugin using the `spw_template` filter.

    <?php
        add_filter( 'spw_template', 'customize_spw_template_filter' );
        function customize_spw_template_filter( $template ){
            $template_dir = get_template_directory();
            return $template_dir . '/views/custom-spw.php';
        }
    ?>

= How do I restrict post types from being used in these widgets? =

Simply extend the plugin using the `spw_post_types` filter.

    <?php
        add_filter( 'spw_post_types', 'customize_spw_post_types' );
        function customize_spw_post_types( $post_types ){
            unset($post_types['page']);
            return $post_types;
        }
    ?>

== Changelog ==

**0.7.0**
* Refactored post selection to use the post selection ui (https://github.com/voceconnect/post-selection-ui).
* Restrict post types available to widgets.
* Added functionality that allows Can change post types on the fly.

**0.6.0**  
* Works with widget section in the theme customizer.

**0.5.1**  
* Changed composer type to wordpress-plugin (was library).

**0.5.0**  
* Bound javascript events on 'body' instead of '.widget' to ensure events persist after destroying/creating widget.
* Added templating for individual widgets.
* Added indicator if no posts are selected.
* Refactored WET code.
* Updated code comments.
  
**0.4.1**  
* Added readme.

**0.4**  
* Added filter to allow developers ability to use custom template for widget.
