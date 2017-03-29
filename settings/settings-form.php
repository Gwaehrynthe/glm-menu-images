<?php 
    $nav_menus = get_registered_nav_menus(); 

    foreach($nav_menus as $key=>$value){
        $menu_obj = get_term( $key, 'nav_menu' );
        echo $menu_obj;
    }
$theme_locations = get_nav_menu_locations();

$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );

$menu_name = $menu_obj->name;
echo $menu_name;
?>


<form method="POST" action="">
    <input name="default-image-width" type="number">
    <input name="default-image-height" type="number">
    <input name="settingsSubmit" type="submit">
</form>
