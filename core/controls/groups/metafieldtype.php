<?php
namespace EAddonsForElementor\Core\Controls\Groups;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Metafieldtype any elements this control is a group control.
 *
 * A control for transformer any elements (rotation, transition, scale  with perspective and origin )
 *
 * @since 1.0.0
 */
class Metafieldtype extends Group_Control_Base {
    
    protected static $fields;

    public static function get_type() {
        return 'metafieldtype';
    }

    protected function init_fields() {
        $controls = [];
        
        $controls['type'] = [
            'label' => __('Custom Field', 'e-addons').'<b> '.__('from','e-addons').':</b>',
            'type' => Controls_Manager::CHOOSE,
            'show_label' => false,
            'options' => [
                'post' => [
                    'title' => 'Post',
                    'icon' => 'fas fa-thumbtack',
                ],
                'term' => [
                    'title' => 'Term',
                    'icon' => 'fas fa-folder-open',
                ],
                'user' => [
                    'title' => 'User',
                    'icon' => 'fas fa-user',
                ],
                'attachment' => [
                    'title' => 'Attachment',
                    'icon' => 'fas fa-images',
                ]
            ],
            'default' => 'post',
            'required' => 'true',
        ];
        $controls['key_post'] = [
            'label' => '<b>Post </b>'.__('Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search Post Custom Field', 'e-addons'),
            'label_block' => true,
            'frontend_available' => true,
            'query_type' => 'metas',
            'object_type' => 'post',
            'default' => '',
            'condition' => [
                'type' => 'post'
            ],
            
			'required' => 'true',
        ];
        $controls['key_term'] = [
            'label' => '<b>Term </b>'.__('Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search Term Custom Field', 'e-addons'),
            'label_block' => true,
            'frontend_available' => true,
            'query_type' => 'metas',
            'object_type' => 'term',
            'default' => '',
            'condition' => [
                'type' => 'term'
            ],
            
			'required' => 'true',
        ];
        $controls['key_user'] = [
            'label' => '<b>User </b>'.__('Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search User Custom Field', 'e-addons'),
            'label_block' => true,
            'frontend_available' => true,
            'query_type' => 'metas',
            'object_type' => 'user',
            'default' => '',
            'condition' => [
                'type' => 'user'
            ],
            
			'required' => 'true',
        ];
        
        $controls['key_attachment'] = [
            'label' => '<b>Media </b>'.__('Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search Media Custom Field', 'e-addons'),
            'label_block' => true,
            'frontend_available' => true,
            'query_type' => 'metas',
            'object_type' => 'attachment',
            'default' => '',
            'condition' => [
                'type' => 'attachment'
            ],
            
			'required' => 'true',
        ];
        
        return $controls;
    }
    /**
	 * Prepare fields.
	 *
	 * Process image size control fields before adding them to `add_control()`.
	 *
	 * @since 1.2.2
	 * @access protected
	 *
	 * @param array $fields Image size control fields.
	 *
	 * @return array Processed fields.
	 */
	/*protected function prepare_fields( $fields ) {
		$image_sizes = $this->get_image_sizes();

		$args = $this->get_args();

		if ( ! empty( $args['default'] ) && isset( $image_sizes[ $args['default'] ] ) ) {
			$default_value = $args['default'];
		} else {
			// Get the first item for default value.
			$default_value = array_keys( $image_sizes );
			$default_value = array_shift( $default_value );
		}

		$fields['size']['options'] = $image_sizes;

		$fields['size']['default'] = $default_value;

		if ( ! isset( $image_sizes['custom'] ) ) {
			unset( $fields['custom_dimension'] );
		}

		return parent::prepare_fields( $fields );
	}*/
    protected function get_default_options() {
        return [
            'popover' => false,
            'show_label' => true,
            'custommeta_source_querytype' => 'post'

        ];
    }
    
}
