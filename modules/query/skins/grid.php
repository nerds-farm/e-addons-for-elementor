<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Controls_Manager;
use EAddonsForElementor\Modules\Query\Skins\Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Grid Skin
 *
 * Elementor widget query-posts for e-addons
 *
 */
class Grid extends Base {

    public function _register_controls_actions() {

        parent::_register_controls_actions();
        add_action('elementor/element/' . $this->parent->get_name() . '/section_e_query/after_section_end', [$this, 'register_additional_grid_controls'], 20);
        add_action('elementor/element/' . $this->parent->get_name() . '/section_items/before_section_start', [$this, 'register_reveal_controls'], 20);
    }

    public function get_script_depends() {
        return ['imagesloaded', 'jquery-masonry', 'e-addons-query-grid'];
    }

    public function get_style_depends() {
        return ['font-awesome','e-addons-common-query', 'e-addons-query-grid'];
    }

    public function get_id() {
        return 'grid';
    }

    public function get_title() {
        return __('Grid', 'e-addons');
    }

    public function get_docs() {
        return 'https://e-addons.com';
    }

    public function get_icon() {
        return 'eadd-queryviews-grid';
    }

    public function register_additional_grid_controls() {
        //var_dump($this->get_id());
        //var_dump($this->parent->get_settings('_skin')); //->get_current_skin()->get_id();

        $this->start_controls_section(
                'section_grid', [
            'label' => '<i class="eaddicon eadd-queryviews-grid"></i> ' . __('Grid', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );
        $this->add_control(
                'grid_type', [
            'label' => __('Type', 'e-addons'),
            'type' => 'ui_selector',
            'toggle' => false,
            'type_selector' => 'icon',
            'columns_grid' => 3,
            'options' => [
                'flex' => [
                    'title' => __('Flex', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'eicon-posts-grid',
                ],
                'masonry' => [
                    'title' => __('Masonry', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'eicon-posts-masonry',
                ],
                /* 'justified' => [
                  'title' => __('Justified','e-addons'),
                  'return_val' => 'val',
                  'icon' => 'eicon-gallery-justified',
                  ], */
                'blog' => [
                    'title' => __('Blog', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'eicon-posts-group',
                ],
            ],
            'default' => 'flex',
            'label_block' => true,
            'frontend_available' => true
                ]
        );
        $this->add_control(
                'blog_template_id',
                [
                    'label' => __('First item Template', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => __('Template Name', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'object_type' => 'elementor_library',
                    'separator' => 'after',
                    'condition' => [
                        $this->get_control_id('grid_type') => ['blog']
                    ],
                ]
        );
        $this->add_responsive_control(
                'column_blog', [
            'label' => __('First item Column', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => '1',
            'tablet_default' => '3',
            'mobile_default' => '1',
            'options' => [
                '1' => '1/1',
                '2' => '1/2',
                '3' => '1/3',
                '1.5' => '2/3',
                '4' => '1/4',
                '1.34' => '3/4',
                '1.67' => '3/5',
                '1.25' => '4/5',
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid-blog .e-add-item-grid:nth-child(1)' => 'width: calc(100% / {{VALUE}}); flex-basis: calc( 100% / {{VALUE}} );',
            ],
            'condition' => [
                $this->get_control_id('grid_type') => ['blog']
            ],
                ]
        );
        $this->add_responsive_control(
                'columns_grid', [
            'label' => __('Columns', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => '5',
            'tablet_default' => '3',
            'mobile_default' => '1',
            'options' => [
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7'
            ],
            'prefix_class' => 'e-add-col%s-',
            //'frontend_available' => true,
            'selectors' => [
                '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid > .e-add-item-grid' => 'width: calc(100% / {{VALUE}}); flex: 0 1 calc( 100% / {{VALUE}} );',
            //'{{WRAPPER}} .e-add-posts-container.e-add-skin-grid ' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
            ]
                ]
        );

        // Width
        $this->add_responsive_control(
                'grid_item_width', [
            'label' => __('Width', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vh'],
            'default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'max' => 800,
                    'min' => 0,
                    'step' => 1,
                ]
            ],
            'condition' => [
                $this->get_control_id('columns_grid') => '1',
                $this->get_control_id('grid_type') => 'flex'
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid' => 'margin: 0 auto; width: {{SIZE}}{{UNIT}};'
            ]
                ]
        );
        // Alternanza sinistra / destra
        $this->add_responsive_control(
                'grid_alternate', [
            'label' => __('Alternate', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vw'],
            'default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'max' => 400,
                    'min' => 0,
                    'step' => 1,
                ]
            ],
            'condition' => [
                $this->get_control_id('columns_grid') => '1',
                $this->get_control_id('grid_type') => 'flex'
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid > .e-add-item-grid:nth-child(even)' => 'margin-right: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid > .e-add-item-grid:nth-child(odd)' => 'margin-left: {{SIZE}}{{UNIT}};',
            ]
                ]
        );

        // *****+ Masonry

        /* $this->add_control(
          'fitrow_enable', [
          'label' => __('Fit Row', 'e-addons'),
          'type' => Controls_Manager::SWITCHER,
          'condition' => [
          $this->get_control_id('grid_type') => ['masonry']
          ],
          ]
          ); */
        // *****+ Flex
        /*
          flex-grow: 0;
          flex-shrink: 1;
          flex-basis: calc(33.3333%)
         */
        $this->add_control(
                'flex_grow', [
            'label' => __('Flex grow', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'label_block' => false,
            'options' => [
                '1' => [
                    'title' => __('1', 'e-addons'),
                    'icon' => 'fa fa-check',
                ],
                '0' => [
                    'title' => __('0', 'e-addons'),
                    'icon' => 'fa fa-ban',
                ]
            ],
            'default' => '0',
            'selectors' => [
                '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid > .e-add-item-grid' => 'flex-grow: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('grid_type!') => ['masonry']
            ],
                ]
        );
        $this->add_control(
                'heading_grid_alignments', [
            'label' => __('Grid alignments', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('grid_type!') => ['masonry']
            ],
                ]
        );
        
        /*
        $this->add_responsive_control(
                'h_pos_postitems', [
            'label' => '<i class="fas fa-arrows-alt-h"></i>&nbsp;' . __('Horizontal position', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => [
                '' => 'Default',
                'flex-start' => 'Left',
                'center' => 'Center',
                'flex-end' => 'Right',
                'space-between' => 'Space Between',
                'space-around' => 'Space Around',
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid .e-add-wrapper-grid' => 'justify-content: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('flex_grow') => '0',
                $this->get_control_id('grid_type!') => ['masonry']
            ],
                ]
        );*/
        $this->add_control(
            'h_pos_postitems', [
                'label' => '<i class="fas fa-arrows-alt-h"></i>&nbsp;' . __('Horizontal position', 'e-addons'),
                'type' => 'ui_selector',
                'toggle' => false,
                'type_selector' => 'image',
                'label_block' => true,
                'columns_grid' => 5,
                'options' => [
                    /*'' => [
                        'title' => __('Default','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_default.svg',
                    ],*/
                    '' => [
                        'title' => __('Left','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_left.svg',
                    ],
                    'center' => [
                        'title' => __('Middle','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_middle.svg',
                    ],
                    'flex-end' => [
                        'title' => __('Right','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_right.svg',
                    ],
                    'space-between' => [
                        'title' => __('Space Between','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_space-between.svg',
                    ],
                    'space-around' => [
                        'title' => __('Space Around','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_space-around.svg',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    $this->get_control_id('flex_grow') => '0',
                    $this->get_control_id('grid_type!') => ['masonry']
                ],
            ]
        );
       /*
        $this->add_responsive_control(
                'v_pos_postitems', [
            'label' => '<i class="fas fa-arrows-alt-v"></i>&nbsp;' . __('Vertical position', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => [
                '' => 'Default',
                'flex-start' => 'Top',
                'center' => 'Middle',
                'flex-end' => 'Down',
                'stretch' => 'Stretch',
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid .e-add-wrapper-grid' => 'align-items: {{VALUE}};',
                '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid .e-add-item-area' => 'justify-content: {{VALUE}};'
            ],
            'condition' => [
                //$this->get_control_id('flex_grow') => '0',
                $this->get_control_id('grid_type!') => ['masonry'],
            //$this->get_control_id('style_items!') => ['float'],
            ],
                ]
        );
        // *****+ Justified: Height, end coplete
        // *****+ Blog: ..... "da valutere"
        */
        $this->add_control(
            'v_pos_postitems', [
                'label' => '<i class="fas fa-arrows-alt-v"></i>&nbsp;' . __('Vertical position', 'e-addons'),
                'type' => 'ui_selector',
                'toggle' => false,
                'label_block' => true,
                'type_selector' => 'image',
                'columns_grid' => 5,
                'options' => [
                    '' => [
                        'title' => __('Default','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_default.svg',
                    ],
                    'flex-start' => [
                        'title' => __('Top','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_top.svg',
                    ],
                    'center' => [
                        'title' => __('Center','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_center.svg',
                    ],
                    'flex-end' => [
                        'title' => __('Bottom','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_bottom.svg',
                    ],
                    'stretch' => [
                        'title' => __('stretch','e-addons'),
                        'return_val' => 'val',
                        'image' => E_ADDONS_URL.'modules/query/assets/img/grid_alignments/block_stretch.svg',
                    ]
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid' => 'align-items: {{VALUE}};',
                    '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid .e-add-item-area' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid > .e-add-item-grid > .e-add-post-block > .elementor > .elementor-section-wrap' => 'display: flex; height: 100%;',
                    '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid > .e-add-item-grid > .e-add-post-block > .elementor > .elementor-section-wrap > .elementor-section' => 'align-self: {{VALUE}};',
                ],
                'condition' => [
                    //$this->get_control_id('flex_grow') => '0',
                    $this->get_control_id('grid_type!') => ['masonry'],
                //$this->get_control_id('style_items!') => ['float'],
                ]
            ]
        );
        $this->end_controls_section();
    }

    protected function register_style_controls() {
        parent::register_style_controls();

        $this->start_controls_section(
                'section_style_grid',
                [
                    'label' => __('Grid', 'e-addons'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_responsive_control(
                'column_gap',
                [
                    'label' => '<i class="fas fa-arrows-alt-h"></i>&nbsp;' . __('Columns Gap', 'e-addons'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 30,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        //'{{WRAPPER}} .e-add-posts-container' => 'column-gap: {{SIZE}}{{UNIT}}',
                        '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid > .e-add-item-grid' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
                        '{{WRAPPER}} .e-add-posts-container.e-add-skin-grid > .e-add-wrapper-grid' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
                    ],
                ]
        );

        $this->add_responsive_control(
                'row_gap',
                [
                    'label' => '<i class="fas fa-arrows-alt-v"></i>&nbsp;' . __('Rows Gap', 'e-addons'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 35,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        //'{{WRAPPER}} .e-add-post-item' => 'row-gap: {{SIZE}}{{UNIT}}',
                        '{{WRAPPER}} .e-add-post-item' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        $this->end_controls_section();
    }

    protected function render_element_item() {
        
        $this->index++;
        
        $style_items = $this->parent->get_settings_for_display('style_items');
        $blog_template_id = $this->get_instance_value('blog_template_id');
        $grid_type = $this->get_instance_value('grid_type');

        $this->render_item_start();

        if ($this->counter == 0 && $blog_template_id && $grid_type == 'blog') {
            $this->render_template($blog_template_id);
        } else {
            if ($style_items == 'template') {
                $this->render_template();
            } else {
                $this->render_items();
            }
        }
        $this->render_item_end();

        $this->counter++;
    }

    public function get_container_class() {
        return 'e-add-skin-' . $this->get_id() . ' e-add-skin-' . $this->get_id() . '-' . $this->get_instance_value('grid_type');
    }

    public function get_scrollreveal_class() {
        if ($this->get_instance_value('scrollreveal_effect_type'))
            return 'reveal-effect reveal-effect-' . $this->get_instance_value('scrollreveal_effect_type');
    }

    /* public function render() {

      echo 'is:'.$this->get_id().' skin:'.$this->parent->get_settings('_skin');
      var_dump($this->parent->get_script_depends());
      } */
}
