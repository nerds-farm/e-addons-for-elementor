<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use EAddonsForElementor\Modules\Query\Skins\Base;
use EAddonsForElementor\Modules\Query\Skins\Carousel;
use EAddonsForElementor\Modules\Query\Base\Query as Base_Query;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Dualslider extends Carousel {

    public function _register_controls_actions() {
        parent::_register_controls_actions();
        add_action('elementor/element/' . $this->parent->get_name() . '/section_e_query/after_section_end', [$this, 'register_additional_dualslider_controls'], 20);                
    }

    public function get_script_depends() {
        if (!wp_script_is('swiper', 'registered')) {
            // fix improved_assets_loading
            wp_register_script(
                    'swiper',
                    //$frontend->get_js_assets_url( 'swiper', 'assets/lib/swiper/' ),
                    ELEMENTOR_ASSETS_URL .'lib/swiper/swiper.min.js',
                    [],
                    '5.3.6',
                    true
            );
        }
        return ['imagesloaded', 'swiper', 'jquery-swiper', 'e-addons-query-carousel', 'e-addons-query-dualslider'];
    }

    public function get_style_depends() {
        return ['custom-swiper', 'e-addons-common-query', 'e-addons-query-grid', 'e-addons-query-carousel', 'e-addons-query-dualslider'];
    }

    public function get_id() {
        return 'dualslider';
    }

    public function get_title() {
        return __('Dual Slider', 'e-addons');
    }

    public function get_docs() {
        return 'https://e-addons.com';
    }

    public function get_icon() {
        return 'eadd-queryviews-dualslider';
    }

    public function register_additional_dualslider_controls() {

        $this->start_controls_section(
                'section_dualslider', [
            'label' => '<i class="eaddicon eadd-queryviews-dualslider"></i> ' . __('Dual Slider', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );
        $this->add_responsive_control(
                'dualslider_style', [
            'label' => __('Position Style', 'e-addons'),
            'type' => 'ui_selector',
            'label_block' => true,
            'toggle' => false,
            'type_selector' => 'image',
            'columns_grid' => 4,
            'options' => [
                'column' => [
                    'title' => __('Bottom', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/dualslider/dualslider_b.png',
                ],
                'column-reverse' => [
                    'title' => __('Top', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/dualslider/dualslider_t.png',
                ],
                'row-reverse' => [
                    'title' => __('Left', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/dualslider/dualslider_l.png',
                ],
                'row' => [
                    'title' => __('Right', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/dualslider/dualslider_r.png',
                ],
            ],
            'toggle' => false,
            'render_type' => 'template',
            'default' => 'column',
            //'tablet_default' => '',
            //'mobile_default' => 'bottom',
            'prefix_class' => 'e-add-style-dualslider-position-', //'e-add-align%s-',
            'separator' => 'before',
            'frontend_available' => true,
            'selectors' => [
                '{{WRAPPER}} .e-add-style-position-dualslider' => 'flex-direction: {{VALUE}};',
            ],
                ]
        );
        $this->add_responsive_control(
                'dualslider_distribution_vertical', [
            'label' => __('Distribution', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => '%',
            ],
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 10,
                    'max' => 60,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}}.e-add-style-dualslider-position-row .e-add-dualslider-thumbnails, {{WRAPPER}}.e-add-style-dualslider-position-row-reverse .e-add-dualslider-thumbnails' => 'width: {{SIZE}}%;',
                '{{WRAPPER}}.e-add-style-dualslider-position-row .e-add-dualslider-posts, {{WRAPPER}}.e-add-style-dualslider-position-row-reverse .e-add-dualslider-posts' => 'width: calc(100% - {{SIZE}}%);'
            ],
            'condition' => [
                $this->get_control_id('dualslider_style') => ['row-reverse', 'row']
            ]
                ]
        );
        $this->add_responsive_control(
                'dualslider_height_container', [
            'label' => __('Viewport Height', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 800,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}.e-add-style-dualslider-position-row .e-add-dualslider-posts .swiper-container, {{WRAPPER}}.e-add-style-dualslider -position-row.e-add-dualslider-thumbnails .swiper-container, {{WRAPPER}}.e-add-style-dualslider-position-row-reverse .e-add-dualslider-posts .swiper-container, {{WRAPPER}}.e-add-style-dualslider-position-row-reverse .e-add-dualslider-thumbnails .swiper-container' => 'height: {{SIZE}}{{UNIT}};'
            ],
            'condition' => [
                $this->get_control_id('dualslider_style') => ['row-reverse', 'row']
            ]
                ]
        );
        // slides per row
        $this->add_responsive_control(
                'thumbnails_slidesPerView', [
            'label' => __('Slides per View', 'e-addons'),
            'description' => __('Number of slides per view (slides visible at the same time on sliders container). If you use it with "auto" value and along with loop: true then you need to specify loopedSlides parameter with amount of slides to loop (duplicate). SlidesPerView: "auto"\'" is currently not compatible with multirow mode, when slidesPerColumn greater than 1', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '4',
            'tablet_default' => '3',
            'mobile_default' => '2',
            'separator' => 'before',
            'min' => 3,
            'max' => 12,
            'step' => 1,
            'frontend_available' => true,
                ]
        );
        // space
        $this->add_responsive_control(
                'dualslider_space', [
            'label' => __('Space', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'tablet_default' => [
                'size' => '',
            ],
            'mobile_default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'max' => 400,
                    'min' => 0,
                    'step' => 1,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}}.e-add-style-dualslider-position-column-reverse .e-add-dualslider-thumbnails' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}}.e-add-style-dualslider-position-column .e-add-dualslider-thumbnails' => 'margin-top: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}}.e-add-style-dualslider-position-row-reverse .e-add-dualslider-thumbnails' => 'margin-right: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}}.e-add-style-dualslider-position-row .e-add-dualslider-thumbnails' => 'margin-left: {{SIZE}}{{UNIT}};'
            ]
                ]
        );

        // gap
        $this->add_responsive_control(
                'dualslider_gap', [
            'label' => __('Gap', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '',
            'tablet_default' => '3',
            'mobile_default' => '2',
            'separator' => 'before',
            'min' => 0,
            'max' => 80,
            'step' => 1,
            'frontend_available' => true
                /* 'selectors' => [
                  '{{WRAPPER}} .e-add-dualslider-gallery-thumbs .swiper-slide' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
                  '{{WRAPPER}} .e-add-dualslider-gallery-thumbs .swiper-wrapper' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
                  ], */
                ]
        );
        // alignment
        /* $this->add_responsive_control(
          'dualslider_align', [
          'label' => __('Alignment', 'e-addons'),
          'type' => Controls_Manager::CHOOSE,
          'toggle' => false,
          'options' => [
          'flex-start' => [
          'title' => __('Left', 'e-addons'),
          'icon' => 'eicon-h-align-left',
          ],
          'center' => [
          'title' => __('Center', 'e-addons'),
          'icon' => 'eicon-h-align-center',
          ],
          'flex-end' => [
          'title' => __('Right', 'e-addons'),
          'icon' => 'eicon-h-align-right',
          ],
          ],
          'default' => 'center',
          'selectors' => [
          '{{WRAPPER}} .e-add-pagination' => 'justify-content: {{VALUE}};',
          ],
          ]
          ); */
        $this->add_responsive_control(
                'dualslider_align', [
            'label' => __('Text Alignment', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'options' => [
                'left' => [
                    'title' => __('Left', 'e-addons'),
                    'icon' => 'fa fa-align-left',
                ],
                'center' => [
                    'title' => __('Center', 'e-addons'),
                    'icon' => 'fa fa-align-center',
                ],
                'right' => [
                    'title' => __('Right', 'e-addons'),
                    'icon' => 'fa fa-align-right',
                ]
            ],
            'default' => 'left',
            'prefix_class' => 'e-add-align%s-',
            'selectors' => [
                //'{{WRAPPER}} .e-add-item:not(.e-add-item_author)' => 'text-align: {{VALUE}};',
                '{{WRAPPER}} .e-add-dualslider-gallery-thumbs .swiper-slide' => 'text-align: {{VALUE}};',
            ],
            'separator' => 'before',
                ]
        );

        // gap
        // style: top, overflow,
        // -----STATUS
        $this->add_control(
                'dualslider_heading_status',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="far fa-star"></i>&nbsp;&nbsp;' . __('Status', 'e-addons'),
                    'label_block' => false,
                    'content_classes' => 'e-add-icon-heading',
                ]
        );
        $this->start_controls_tabs('dualslider_status');

        $this->start_controls_tab('tab_dualslider_normal', [
            'label' => __('Normal', 'e-addons'),
        ]);
        $this->add_control(
                'dualslider_item_opacity', [
            'label' => __('Normal Opacity', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-dualslider-thumbnails .e-add-dualslider-gallery-thumbs .swiper-slide:not(.swiper-slide-thumb-active) .e-add-dualslider-wrap' => 'opacity: {{SIZE}};',
            ],
                ]
        );
        // background text color
        $this->add_control(
                'dualslider_title_background', [
            'label' => __('Normal Title background', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-dualslider-thumbnails .e-add-dualslider-gallery-thumbs .swiper-slide:not(.swiper-slide-thumb-active) .e-add-dualslider-wrap' => 'color: {{VALUE}};'
            ],
            'condition' => [
                $this->get_control_id('use_title') => 'yes',
            ]
                ]
        );
        // Image background of overlay
        $this->add_group_control(
                Group_Control_Background::get_type(), [
            'name' => 'dualslider_image_background',
            'label' => __('Normal Image Overlay', 'e-addons'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .e-add-dualslider-gallery-thumbs .swiper-slide:not(.swiper-slide-thumb-active) .e-add-thumbnail-image:after',
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab('tab_dualslider_active', [
            'label' => __('Active', 'e-addons'),
        ]);
        $this->add_control(
                'dualslider_itemactive_opacity', [
            'label' => __('Active Opacity', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-dualslider-thumbnails .e-add-dualslider-gallery-thumbs .swiper-slide-thumb-active .e-add-dualslider-wrap' => 'opacity: {{SIZE}};',
            ],
                ]
        );
        // background text color
        $this->add_control(
                'dualslider_titleactive_background', [
            'label' => __('Active Title background', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-dualslider-thumbnails .e-add-dualslider-gallery-thumbs .swiper-slide-thumb-active .e-add-dualslider-wrap' => 'color: {{VALUE}};'
            ],
            'condition' => [
                $this->get_control_id('use_title') => 'yes',
            ]
                ]
        );
        // Image background of Overlay
        
        $this->add_group_control(
                Group_Control_Background::get_type(), [
            'name' => 'dualslider_imageactive_background',
            'label' => __('Active Image Overlay', 'e-addons'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .e-add-dualslider-gallery-thumbs .swiper-slide-thumb-active .e-add-thumbnail-image:after',
                ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();



        // ------------ Title
        $this->add_control(
                'dualslider_heading_title',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-heading"></i>&nbsp;&nbsp;' . __('Title', 'e-addons'),
                    'label_block' => false,
                    'content_classes' => 'e-add-icon-heading',
                ]
        );
        $this->add_control(
                'use_title', [
            'label' => __('Show Title', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
                ]
        );
        // color
        $this->add_control(
                'dualslider_title_color', [
            'label' => __('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-thumbnail-title' => 'color: {{VALUE}};'
            ],
            'condition' => [
                $this->get_control_id('use_title') => 'yes',
            ]
                ]
        );

        // typography
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'dualslider_title_typography',
            'label' => __('Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} .e-add-thumbnail-title',
            'condition' => [
                $this->get_control_id('use_title') => 'yes',
            ]
                ]
        );
        // padding
        $this->add_control(
                'dualslider_text_padding', [
            'label' => __('Text Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .e-add-dualslider-thumbnails .e-add-thumbnail-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('use_title') => 'yes',
            ]
                ]
        );
        
        
        
        // 
        // ------------ Image
        $this->add_control(
                'dualslider_heading_image',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="far fa-image"></i>&nbsp;&nbsp;' . __('Image', 'e-addons'),
                    'label_block' => false,
                    'content_classes' => 'e-add-icon-heading',
                ]
        );
        $this->add_control(
                'use_image', [
            'label' => __('Show Image', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
                ]
        );
        $type = $this->parent->get_querytype();

        if ($type == 'post' || $type == 'user') {
            //@p questa è solo l'etichetta string
            if ($type == 'post') {
                $defIm = 'featured';
            } else if ($type == 'user') {
                $defIm = 'avatar';
            }
            
            $this->add_control(
                'image_type', [
                    'label' => __('Image type', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'featuredimage' => __(ucfirst($defIm . ' image'), 'e-addons'),
                        'customimage' => __('Custom meta image', 'e-addons'),
                    ],
                    'default' => $defIm . 'image',
                    'condition' => [
                        $this->get_control_id('use_image') => 'yes',
                    ]
                ]
            );

            $this->add_control(
                    'image_custom_metafield', [
                'label' => __('Image Meta Field', 'e-addons'),
                'type' => 'e-query',
                'placeholder' => __('Meta key', 'e-addons'),
                'label_block' => true,
                'query_type' => 'metas',
                'object_type' => $type,
                'separator' => 'after',
                'condition' => [
                    $this->get_control_id('use_image') => 'yes',
                    $this->get_control_id('image_type') => 'customimage',
                ]
                    ]
            );
        } else if ($type == 'term') {
            //@p altrimeti in termine è solo la custom
            $this->add_control(
                    'image_custom_metafield', [
                'label' => __('Image Meta Field', 'e-addons'),
                'type' => 'e-query',
                'placeholder' => __('Meta key', 'e-addons'),
                'label_block' => true,
                'query_type' => 'metas',
                'object_type' => $type,
                'separator' => 'after',
                'condition' => [
                    $this->get_control_id('use_image') => 'yes',
                ]
                    ]
            );
        }
        // size
        $this->add_group_control(
                Group_Control_Image_Size::get_type(), [
            'name' => 'thumbnailimage_size',
            'label' => __('Image Format', 'e-addons'),
            'default' => 'medium',
            'condition' => [
                $this->get_control_id('use_image') => 'yes',
            ]
                ]
        );
        $this->add_control(
            'use_bgimage', [
        'label' => __('Background Mode', 'e-addons'),
        'type' => Controls_Manager::SWITCHER,
        'separator' => 'before',
        'default' => 'yes',
        'render_type' => 'template',
            ]
        );
        // height
        $this->add_responsive_control(
                'dualslider_image_height', [
            'label' => __('Height', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'tablet_default' => [
                'size' => '',
            ],
            'mobile_default' => [
                'size' => '',
            ],
            'size_units' => ['px', '%', 'em'],
            'range' => [
                'px' => [
                    'max' => 400,
                    'min' => 0,
                    'step' => 1,
                ],
                '%' => [
                    'max' => 100,
                    'min' => 0,
                    'step' => 1,
                ],
                'em' => [
                    'max' => 10,
                    'min' => 0,
                    'step' => 1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-dualslider-thumbnails .e-add-bgimage' => 'height: {{SIZE}}{{UNIT}};'
            ],
            'condition' => [
                $this->get_control_id('use_image') => 'yes',
                $this->get_control_id('use_bgimage') => 'yes',
            ],
            'frontend_available' => true
                ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name' => 'dualslider_image_border',
                'selector' => '{{WRAPPER}} .e-add-dualslider-thumbnails .e-add-thumbnail-image',
                'condition' => [
                    $this->get_control_id('use_image') => 'yes',
                ]
            ]
        );
        $this->add_control(
            'dualslider_image_border_radius', [
                'label' => __('Border Radius', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .e-add-dualslider-thumbnails .e-add-thumbnail-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    $this->get_control_id('use_image') => 'yes',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name' => 'dualslider_image_boxshadow',
                'selector' => '{{WRAPPER}} .e-add-dualslider-thumbnails .e-add-thumbnail-image',
                'condition' => [
                    $this->get_control_id('use_image') => 'yes',
                ]
            ]
        );
        // space
        // filters
        // overlay
        // ----------- Rollhover
        // filters
        // overlay
        // zoom

        $this->end_controls_section();
    }

    public function render() {
        if (!$this->parent) {
            return;
        }

        $this->parent->render();

        $settings = $this->parent->get_settings_for_display('list_items');
        // @p [apro] il wrapper che defifinisce la direction style del dualslider
        echo '<div class="e-add-style-position-' . $this->get_id() . '">';

        echo '<div class="e-add-dualslider-posts">';
        parent::render();
        echo '</div>';

        /** @p elaboro la query... */
        //$this->parent->query_the_elements();

        /** @p qui prendo il valore di $query elaborato in base > query.php */
        $query = $this->parent->get_query();
        $querytype = $this->parent->get_querytype();
        //var_dump($query);
        
        //@p MMMMM se esistono sia immagine che titolo uso una classe: xxxxx per getire gli allineamenti flex
        $multip = '';
        if ($this->get_instance_value('use_title') && $this->get_instance_value('use_image')) {
            $multip = ' e-add-dualslider-multi';
        }
        //@p controllo se ci sono 
        echo '<div class="e-add-dualslider-thumbnails">';
        
        
        
        
        echo '	<div class="swiper-container e-add-dualslider-gallery-thumbs">'; //@p this is the target 
        echo '		<div class="swiper-wrapper e-add-dualslider-wrapper' . $multip . '">';
        

        /*switch ($querytype) {
            case 'attachment':
            case 'post':
            case 'term':
            case 'items':
            case 'repeater':
                $this->render_item_image($settings, $i = 0);
                break;
            case 'users':
                $this->render_item_avatar($settings);
                break;
            }
        }*/
        
        if ($this->has_results($query, $querytype)) {

            switch ($querytype) {
                case 'attachment':
                case 'post':

                /** @p qui identifico se mi trovo in un loop, altrimenti uso la wp_query */
                if ($query->in_the_loop) {
                    $this->current_permalink = get_permalink();
                    $this->current_id = get_the_ID();
                    //
                    $this->render_thumbnail();
                } else {
                    while ($query->have_posts()) {
                        $query->the_post();

                        $this->current_permalink = get_permalink();
                        $this->current_id = get_the_ID();
                        $this->current_data = get_post(get_the_ID());
                        //
                        $this->render_thumbnail();
                    }
                }
                wp_reset_postdata();

                break;
                case 'user':
                    foreach ($query->get_results() as $user) {
                        $this->current_permalink = get_author_posts_url($user->ID);
                        $this->current_id = $user->ID;
                        $this->current_data = $user;
                        //var_dump($this->current_id);
                        //
                        $this->render_thumbnail(true);
                    }
                    break;
                case 'term':
                
                    break;
                case 'repeater':

                    break;
                case 'items':

                    break;
            }

        }

        
        





        echo '</div>'; // @p END: swiper-container
        echo '</div>'; // @p END: swiper-wrapper
        
        // @p le freccine di navigazione
        echo '<div class="e-add-dualslider-controls  e-add-dualslider-controls-' . $this->get_instance_value('dualslider_style') . '" data-post-id="' . $this->current_id . '">';
        $this->render_thumb_navigation();
        echo '</div>';

        echo '</div>'; // @p END: e-add-dualslider-thumbnails

        echo '</div>'; // @p [chiudo] il wrapper che defifinisce la direction style del dualslider
    }

    protected function render_thumb_navigation() {

        $arrow_1 = 'left';
        $arrow_2 = 'right';
        if ($this->get_instance_value('dualslider_style') == 'row-reverse' || $this->get_instance_value('dualslider_style') == 'row') {
            $arrow_1 = 'up';
            $arrow_2 = 'down';
        }
        //if ( $this->get_instance_value('useNavigation') ) {
        echo '<div class="swiper-button-prev prev-' . $this->parent->get_id() . '-' . $this->current_id . '"><i class="fa fas fa-chevron-' . $arrow_1 . '"></i></div>';
        echo '<div class="swiper-button-next next-' . $this->parent->get_id() . '-' . $this->current_id . '"><i class="fa fas fa-chevron-' . $arrow_2 . '"></i></div>';
        //}
    }

    public function render_thumbnail($is_user = false) {

        echo '<div class="swiper-slide e-add-dualslider-item no-transitio">';
        echo '<div class="e-add-dualslider-wrap">';
        if ($this->get_instance_value('use_image')){
            if($is_user){
                $this->render_thumb_avatar();
            }else{
                $this->render_thumb_image();
            }
        }    
        if ($this->get_instance_value('use_title')){
            if($is_user){
                $this->render_thumb_usermeta();  
            }else{
                 $this->render_thumb_title();
            }
        }  
        
        
       

        echo '</div>';
        echo '</div>';
    }
    protected function render_thumb_usermeta() {
        $user_info = $this->current_data;
        // Settings ------------------------------
        $html_tag = 'h3'; //['html_tag'];
        // ---------------------------------------

        echo sprintf('<%1$s class="e-add-thumbnail-title">', $html_tag);
?>
        <?php echo $user_info->display_name; ?>
        <?php

        echo sprintf('</%s>', $html_tag);
        ?>
        <?php

    }
    protected function render_thumb_title() {
        // Settings ------------------------------
        $html_tag = 'h3'; //['html_tag'];
        // ---------------------------------------

        echo sprintf('<%1$s class="e-add-thumbnail-title">', $html_tag);
?>
        <?php get_the_title() ? the_title() : the_ID(); ?>
        <?php

        echo sprintf('</%s>', $html_tag);
        ?>
        <?php

    }
    protected function render_thumb_avatar(){
        $user_info = $this->current_data;
        $avatarsize = $this->get_instance_value('thumbnailimage_size_size');
        $querytype = $this->parent->get_querytype();
        $use_bgimage = $this->get_instance_value('use_bgimage');
        
        if (!empty($this->get_instance_value('image_custom_metafield'))) {
            $meta_value = get_metadata($querytype, $this->current_id, $this->get_instance_value('image_custom_metafield'), true);
            //
            $img_url = wp_get_attachment_image_src($meta_value, $avatarsize, false);
            $avatar_url = $img_url[0];
            $avatar_html = wp_get_attachment_image($meta_value, $avatarsize, false);
        }

        if(empty($avatar_url)){
            $avatar_url = get_avatar_url($user_info->user_email, $avatarsize);
        }
        if(empty($avatar_html)){
            // @p questa è l'mmagine avatar HTML
            $avatar_html = get_avatar($user_info->user_email, $avatarsize);
        }

        echo '<div class="e-add-thumbnail-image">';



        if ($use_bgimage) {
            echo '<figure class="e-add-img e-add-bgimage" style="background: url(' . $avatar_url . ') no-repeat center; background-size: cover; display: block;"></figure>';
        } else {
            echo '<figure class="e-add-img">' . $avatar_html . '</figure>';
        }
        
        echo '</div>';
        
    }
    protected function render_thumb_image() {
        
        $setting_key = $this->get_instance_value('thumbnailimage_size_size');
        $querytype = $this->parent->get_querytype();
        $use_bgimage = $this->get_instance_value('use_bgimage');
        
        $image_id = false;
        if (!empty($this->get_instance_value('image_custom_metafield'))) {
            $meta_value = get_metadata($querytype, $this->current_id, $this->get_instance_value('image_custom_metafield'), true);
            $image_id = $meta_value;
        } else {

            switch ($querytype) {
                case 'attachment':
                    //se mi trovo in media basta l'id dell'attachment
                    $image_id = get_the_ID();
                    break;
                case 'post':
                    //se mi trovo in post
                    $image_id = get_post_thumbnail_id();
                    break;
                case 'term':
                    //se mi trovo in term (nativamente non ho immagine )
                    $image_id = ''; //$meta_value;
                    break;
                case 'items':
                    //se mi trovo in item_list
                    $image_id = $this->current_data['sl_image']['id'];
                    break;
                case 'repeater':
                    //se mi trovo in repeater ...... 
                    if (!empty($this->current_data['item_image_' . $i])) {
                        $image_id = $this->current_data['item_image_' . $i];
                    }
                    break;
            }
        }

        





        if ($image_id) {
            $image_attr = [
                'class' => $this->get_image_class()
            ];

            $image_url = wp_get_attachment_image_src($image_id, $setting_key, true);
            $image_html = wp_get_attachment_image($image_id, $setting_key, true, $image_attr);

            echo '<div class="e-add-thumbnail-image">';

            if ($use_bgimage) {
                echo '<figure class="e-add-img e-add-bgimage" style="background: url(' . $image_url[0] . ') no-repeat center; background-size: cover; display: block;"></figure>';
            } else {
                echo '<figure class="e-add-img">' . $image_html . '</figure>';
            }

            echo '</div>';
        }
    }

    // Classes ----------
    public function get_container_class() {
        return 'swiper-container e-add-skin-' . $this->get_id() . ' e-add-skin-' . parent::get_id();
    }

    public function get_wrapper_class() {
        return 'swiper-wrapper e-add-wrapper-' . $this->get_id() . ' e-add-wrapper-' . parent::get_id();
    }

    public function get_item_class() {
        return 'swiper-slide e-add-item-' . $this->get_id() . ' e-add-item-' . parent::get_id();
    }

}
