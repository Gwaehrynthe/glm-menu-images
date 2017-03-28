<?php
/**
 * Customize the output of menus for Foundation top bar
 */
if ( ! class_exists( 'Glm_Theme_Top_Bar_Walker' ) ) :
class Glm_Theme_Top_Bar_Walker extends Walker_Nav_Menu {
    function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
        static $mainLevelCounter;
        if ($depth == 0) {
            ++$mainLevelCounter;
        }

        $element->has_children = ! empty( $children_elements[ $element->ID ] );
        $element->classes[] = ( $element->current || $element->current_item_ancestor ) ? 'active' : '';
        $element->classes[] = ( $element->has_children && 1 !== $max_depth ) ? 'has-dropdown' : '';
        if( $element->ID === 231 || $element->ID === 232 ){
            $element->classes[] = ( $element->post_parent == 0 && $mainLevelCounter < 1 ) ? '' : 'drop-right parent';
        } else {
            $element->classes[] = ( $element->post_parent == 0 && $mainLevelCounter < 1 ) ? '' : 'drop-left parent';
        }
        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

    function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
        $item_html = '';
        parent::start_el( $item_html, $object, $depth, $args );

        // Insert style to display page's thumbnail $depth == 0 && has_post_thumbnail((int)$object->object_id
        $item_style = ''; 
        $meta_image = get_post_meta($object->ID, '_menu_item_image');
        $meta_image = $meta_image[0];
        $meta_width = get_post_meta($object->ID, '_menu_image_width',true);
        $meta_height = get_post_meta($object->ID, '_menu_image_height',true);
        $meta_crop = get_post_meta($object->ID, '_menu_image_crop',true);
        
        
        if ($meta_image){

            if( $depth !== 0 && $meta_image !== ''){
                $item_style .= "<style>
                #menu-item-".$object->ID.":before {
                display: inline-block;
                width: ".$meta_width."px;
                height: ".$meta_height."px;
                content: ' ';
                background-image: url('".$meta_image."');
                background-size: cover;
                background-position: ".$meta_crop.";
                </style>";

            } else if ( $depth !== 0 && ! has_post_thumbnail( (int)$object->object_id ) ){
                $thumbnail = get_template_directory_uri() . '/assets/interior-pg-header-image.jpg';
                $item_style .= "<style>
                #menu-item-".$object->ID.":before {
                display: inline-block;
                width: 200px;
                height: 100px;
                content: ' ';
                background-image: url('".$thumbnail."');
                background-size: cover;
                </style>";
            }
        }
        //$output .= ( 0 == $depth ) ? '<li class="divider"></li>' : '';
        $classes = empty( $object->classes ) ? array() : (array) $object->classes;
        if ( in_array( 'label', $classes ) ) {
            //$output .= '<li class="divider"></li>';
            $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '<label>$1</label>', $item_html );
        }
        if ( in_array( 'divider', $classes ) ) {
            $item_html = preg_replace( '/<a[^>]*>( .* )<\/a>/iU', '', $item_html );
        }
        if ($item_style)
            $output .= $item_style;
        $output .= $item_html;
    }

    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= "\n<ul class=\"sub-menu dropdown medium-block-grid-5\">\n";
    }
}
endif;
if ( ! class_exists( 'Glm_Theme_Off_Canvas_Walker' ) ) :
class Glm_Theme_Off_Canvas_Walker extends Walker_Nav_Menu {
    function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
        $element->has_children = ! empty( $children_elements[ $element->ID ] );
        $element->classes[] = ( $element->current || $element->current_item_ancestor ) ? 'active' : '';
        $element->classes[] = ( $element->has_children && 1 !== $max_depth ) ? 'page_item_has_children' : '';
        $element->classes[] = 'page_item';

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

    function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
        $item_html = '';
        parent::start_el( $item_html, $object, $depth, $args );

        $classes = empty( $object->classes ) ? array() : (array) $object->classes;

        if ( in_array( 'label', $classes ) ) {
            $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '<label>$1</label>', $item_html );
        }

        $output .= $item_html;
    }

    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= "\n<ul class=\"children\">\n";
    }

    function end_lvl(&$output, $depth = 0, $args = array()){
        $output .= '</ul>';
    }
}
endif;