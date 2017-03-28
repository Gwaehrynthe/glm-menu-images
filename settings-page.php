<?php

add_action( 'admin_menu', 'menu_images_settings' );

function menu_images_settings(){
    add_options_page( 'Menu Images', 'Menu Images', 'manage_options','menu-images', 'settings_page');
}

function settings_page(){
    echo "settings";
}