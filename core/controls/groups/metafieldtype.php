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
        $controls['post'] = [
            'type' => 'e-query',
            'placeholder' => __('Search Post Custom Field', 'e-addons'),
            'label_block' => true,
            'query_type' => 'metas',
            'object_type' => 'post',
            'condition' => [
                'type' => 'post'
            ],
            'required' => 'true',
        ];
        $controls['term'] = [
            'type' => 'e-query',
            'placeholder' => __('Search Term Custom Field', 'e-addons'),
            'label_block' => true,
            'query_type' => 'metas',
            'object_type' => 'term',
            'condition' => [
                'type' => 'term'
            ],
            'required' => 'true',
        ];
        $controls['user'] = [
            'type' => 'e-query',
            'placeholder' => __('Search User Custom Field', 'e-addons'),
            'label_block' => true,
            'query_type' => 'metas',
            'object_type' => 'user',
            'condition' => [
                'type' => 'user'
            ],
            'required' => 'true',
        ];

        $controls['attachment'] = [
            'type' => 'e-query',
            'placeholder' => __('Search Media Custom Field', 'e-addons'),
            'label_block' => true,
            'query_type' => 'metas',
            'object_type' => 'attachment',
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
     * @return array Processed fields.
     */
    protected function prepare_fields($fields) {
        $args = $this->get_args();
        if (!empty($args['multiple'])) {
            $fields['post']['multiple'] = $args['multiple'];
            $fields['term']['multiple'] = $args['multiple'];
            $fields['user']['multiple'] = $args['multiple'];
            $fields['attachment']['multiple'] = $args['multiple'];
        }
        if (!empty($args['frontend_available'])) {
            $fields['post']['frontend_available'] = $args['frontend_available'];
            $fields['term']['frontend_available'] = $args['frontend_available'];
            $fields['user']['frontend_available'] = $args['frontend_available'];
            $fields['attachment']['frontend_available'] = $args['frontend_available'];
        }
        if (!empty($args['label'])) {
            $fields['post']['label'] = 'From <b>Post </b>' . $args['label'];
            $fields['term']['label'] = 'From <b>Term </b>' . $args['label'];
            $fields['user']['label'] = 'From <b>User </b>' . $args['label'];
            $fields['attachment']['label'] = 'From <b>Media </b>' . $args['label'];
        }

        return parent::prepare_fields($fields);
    }

    protected function get_default_options() {
        return [
            'popover' => false,
            'show_label' => true,
        ];
    }

}
