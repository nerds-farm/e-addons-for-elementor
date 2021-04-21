<?php

namespace EAddonsForElementor\Core\Controls\Groups;

use EAddonsForElementor\Core\Utils;
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
class Sourcetype extends Group_Control_Base {

    protected static $fields;

    public static function get_type() {
        return 'sourcetype';
    }

    protected function init_fields() {
        $controls = [];

        $controls['type'] = [
            'label' => __('Source', 'e-addons') . '<b> ' . __('from', 'e-addons') . ':</b>',
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
                'author' => [
                    'title' => 'Author',
                    'icon' => 'fas fa-user-edit',
                ],
                'attachment' => [
                    'title' => 'Attachment',
                    'icon' => 'fas fa-images',
                ],
                'option' => [
                    'title' => 'Site Option',
                    'icon' => 'fas fa-globe',
                ],
                'static' => [
                    'title' => 'Static',
                    'icon' => 'fas fa-crosshairs',
                ]
            ],
            'default' => 'post',
            'required' => 'true',
        ];

        $controls['post'] = [
            'type' => 'e-query',
            'placeholder' => __('Search Post', 'e-addons'),
            'description' => __('Leave empty for Current Post', 'e-addons'),
            'label_block' => true,
            'query_type' => 'posts',
            'condition' => [
                'type' => 'post'
            ],
        ];
        $controls['post_field'] = [
            'type' => 'e-query',
            'placeholder' => __('Search Post Field', 'e-addons'),
            'label_block' => true,
            'query_type' => 'fields',
            'object_type' => 'post',
            'condition' => [
                'type' => 'post'
            ],
        ];

        $controls['term'] = [
            'type' => 'e-query',
            'placeholder' => __('Search Term', 'e-addons'),
            'label_block' => true,
            'query_type' => 'terms',
            'condition' => [
                'type' => 'term'
            ],
        ];
        $controls['term_field'] = [
            'type' => 'e-query',
            'placeholder' => __('Search Term Field', 'e-addons'),
            'label_block' => true,
            'query_type' => 'fields',
            'object_type' => 'term',
            'condition' => [
                'type' => 'term'
            ],
        ];

        $controls['user'] = [
            'type' => 'e-query',
            'placeholder' => __('Search User', 'e-addons'),
            'description' => __('Leave empty for Current User', 'e-addons'),
            'label_block' => true,
            'query_type' => 'users',
            'condition' => [
                'type' => 'user'
            ],
        ];
        $controls['user_field'] = [
            'type' => 'e-query',
            'placeholder' => __('Search User Field', 'e-addons'),
            'label_block' => true,
            'query_type' => 'fields',
            'object_type' => 'user',
            'condition' => [
                'type' => ['user', 'author'],
            ],
        ];

        $controls['attachment'] = [
            'type' => 'file',
            'placeholder' => __('Search Media', 'e-addons'),
            'label_block' => true,
            'condition' => [
                'type' => 'attachment'
            ],
        ];
        $controls['attachment_field'] = [
            'type' => 'e-query',
            'placeholder' => __('Search Media Field', 'e-addons'),
            'label_block' => true,
            'query_type' => 'fields',
            'object_type' => 'attachment',
            'condition' => [
                'type' => 'attachment'
            ],
        ];

        $controls['option'] = [
            'type' => 'e-query',
            'placeholder' => __('Search Site Option', 'e-addons'),
            'label_block' => true,
            'query_type' => 'options',
            'condition' => [
                'type' => 'option'
            ],
        ];

        $controls['static'] = [
            'type' => Controls_Manager::TEXT,
            'placeholder' => __('1, abc, 3', 'e-addons'),
            'description' => __('Use as Static values or use as Fallback', 'e-addons'),
            'label_block' => true,
                /* 'condition' => [
                  'type' => 'static'
                  ], */
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
            $fields['option']['multiple'] = $args['multiple'];
        }
        if (!empty($args['frontend_available'])) {
            $fields['post']['frontend_available'] = $args['frontend_available'];
            $fields['term']['frontend_available'] = $args['frontend_available'];
            $fields['user']['frontend_available'] = $args['frontend_available'];
            $fields['attachment']['frontend_available'] = $args['frontend_available'];
            $fields['option']['frontend_available'] = $args['frontend_available'];
        }
        if (!empty($args['label'])) {
            $fields['post']['label'] = $args['label'] . 'From <b> Post</b>';
            $fields['term']['label'] = $args['label'] . 'From <b> Term</b>';
            $fields['user']['label'] = $args['label'] . 'From <b> User</b>';
            $fields['attachment']['label'] = $args['label'] . 'From <b> Media</b>';
            $fields['option']['label'] = $args['label'] . 'From <b> Site Option</b>';
        }
        return parent::prepare_fields($fields);
    }

    public function get_value($field) {
        $value = false;
        switch ($field['type']) {

            case 'option':
                if (!empty($field['option'])) {
                    $value = get_option($settings['option']);
                }
                break;

            case 'post':
                $post_id = !empty($settings['post']) ? $settings['post'] : get_the_ID();
                $value = Utils::get_post_field($settings['post_field'], $post_id);

            case 'term':
                $term_id = !empty($settings['term']) ? $settings['term'] : get_the_ID();
                $value = Utils::get_term_field($settings['term_field'], $term_id);

            case 'user':
                $user_id = get_current_user_id();
            case 'author':
                if (empty($user_id)) {
                    $user_id = get_the_author_meta('ID');
                }
                $value = Utils::get_user_field($settings['user_field'], $user_id);

            case 'static':
                if ($settings['static']) {
                    return $settings['static'];
                }
                break;

            default:
                // media page
                return get_the_ID();
        }
        if (empty($value)) {
            if ($settings['static']) {
                return $settings['static'];
            }
        }
        return $value;
    }

    protected function get_default_options() {
        return [
            'popover' => false,
            'show_label' => true,
        ];
    }

}
