Select Posts Widget
===================
Contributors: matstars  
Tags: post, widget  
Tested up to: 3.8  
Requires at least: 3.5  
Tested up to: 3.8  
Stable tag: 0.4.5
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description
Widget allowing custom curation.

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
## Changelog

**0.4.1**  
*Adding readme*

**0.4**  
*Adding filter to allow developers ability to use custom template for widget*
