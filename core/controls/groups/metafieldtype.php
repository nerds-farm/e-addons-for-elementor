<?php

namespace EAddonsForElementor\Core\Controls\Groups;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;

if (!defined('ABSPATH')) {
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
            'label' => __('Custom Field', 'e-addons') . '<b> ' . __('from', 'e-addons') . ':</b>',
            'type' => Controls_Manager::CHOOSE,
            'show_label' => false,
            'options' => [
                'post' => [
                    'title' => __('Post'),
                    'icon' => 'fas fa-thumbtack',
                ],
                'term' => [
                    'title' => __('Term'),
                    'icon' => 'fas fa-folder-open',
                ],
                'user' => [
                    'title' => __('User'),
                    'icon' => 'fas fa-user',
                ],
                'attachment' => [
                    'title' => __('Attachment'),
                    'icon' => 'fas fa-images',
                ]
            ],
            'default' => 'post',
            'required' => 'true',
        ];
        $controls['key_post'] = [
            'label' => '<b>' . __('Post') . '</b> ' . __('Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search Post Custom Field', 'e-addons'),
            'label_block' => true,
            'frontend_available' => true,
            'query_type' => 'metas',
            'object_type' => 'post',
            'condition' => [
                'type' => 'post'
            ],
            'required' => 'true',
        ];
        $controls['key_term'] = [
            'label' => '<b>' . __('Term') . '</b> ' . __('Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search Term Custom Field', 'e-addons'),
            'label_block' => true,
            'frontend_available' => true,
            'query_type' => 'metas',
            'object_type' => 'term',
            'condition' => [
                'type' => 'term'
            ],
            'required' => 'true',
        ];
        $controls['key_user'] = [
            'label' => '<b>' . __('User') . '</b> ' . __('Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search User Custom Field', 'e-addons'),
            'label_block' => true,
            'frontend_available' => true,
            'query_type' => 'metas',
            'object_type' => 'user',
            'condition' => [
                'type' => 'user'
            ],
            'required' => 'true',
        ];

        $controls['key_attachment'] = [
            'label' => '<b>' . __('Media') . '</b> ' . __('Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search Media Custom Field', 'e-addons'),
            'label_block' => true,
            'frontend_available' => true,
            'query_type' => 'metas',
            'object_type' => 'attachment',
            'condition' => [
                'type' => 'attachment'
            ],
            'required' => 'true',
        ];

        return $controls;
    }

    protected function get_default_options() {
        return [
            'popover' => false,
            'show_label' => true,
            'custommeta_source_querytype' => 'post'
        ];
    }

}
