<?php

add_action( 'init', array( 'Glm_Nav_Menu_Item_Custom_Fields', 'setup' ) );
class GLM_Nav_Menu_Item_Custom_Fields {
    
	static $options = array(
		'display_template' => '
            <label class="check-image-label" for="check-image-{id}">
                <input id="check-image-{id}" class="field-check-image" type="checkbox">
                Menu Image
            </label>
            <div class="image-preview-wrapper">
                <h4 class="menu-image-header"> Menu Item Image </h4>
                <img id="image-preview-{id}" class="image-preview" src="{value}">
                <select value="{crop}" name="image-crop-{id}" class="crop-options">
                    <option disabled {none} value="">Crop</option>
                    <option {center bottom} value="center bottom">Top</option>
                    <option {center top} value="center top">Bottom</option>
                    <option {center center} value="center center">Top and bottom</option>
                </select>
                <span class="crop-value">Crop: </span>
                <div class="image-dimensions">
                    <label for="image-width-{id}"> Width </label>
                    <input value="{width}" name="image-width-{id}" id="image-width-{id}" type="text" class="image-width">
                    <label for="image-height-{id}"> Height </label>
                    <input value="{height}" name="image-height-{id}" id="image-height-{id}" type="text" class="image-height">
                </div>
                <input id="edit-menu-item-{name}-{id}" class="image-url"type="hidden" name="menu-item-{name}[{id}]" >
                <input id="upload-button-{id}" type="button" class="button upload-button" value="Upload Image" />

                <input style="display: none;" class="delete-image" type="button" value="Delete Image" />
            </div>
		',
	);
    
	static function setup() {
        // admin section only
		if ( !is_admin() )
			return;
        
		$new_fields = apply_filters( 'glm_nav_menu_item_additional_fields', array() );
        
		if ( empty($new_fields) )
			return;
        
		self::$options['fields'] = self::get_fields_schema( $new_fields );
        
		add_filter( 'wp_edit_nav_menu_walker', function () {
			return 'GLM_Walker_Nav_Menu_Edit';
		});
        
		//add_filter( 'xteam_nav_menu_item_additional_fields', array( __CLASS__, '_add_fields' ), 10, 5 );
        
        // run the save post function to update post meta
		add_action( 'save_post', array( __CLASS__, '_save_post' ), 10, 2 );
	}
    
	static function get_fields_schema( $new_fields ) {
		$schema = array();
		foreach( $new_fields as $name => $field) {
			if (empty($field['name'])) {
				$field['name'] = $name;
			}
			$schema[] = $field;
		}
		return $schema;
	}
	static function get_menu_item_postmeta_key($name) {
		return '_menu_item_' . $name;
	}

	static function get_field( $item, $depth, $args ) {
		$new_fields = '';
        $crop_options = array('center bottom' => '', 'center top' => '', 'center center' => '');
        
		foreach( self::$options['fields'] as $field ) {
            
			$field['value'] = str_replace(' ', '/', get_post_meta($item->ID, self::get_menu_item_postmeta_key($field['name']), true) );
			$field['id']    = $item->ID;
            $field['width'] = get_post_meta($item->ID, '_menu_image_width', true);
            $field['height'] = get_post_meta($item->ID, '_menu_image_height', true);
            $field['crop'] = get_post_meta($item->ID, '_menu_image_crop', true);
            $crop = $field['crop'];
            if( array_key_exists($crop, $crop_options) ){
                $field[$crop] = 'selected';
            } else {
                $field[$crop] = 'no';
            }
            
			$new_fields .= str_replace(
				array_map(function($key){ return '{' . $key . '}'; }, array_keys($field)),
				array_values(array_map('esc_attr', $field)),
				self::$options['display_template']
			);
		}
		return $new_fields;
	}

    // update the meta input values
	static function _save_post($post_id, $post) {
		if ( $post->post_type !== 'nav_menu_item' ) {
			return $post_id; // prevent weird things from happening
		}

		foreach( self::$options['fields'] as $field_schema ) {
            
			$form_field_name = 'menu-item-' . $field_schema['name'];
			// @todo FALSE should always be used as the default $value, otherwise we wouldn't be able to clear checkboxes
			if (isset($_POST[$form_field_name][$post_id])) {
                $crop = $_POST['image-crop-' . $post_id];
                $width = $_POST['image-width-' . $post_id];
                $height = $_POST['image-height-' . $post_id];
				$key = self::get_menu_item_postmeta_key($field_schema['name']);
				$value = $_POST[$form_field_name][$post_id];
                
				update_post_meta($post_id, $key, $value);
                update_post_meta($post_id,'_menu_image_crop', $crop);
                update_post_meta($post_id,'_menu_image_width', $width);
                update_post_meta($post_id,'_menu_image_height', $height);
			}
		}
	}
}

// fields are added to the output here
require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
class GLM_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {
	function start_el(&$output, $item, $depth, $args) {
		$item_output = '';
		parent::start_el($item_output, $item, $depth, $args);
		// Inject $new_fields before: <div class="menu-item-actions description-wide submitbox">
		if ( $new_fields = GLM_Nav_Menu_Item_Custom_Fields::get_field( $item, $depth, $args ) ) {
			$item_output = preg_replace('/(?=<div[^>]+class="[^"]*submitbox)/', $new_fields, $item_output);
		}
		$output .= $item_output;
	}
}

add_filter( 'glm_nav_menu_item_additional_fields', 'glm_menu_item_additional_fields' );
function glm_menu_item_additional_fields( $fields ) {
    
	$fields['image'] = array(
		'name'              => 'image',
		'label'             => __('Image', 'glm'),
		'container_class'   => 'menu-image',
		'input_type'        => 'hidden',
	);
	return $fields;
}
?>