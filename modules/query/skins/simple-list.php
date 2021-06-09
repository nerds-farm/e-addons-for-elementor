<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use EAddonsForElementor\Modules\Query\Skins\Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Simple List Skin
 *
 * Elementor widget query-posts for e-addons
 *
 */
class Simple_List extends Base {

    public function _register_controls_actions() {
        parent::_register_controls_actions();
        add_action('elementor/element/' . $this->parent->get_name() . '/section_e_query/after_section_end', [$this, 'register_additional_controls'], 20);
    }

    public function get_style_depends() {
        return ['e-addons-common-query'];
    }

    public function get_id() {
        return 'list';
    }

    public function get_title() {
        return __('List', 'e-addons');
    }

    public function get_icon() {
        return 'eadd-skin-list';
    }

    public function register_additional_controls() {
        //var_dump($this->get_id());
        //var_dump($this->parent->get_settings('_skin')); //->get_current_skin()->get_id();

        $this->start_controls_section(
            'section_list', [
                'label' => '<i class="eaddicon eadd-skin-list"></i> ' . __('List', 'e-addons'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'type',
            [
                'label' => __('Ordered or Unordered', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'ul' => [
                        'title' => __('Unordered', 'e-addons'),
                        'icon' => 'eicon-editor-list-ul',
                    ],
                    'ol' => [
                        'title' => __('Ordered', 'e-addons'),
                        'icon' => 'eicon-editor-list-ol',
                    ],
                ],
                'default' => 'ul',
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_controls() {
        parent::register_style_controls();

        $this->start_controls_section(
            'section_style_table',
            [
                'label' => '<i class="eaddicon eadd-skin-list"></i> ' . __('List', 'e-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
                'list_wrapper', [
                    'label' => __('List Wrapper', 'e-addons'),
                    'type' => Controls_Manager::HEADING,
                ]
            );
        $this->add_responsive_control(
                    'oul_padding', [
                'label' => __('Padding', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} ul, {{WRAPPER}} ol' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                    ]
            );
        $this->add_responsive_control(
                    'oul_margin', [
                'label' => __('Margin', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} ul, {{WRAPPER}} ol' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],                        
                    ]
            );
        
        $this->add_control(
                    'list_item', [
                'label' => __('List Item', 'e-addons'),
                'type' => Controls_Manager::HEADING,                        
                'separator' => 'before',
                    ]
            );
        $this->add_responsive_control(
                    'li_padding', [
                'label' => __('Padding', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                    ]
            );
        $this->add_responsive_control(
                    'li_margin', [
                'label' => __('Margin', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],                        
                    ]
            );
        
        $this->add_control(
                'li_color', [
            'label' => __('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} li' => 'color: {{VALUE}};'
            ],
                ]
        );
        $this->add_control(
                'li_bgcolor', [
            'label' => __('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} li' => 'background-color: {{VALUE}};'
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'li_border',
            'selector' => '{{WRAPPER}} li',
                ]
        );
        
        $this->add_control(
                'style_type_ul',
                [
                    'label' => __('Style Type', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => __('Default', 'e-addons'),
                        'none' => __('None', 'e-addons'),
                        'circle' => __('Circle', 'e-addons'),
                        'disc' => __('Disc', 'e-addons'),
                        'square' => __('Square', 'e-addons'),
                    ],
                    'selectors' => [                       
                        '{{WRAPPER}} li' => 'list-style: {{VALUE}}',
                    ],                    
                    'condition' => [
                        'list_type' => 'ul',
                    ],
                ]
        );
        $this->add_control(
                'style_type_ol',
                [
                    'label' => __('Style Type', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => __('Default', 'e-addons'),
                        'none' => __('None', 'e-addons'),
                        'armenian' => __('Armenian', 'e-addons'),
                        'cjk-ideographic' => __('Cjk-ideographic', 'e-addons'),
                        'decimal' => __('decimal', 'e-addons'),
                        'decimal-leading-zero' => __('decimal-leading-zero', 'e-addons'),
                        'georgian' => __('georgian', 'e-addons'),
                        'decimal' => __('decimal', 'e-addons'),
                        'hebrew' => __('hebrew', 'e-addons'),
                        'hiragana' => __('hiragana', 'e-addons'),
                        'hiragana-iroha' => __('hiragana-iroha', 'e-addons'),
                        'katakana' => __('katakana', 'e-addons'),
                        'katakana-iroha' => __('katakana-iroha', 'e-addons'),
                        'lower-alpha' => __('lower-alpha', 'e-addons'),
                        'lower-greek' => __('lower-greek', 'e-addons'),
                        'lower-latin' => __('lower-latin', 'e-addons'),
                        'lower-roman' => __('lower-roman', 'e-addons'),
                        'upper-alpha' => __('upper-alpha', 'e-addons'),
                        'upper-greek' => __('upper-greek', 'e-addons'),
                        'upper-latin' => __('upper-latin', 'e-addons'),
                        'upper-roman' => __('upper-roman', 'e-addons'),
                    ],
                    'selectors' => [                       
                        '{{WRAPPER}} li' => 'list-style: {{VALUE}}',
                    ],
                    'condition' => [
                        'list_type' => 'ol',
                    ],
                ]
        );

        $this->end_controls_section();
    }

    protected function render_element_item() {
        
        $this->index++;

        $this->render_item_start();

        $this->render_items();

        $this->render_item_end();

        $this->counter++;
    }

    public function get_container_class() {
        return 'e-add-skin-' . $this->get_id();
    }

    protected function render_loop_start() {
        $this->parent->add_render_attribute('eaddposts_container', [
            'class' => [
                'e-list',
                'e-add-posts-container',
                'e-add-posts',
                'e-add-posts-wrapper',
                $this->get_scrollreveal_class(), //@p prevedo le classi per generare il reveal,
                $this->get_container_class(), //@p una classe personalizzata per lo skin
            ],
        ]);
        $tag = $this->parent->get_settings('list_type');
        echo '<'.$tag.' ' . $this->parent->get_render_attribute_string('eaddposts_container') . '>';
    }

    protected function render_loop_end() {
        $tag = $this->parent->get_settings('list_type');
        echo '</'.$tag.'>';
    }

    public function render_item_start($key = 'post') {
        //@p data post ID
        $data_post_id = ' data-e-add-id="' . $this->current_id . '"';
        //@p data post INDEX
        $data_post_index = ' data-e-add-index="' . $this->counter . '"';
        //@p una classe personalizzata per lo skin
        $item_class = ' ' . $this->get_item_class();
        ?>
        <li<?php
        echo ' class="e-add-item e-add-post-item-' . $this->parent->get_id().' e-add-item-' . $this->parent->get_id() . $item_class . '"';
        //post_class(['e-add-post e-add-post-item e-add-post-item-' . $this->parent->get_id() . $item_class]);
        echo $data_post_id . $data_post_index;
        ?>><?php
        }

        public function render_item_end() {
            echo '</li>';
        }

    }
    