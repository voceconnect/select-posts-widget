Select Posts Widget
===================
Contributors: matstars  
Tags: post, widget  
Tested up to: 3.8  
Requires at least: 3.5  
Tested up to: 3.8  
Stable tag: 0.5.0
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description
Widget allowing custom curation.

The following filters are available:

* spw_get_args to override arguments of main query for the widget.
* spw_template to override the output template for the widget, see below for more information.
* spw_WIDGET_NAME_template to override the output template for the widget on a widget by widget basis, the WIDGET_NAME appears on the back-end in the widgets window see below for more information.
* widget_title this is a WordPress core filter [see here](http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters) for more information.
* spw_post_type to override the post types available for this widget.


## Installation
> See [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).


## Usage

#### Example of using a custom template from within your theme pre-PHP 5.3

```php
<?php
    function customize_spw_template_filter( $template ){
        $template_dir = get_template_directory();
        return $template_dir . '/views/custom-spw.php';    
    }
    add_filter( 'spw_template', 'customize_spw_template_filter' );
?>
```


#### Example of using a custom template from within your theme PHP 5.3+ which allows anonymous functions

```php
<?php

    add_filter( 'spw_template', function( $template ){
        $template_dir = get_template_directory();
        return $template_dir . '/views/custom-spw.php';
    });
?>
```


#### Example of using a custom template from within your theme for an individual widget (assuming PHP 5.3+ which allows anonymous functions)

```php
<?php
    add_filter('spw_select_posts_widget-15_template', function($template){
        return get_template_directory() . '/views/custom-spw-15.php';
    });
?>
```
## Changelog

**0.5.0**  
*Bound javascript events on 'body' instead of '.widget' to ensure events persist after destroying/creating widget*
*Added templating for individual widgets*
*Added indicator if no posts are selected*
*Refactored WET code*
*Updated code comments*
  
**0.4.1**  
*Added readme*

**0.4**  
*Added filter to allow developers ability to use custom template for widget*
