<?php
/**
 * Plugin Name: GLM Menu Images
 * Plugin URI: http://www.gaslightmedia.com/
 * Description: Gaslight Nav Menu Images
 * Version: 1.0.0
 * Author: Anthony Talarico @ Gaslight Media
 * Author URI: http://www.gaslightmedia.com/
 * License: GPL2
 */

// Check that we're being called by WordPress.
if (!defined('ABSPATH')) {
    die("Please do not call this code directly!");
}

function admin_scripts($hook) {
    if ( 'nav-menus.php' != $hook ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script( 'admin_scripts', plugin_dir_url(__FILE__) . '/main.js', array('jquery'));
    wp_enqueue_style( 'admin_styles', plugin_dir_url(__FILE__) . '/main.css');
}
add_action( 'admin_enqueue_scripts', 'admin_scripts' );

require_once 'menu_walker_edit.php';
require_once 'walker_nav_menu.php';
