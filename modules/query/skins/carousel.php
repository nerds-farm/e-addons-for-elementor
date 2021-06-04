<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use EAddonsForElementor\Modules\Query\Skins\Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Carousel extends Base {

    public function _register_controls_actions() {
        parent::_register_controls_actions();
        add_action('elementor/element/' . $this->parent->get_name() . '/section_e_query/after_section_end', [$this, 'register_additional_carousel_controls'], 20);
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
        return ['imagesloaded', 'swiper', 'jquery-swiper', 'e-addons-query-carousel'];
    }

    public function get_style_depends() {
        return ['custom-swiper', 'e-addons-common-query', 'e-addons-query-grid', 'e-addons-query-carousel'];
    }

    public function get_id() {
        return 'carousel';
    }

    public function get_title() {
        return __('Carousel', 'e-addons');
    }

    public function get_docs() {
        return 'https://e-addons.com';
    }

    public function get_icon() {
        return 'eadd-queryviews-carousel';
    }

    public function register_additional_carousel_controls() {

        $this->start_controls_section(
                'section_carousel', [
            'label' => '<i class="eaddicon eadd-queryviews-carousel"></i> ' . __('Carousel', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control(
                'speed_slider', [
            'label' => __('Speed (ms)', 'e-addons'),
            'description' => __('Duration of transition between slides (in ms)', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 300,
            'min' => 0,
            'max' => 3000,
            'step' => 10,
            'frontend_available' => true
                ]
        );
        /*$this->add_control(
                'effects', [
            'label' => __('Transition Effect', 'e-addons'),
            'description' => __('Transition effect from the slides.', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'slide' => __('Slide', 'e-addons'),
                'fade' => __('Fade', 'e-addons'),
                'cube' => __('Cube', 'e-addons'),
                'coverflow' => __('Coverflow', 'e-addons'),
                'flip' => __('Flip', 'e-addons'),
            ],
            'default' => 'slide',
            'render_type' => 'template',
            'frontend_available' => true,
            'prefix_class' => 'e-add-carousel-effect-'
                ]
        );*/

        // ------------------------------------
        $this->add_control(
            'effects', [
            'label' => __('Transition Effects', 'e-addons'),
            'type' => 'ui_selector',
            'label_block' => true,
            'toggle' => false,
            'type_selector' => 'image',
            'columns_grid' => 5,
            'separator' => 'before',
            'options' => [
                'slide' => [
                    'title' => __('Slide','e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL.'modules/query/assets/img/slider_effects/slide.svg',
                ],
                'fade' => [
                    'title' => __('Fade','e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL.'modules/query/assets/img/slider_effects/alpha.svg',
                ],
                'cube' => [
                    'title' => __('Cube','e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL.'modules/query/assets/img/slider_effects/cube.svg',
                ],
                'coverflow' => [
                    'title' => __('Coverflow','e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL.'modules/query/assets/img/slider_effects/coverflow.svg',
                ],
                'flip' => [
                    'title' => __('Flip','e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL.'modules/query/assets/img/slider_effects/flip.svg',
                ]
            ],
            'default' => 'slide',
            'render_type' => 'template',
            'frontend_available' => true,
            'prefix_class' => 'e-add-carousel-effect-'
              //'tablet_default' => '',
              //'mobile_default' => '',
              
            ]
        );
        

        // ------- slideShadows (true) ------
        $this->add_control(
            'effect_cube_heading', [
                'label' => __('Cube options', 'e-addons'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    $this->get_control_id('effects') => ['cube']
                ]
            ]
        );
        
        // ------- cube shadow (true) ------
        $this->add_control(
                'cube_shadow', [
            'label' => __('Shadow', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => ['cube']
            ]
                ]
        );
        // ------- fade crossFade (false) ------
        $this->add_control(
            'effect_fade_heading', [
                'label' => __('Fade option', 'e-addons'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    $this->get_control_id('effects') => ['fade']
                ]
            ]
        );
        $this->add_control(
                'crossFade', [
            'label' => __('Crossfade', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => ['fade']
            ]
                ]
        );
        // ------- coverflow stretch (0) ------
        $this->add_control(
            'effect_coverflow_heading', [
                'label' => __('Coverflow options', 'e-addons'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    $this->get_control_id('effects') => ['coverflow']
                ]
            ]
        );
        $this->add_control(
                'coverflow_stretch', [
            'label' => __('Coverflow Stretch', 'e-addons'),
            'description' => __('Stretch space between slides (in px)', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '0',
            //'tablet_default' => '',
            //'mobile_default' => '',
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => ['coverflow']
            ]
                ]
        );
        // ------- coverflow modifier (1) ------
        $this->add_control(
                'coverflow_modifier', [
            'label' => __('Coverflow Modifier', 'e-addons'),
            'description' => __('Effect multipler', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '1',
            //'tablet_default' => '',
            //'mobile_default' => '',
            'min' => 0,
            'max' => 2,
            'step' => 0.1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => ['coverflow']
            ]
                ]
        );
        $this->add_control(
            'slideShadows', [
        'label' => __('Slide Shadows', 'e-addons'),
        'type' => Controls_Manager::SWITCHER,
        'default' => 'yes',
        'frontend_available' => true,
        'condition' => [
            $this->get_control_id('effects') => ['cube', 'flip', 'coverflow']
        ]
            ]
        );
        

        // Directions of slider
        $this->add_control(
            'direction_slider', [
                'label' => __('Direction', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'horizontal' => [
                        'title' => __('Horizontal', 'e-addons'),
                        'icon' => 'fas fa-arrows-alt-h',
                    ],
                    'vertical' => [
                        'title' => __('Vertical', 'e-addons'),
                        'icon' => 'fas fa-arrows-alt-v',
                    ]
                ],
                'default' => 'horizontal',
                'prefix_class' => 'e-add-carousel-direction-',
                'frontend_available' => true,
                'render_type' => 'template',
                'separator' => 'before'
            ]
        );
        /*$this->add_control(
            'direction_slider', [
                'label' => __('Direction', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'horizontal' => __('Horizontal', 'e-addons'),
                    'vertical' => __('Vertical', 'e-addons'),
                ],
                'toggle' => false,
                'default' => 'horizontal',
                'prefix_class' => 'e-add-carousel-direction-',
                'frontend_available' => true,
                'render_type' => 'template',
                'separator' => 'before'
            ]
        );*/
        $this->add_responsive_control(
                'height_container', [
            'label' => __('Height of viewport', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh', '%'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 800,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-skin-carousel.swiper-container-vertical' => 'height: {{SIZE}}{{UNIT}};'
            ],
            'condition' => [
                //$this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('direction_slider') => 'vertical'
            ]
                ]
        );


        // ******************************************************************
        // ******************************************************************
        // ******************************************************************

        $this->add_responsive_control(
                'slidesPerView', [
            'label' => __('Slides per View', 'e-addons'),
            'description' => __('Number of slides per view (slides visible at the same time on sliders container). If you use it with "auto" value and along with loop "true" then you need to specify loopedSlides parameter with amount of slides to loop (duplicate). SlidesPerView: "auto"\'" is currently not compatible with multirow mode, when slidesPerColumn greater than 1', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '1',
            //'tablet_default' => '',
            //'mobile_default' => '',
            'separator' => 'before',
            'min' => 1,
            'max' => 12,
            'step' => 1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => ['slide','coverflow'],
            ]
                ]
        );
        $this->add_responsive_control(
                'slidesPerGroup', [
            'label' => __('Slides per Group', 'e-addons'),
            'description' => __('Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'tablet_default' => '',
            'mobile_default' => '',
            'min' => 1,
            'max' => 12,
            'step' => 1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => 'slide',
            ]
                ]
        );
        $this->add_responsive_control(
                'slidesColumn', [
            'label' => __('Slides Column', 'e-addons'),
            'description' => __('Number of slides per column, for multirow layout.', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '1',
            //'tablet_default' => '',
            //'mobile_default' => '',
            'min' => 1,
            'max' => 4,
            'step' => 1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects') => 'slide',
            ]
                ]
        );


        // ******************************************************************
        // ******************************************************************
        // ******************************************************************

        $this->add_control(
                'hr_interface',
                [
                    'type' => Controls_Manager::DIVIDER,
                    'style' => 'thick',
                ]
        );
        $this->start_controls_tabs('carousel_interface');

        // -----Tab navigation
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_navigation', [
            'label' => __('Navigatiion', 'e-addons'),
        ]);

        // --------------- Navigation options ------
        $this->add_control(
                'useNavigation', [
            'label' => __('Use Navigation', 'e-addons'),
            'description' => __('Enable to use the navigation arrows.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'arrows_heading', [
            'label' => __('Arrows', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes'
            ]
                ]
        );
        // --------- Navigations Arrow Options
        $this->add_control(
            'heading_navigation_arrow_color', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' => '<i class="fas fa-tint"></i> ' . __('Color', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'condition' => [
                    $this->get_control_id('useNavigation') => 'yes'
                ]
            ]
        );
        $this->add_control(
                'navigation_arrow_color', [
            'label' => __('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next path, {{WRAPPER}} .swiper-button-prev path' => 'fill: {{VALUE}};',
                '{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next polyline, {{WRAPPER}} .swiper-button-prev polyline' => 'stroke: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'navigation_arrow_color_hover', [
            'label' => __('Hover color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next:hover path, {{WRAPPER}} .swiper-button-prev:hover path' => 'fill: {{VALUE}};',
                '{{WRAPPER}} .swiper-button-next:hover line, {{WRAPPER}} .swiper-button-prev:hover line, {{WRAPPER}} .swiper-button-next:hover polyline, {{WRAPPER}} .swiper-button-prev:hover polyline' => 'stroke: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes'
            ],
                ]
        );
        $this->add_control(
                'navigation_arrow_bgcolor', [
            'label' => __('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'navigation_arrow_bgcolor_hover', [
            'label' => __('Hover Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next:hover, {{WRAPPER}} .swiper-button-prev:hover' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes'
            ],
                ]
        );
        // -------------------- STYLE
        $this->add_control(
            'heading_navigation_transform', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' => '<i class="fas fa-vector-square"></i> <b>' . __('Transform', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'condition' => [
                    $this->get_control_id('useNavigation') => 'yes'
                ]
            ]
        );
        $this->add_control(
                'navigation_transform_popover', [
            'label' => __('Transform', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => __('Default', 'e-addons'),
            'label_on' => __('Custom', 'e-addons'),
            'return_value' => 'yes',
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes'
            ]
                ]
        );
        $this->parent->start_popover();
        $this->add_responsive_control(
                'navigation_stroke_1', [
            'label' => __('Stroke Arrow', 'e-addons'),
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
                    'max' => 50,
                    'min' => 0,
                    'step' => 1.0000,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-width: {{SIZE}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('navigation_transform_popover') => 'yes'
            ]
                ]
        );
        $this->add_responsive_control(
                'navigation_stroke_2', [
            'label' => __('Stroke Line', 'e-addons'),
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
                    'max' => 50,
                    'min' => 0,
                    'step' => 1.0000,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line' => 'stroke-width: {{SIZE}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('navigation_transform_popover') => 'yes'
            ]
                ]
        );

        ////////
        $this->add_control(
                'navigation_dash', [
            'label' => __('Dashed', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '0',
            ],
            'range' => [
                'px' => [
                    'max' => 50,
                    'min' => 0,
                    'step' => 1.0000,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-dasharray: {{SIZE}},{{SIZE}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('navigation_transform_popover') => 'yes'
            ]
                ]
        );
        ///////////
        $this->add_responsive_control(
                'navigation_size', [
            'label' => __('Size', 'e-addons'),
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
                    'max' => 2,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'transform: scale({{SIZE}});',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('navigation_transform_popover') => 'yes'
            ]
                ]
        );
        $this->parent->end_popover();
        // -------------------- POSITION
        $this->add_control(
                'navigation_position_popover', [
            'label' => __('Position', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => __('Default', 'e-addons'),
            'label_on' => __('Custom', 'e-addons'),
            'return_value' => 'yes',
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes'
            ]
                ]
        );
        $this->parent->start_popover();
        $this->add_responsive_control(
                'h_navigation_position', [
            'label' => __('Horizontal position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'options' => [
                'left: 0%;' => [
                    'title' => __('Left', 'e-addons'),
                    'icon' => 'eicon-h-align-left',
                ],
                'transform: translateX(-50%); left: 50%;' => [
                    'title' => __('Center', 'e-addons'),
                    'icon' => 'eicon-h-align-center',
                ],
                'left: auto; right: 0;' => [
                    'title' => __('Right', 'e-addons'),
                    'icon' => 'eicon-h-align-right',
                ],
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation' => '{{VALUE}}'
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('navigation_position_popover') => 'yes'
            ]
                ]
        );
        $this->add_responsive_control(
                'v_navigation_position', [
            'label' => __('Vertical position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'options' => [
                '0' => [
                    'title' => __('Top', 'e-addons'),
                    'icon' => 'eicon-v-align-top',
                ],
                '50' => [
                    'title' => __('Middle', 'e-addons'),
                    'icon' => 'eicon-v-align-middle',
                ],
                '100' => [
                    'title' => __('Down', 'e-addons'),
                    'icon' => 'eicon-v-align-bottom',
                ]
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation' => 'top: {{VALUE}}%;'
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('navigation_position_popover') => 'yes'
            ]
                ]
        );
        $this->add_responsive_control(
                'navigation_space', [
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
            'size_units' => '%',
            'range' => [
                '%' => [
                    'max' => 100,
                    'min' => 20,
                    'step' => 1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-carousel-controls .e-add-container-navigation' => 'width: {{SIZE}}%;'
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('navigation_position_popover') => 'yes'
            ]
                ]
        );

        $this->add_responsive_control(
                'horiz_navigation_shift', [
            'label' => __('Horizontal Shift', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px'
            ],
            'range' => [
                'px' => [
                    'max' => 200,
                    'min' => -200,
                    'step' => 1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('navigation_position_popover') => 'yes'
            ]
                ]
        );
        $this->add_responsive_control(
                'vert_navigation_shift', [
            'label' => __('Verical Shift', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px'
            ],
            'range' => [
                'px' => [
                    'max' => 200,
                    'min' => -200,
                    'step' => 1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'top: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('navigation_position_popover') => 'yes'
            ]
                ]
        );
        $this->parent->end_popover();
        
        
        $this->add_control(
                'navigation_padding', [
            'label' => __('Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
            ]
                ]
        );
        $this->add_control(
                'navigation_radius', [
            'label' => __('Border Radius', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
            ]
                ]
        );

        $this->add_control(
                'useNavigation_animationHover', [
            'label' => __('Use rollover animation', 'e-addons'),
            'description' => __('Enable for a short animation on rollover.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'prefix_class' => 'hoveranim-',
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes'
            ]
                ]
        );

        $this->end_controls_tab();

        // -----Tab pagination
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_pagination', [
            'label' => __('Pagination', 'e-addons'),
        ]);


        $this->add_control(
                'usePagination', [
            'label' => __('Use Pagination', 'e-addons'),
            'description' => __('Enable to use the slide progression display system ("bullets", "fraction", "progress").', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'pagination_type', [
            'label' => __('Pagination Type', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'bullets' => __('Bullets', 'e-addons'),
                'fraction' => __('Fraction', 'e-addons'),
                'progressbar' => __('Progressbar', 'e-addons'),
            ],
            'default' => 'bullets',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
            ]
                ]
        );

        // ------------ Pagination Fraction Options
        $this->add_control(
                'fraction_heading', [
            'label' => __('Fraction', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );
        $this->add_control(
                'fraction_separator', [
            'label' => __('Fraction text separator', 'e-addons'),
            'description' => __('The text that separates the 2 numbers', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'frontend_available' => true,
            'default' => '/',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );

        $this->add_control(
                'fraction_color', [
            'label' => __('Numbers color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-fraction > *' => 'color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );
        $this->add_control(
                'fraction_current_color', [
            'label' => __('Current Number Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current' => 'color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );
        $this->add_control(
                'fraction_separator_color', [
            'label' => __('Separator Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-fraction .separator' => 'color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'fraction_typography',
            'label' => __('Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} .swiper-pagination-fraction > *',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'fraction_typography_current',
            'label' => __('Current Number Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => __('fraction_typography_separator', 'e-addons'),
            'label' => 'Separator Typography',
            'selector' => '{{WRAPPER}} .swiper-pagination-fraction .separator',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );

        $this->add_responsive_control(
                'fraction_space', [
            'label' => __('Spacing', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '4',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => -20,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-fraction .separator' => 'margin: 0 {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'fraction',
            ]
                ]
        );
        // ------------ Pagination Bullets Options
        $this->add_control(
                'bullets_options_heading', [
            'label' => __('Bullets Options', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        $this->add_control(
                'dynamicBullets', [
            'label' => __('dynamicBullets', 'e-addons'),
            'description' => __('Useful when you use bullets pagination with a lot of slides. So it will keep only few bullets visible at the same time.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => ['bullets', 'custom'],
            ]
                ]
        );
        // ------------ Pagination Custom Options
        $this->add_control(
                'bullets_style', [
            'label' => __('Bullets Style', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'default' => __('Default', 'e-addons'),
                'shamso' => __('Dots', 'e-addons'),
                'timiro' => __('Circles', 'e-addons'),
                'xusni' => __('VerticalBars', 'e-addons'),
                'etefu' => __('Bars', 'e-addons'),
                'xusni' => __('VerticalBars', 'e-addons'),
                'ubax' => __('Square', 'e-addons'),
                'magool' => __('Lines', 'e-addons'),
            //'desta' 	=> __('Triangles', 'e-addons'),
            //'totit'		=> __('Icons', 'e-addons'),
            //'zahi' 		=> __('Timeline', 'e-addons'),
            ],
            'default' => 'default',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('dynamicBullets') => '',
            ]
                ]
        );

        // numbers
        $this->add_control(
                'bullets_numbers', [
            'label' => __('Show numbers', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('bullets_style!') => 'default',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('dynamicBullets') => '',
            ]
                ]
        );
        // numbers positions
        $this->add_control(
                'bullets_number_color', [
            'label' => __('Numbers Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet .swiper-pagination-bullet-title' => 'color: {{VALUE}}',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('bullets_style!') => 'default',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('dynamicBullets') => '',
                $this->get_control_id('bullets_numbers') => 'yes',
            ]
                ]
        );
        // numbers typography
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'bullets_number_typography',
            'label' => __('Numbers Typography', 'e-addons'),
            'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet .swiper-pagination-bullet-title',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('bullets_style!') => 'default',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('dynamicBullets') => '',
                $this->get_control_id('bullets_numbers') => 'yes',
            ]
                ]
        );
        // BULLETS STYLE
        $this->add_control(
                'bullets_style_heading', [
            'label' => __('Bullets Style', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );



        
        $this->add_control(
            'heading_bullet_color', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' => '<i class="far fa-circle"></i> <b>' . __('Normal', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'condition' => [
                    $this->get_control_id('usePagination') => 'yes',
                    $this->get_control_id('pagination_type') => 'bullets',
                ]
            ]
        );
        $this->add_control(
                'bullets_color', [
            'label' => __('Bullets Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullets.nav--default .swiper-pagination-bullet, {{WRAPPER}} .swiper-pagination-bullets.nav--ubax .swiper-pagination-bullet:after, {{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet:before, {{WRAPPER}} .swiper-pagination-bullets.nav--xusni .swiper-pagination-bullet:before, {{WRAPPER}} .swiper-pagination-bullets.nav--etefu .swiper-pagination-bullet, {{WRAPPER}} .swiper-pagination-bullets.nav--timiro .swiper-pagination-bullet, {{WRAPPER}} .swiper-pagination-bullets.nav--magool .swiper-pagination-bullet:after' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'border_bullet',
            'label' => __('Bullets border', 'e-addons'),
            'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        
        $this->add_control(
            'heading_current_bullet_color', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' => '<i class="fas fa-circle"></i> <b>' . __('Active', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'condition' => [
                    $this->get_control_id('usePagination') => 'yes',
                    $this->get_control_id('pagination_type') => 'bullets',
                ]
            ]
        );
        $this->add_control(
                'current_bullet_color', [
            'label' => __('Active bullet color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullets.nav--default .swiper-pagination-bullet-active, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--ubax .swiper-pagination-bullet-active:after, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet:not(.swiper-pagination-bullet-active), 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet-active:before, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--xusni .swiper-pagination-bullet-active:before, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--etefu .swiper-pagination-bullet-active:before, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--timiro .swiper-pagination-bullet-active:before, 
                 {{WRAPPER}} .swiper-pagination-bullets.nav--magool .swiper-pagination-bullet-active:after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .swiper-pagination-bullets.nav--shamso .swiper-pagination-bullet-active::after' => 'box-shadow: inset 0 0 0 3px {{VALUE}};'
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        $this->add_control(
            'border_color_current_bullet',
            [
                'type' => Controls_Manager::COLOR,
                'label' => __('Active bullet border', 'e-addons'),
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active:not(.nav--ubax):not(.nav--magool), {{WRAPPER}} .swiper-pagination-bullets.nav--ubax .swiper-pagination-bullet-active::after' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    $this->get_control_id('usePagination') => 'yes',
                    $this->get_control_id('pagination_type') => 'bullets',
                ]
            ]
        );
     


        
        $this->add_control(
            'heading_pagination_transform', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' => '<i class="fas fa-vector-square"></i> <b>' . __('Transform', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'condition' => [
                    $this->get_control_id('usePagination') => 'yes',
                    $this->get_control_id('pagination_type') => 'bullets',
                ]
            ]
        );
        // -------------- Transform
        $this->add_control(
                'pagination_transform_popover', [
            'label' => __('Transform', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => __('Default', 'e-addons'),
            'label_on' => __('Custom', 'e-addons'),
            'return_value' => 'yes',
            'separrator' => 'before',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        $this->parent->start_popover();


        $this->add_responsive_control(
                'pagination_bullets_opacity', [
            'label' => __('Opacity (%)', 'e-addons'),
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
                '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'opacity: {{SIZE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('pagination_transform_popover') => 'yes'
            ]
                ]
        );
        $this->add_responsive_control(
                'pagination_bullets_space', [
            'label' => __('Space', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: {{SIZE}}{{UNIT}} 0;'
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('pagination_transform_popover') => 'yes'
            ]
                ]
        );
        $this->add_responsive_control(
                'pagination_bullets_dimansion', [
            'label' => __('Bullets dimension', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets.swiper-pagination-bullets-dynamic' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets.swiper-pagination-bullets-dynamic' => 'width: {{SIZE}}{{UNIT}};'
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('pagination_transform_popover') => 'yes'
            ]
                ]
        );
        /* $this->add_responsive_control(
          'current_bullet', [
          'label' => __('Dimension of active bullet', 'e-addons'),
          'type' => Controls_Manager::SLIDER,
          'default' => [
          'size' => '',
          'unit' => 'px',
          ],
          'tablet_default' => [
          'unit' => 'px',
          ],
          'mobile_default' => [
          'unit' => 'px',
          ],
          'size_units' => ['px'],
          'range' => [
          'px' => [
          'min' => 0,
          'max' => 100,
          ],
          ],
          'selectors' => [
          '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',

          ],
          'condition' => [
          $this->get_control_id('usePagination') => 'yes',
          $this->get_control_id('pagination_type') => 'bullets',
          $this->get_control_id('pagination_transform_popover') => 'yes'
          ]
          ]
          ); */
        $this->parent->end_popover();
        // -------------- Position
        $this->add_control(
                'pagination_position_popover', [
            'label' => __('Position', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => __('Default', 'e-addons'),
            'label_on' => __('Custom', 'e-addons'),
            'return_value' => 'yes',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
            ]
                ]
        );
        $this->parent->start_popover();
        $this->add_responsive_control(
                'h_pagination_position', [
            'label' => __('Position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => true,
            'options' => [
                'text-align: left; left: 0; transform: translate3d(0,0,0);' => [
                    'title' => __('Left', 'e-addons'),
                    'icon' => 'eicon-h-align-left',
                ],
                'text-align: center; left: 50%; transform: translate3d(-50%,0,0);' => [
                    'title' => __('Center', 'e-addons'),
                    'icon' => 'eicon-h-align-center',
                ],
                'text-align: right; left: auto; right: 0; transform: translate3d(0,0,0);' => [
                    'title' => __('Right', 'e-addons'),
                    'icon' => 'eicon-h-align-right',
                ],
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets' => '{{VALUE}}'
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('pagination_position_popover') => 'yes',
                $this->get_control_id('direction_slider') => 'horizontal'
            ]
                ]
        );
        $this->add_responsive_control(
                'v_pagination_position', [
            'label' => __('Position', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => true,
            'options' => [
                'top: 0; transform: translate3d(0,0,0);' => [
                    'title' => __('Left', 'e-addons'),
                    'icon' => 'eicon-v-align-top',
                ],
                'top: 50%; transform: translate3d(0,-50%,0);' => [
                    'title' => __('Center', 'e-addons'),
                    'icon' => 'eicon-v-align-middle',
                ],
                'top: auto; bottom: 0; transform: translate3d(0,0,0);' => [
                    'title' => __('Right', 'e-addons'),
                    'icon' => 'eicon-v-align-bottom',
                ],
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets' => '{{VALUE}}'
            ],
            'condition' => [
                $this->get_control_id('useNavigation') => 'yes',
                $this->get_control_id('pagination_position_popover') => 'yes',
                $this->get_control_id('direction_slider') => 'vertical'
            ]
                ]
        );
        $this->add_responsive_control(
                'pagination_bullets_posy', [
            'label' => __('Shift', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => -160,
                    'max' => 160,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-bullets' => ' bottom: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-bullets' => ' right: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'bullets',
                $this->get_control_id('pagination_position_popover') => 'yes'
            ]
                ]
        );

        $this->parent->end_popover();


        // ------------ Pagination progressbar Options
        $this->add_control(
                'progress_heading', [
            'label' => __('Progress', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'progressbar',
            ]
                ]
        );
        $this->add_control(
                'progress_color', [
            'label' => __('Bar Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'progressbar',
            ]
                ]
        );
        $this->add_control(
                'progressbar_bg_color', [
            'label' => __('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-progressbar' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'progressbar',
            ]
                ]
        );
        $this->add_responsive_control(
                'progressbal_size', [
            'label' => __('Progressbar Size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '4',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 80,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-vertical > .swiper-pagination-progressbar' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('usePagination') => 'yes',
                $this->get_control_id('pagination_type') => 'progressbar',
            ]
                ]
        );
        $this->end_controls_tab();

        // -----Tab scrollbar
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_scrollbar', [
            'label' => __('ScrollBar', 'e-addons'),
        ]);
        // ----------------- Scrollbar options ------
        $this->add_control(
                'useScrollbar', [
            'label' => __('Use Scrollbar', 'e-addons'),
            'description' => __('If "yes", you will use a scrollbar that displays navigation', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
                ]
        );
        $this->add_control(
                'scrollbar_draggable', [
            'label' => __('Draggable', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'scrollbar_hide', [
            'label' => __('Hide', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'scrollbar_style_popover', [
            'label' => __('Style', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => __('Default', 'e-addons'),
            'label_on' => __('Custom', 'e-addons'),
            'return_value' => 'yes',
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes'
            ]
                ]
        );
        $this->parent->start_popover();
        $this->add_control(
                'scrollbar_color', [
            'label' => __('Bar Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-scrollbar .swiper-scrollbar-drag' => 'background: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes',
                $this->get_control_id('scrollbar_style_popover') => 'yes',
            ]
                ]
        );
        $this->add_control(
                'scrollbar_bg_color', [
            'label' => __('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-scrollbar' => 'background: {{VALUE}};',
            ],
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes',
                $this->get_control_id('scrollbar_style_popover') => 'yes',
            ]
                ]
        );
        $this->add_responsive_control(
                'scrollbar_size', [
            'label' => __('Size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-container-horizontal > .swiper-scrollbar' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-container-vertical > .swiper-scrollbar' => 'width: {{SIZE}}{{UNIT}};'
            ],
            'condition' => [
                $this->get_control_id('useScrollbar') => 'yes',
                $this->get_control_id('scrollbar_style_popover') => 'yes',
            ]
                ]
        );
        $this->parent->end_popover();
        /* Da implemantare e verificare .......... */
        $this->end_controls_tab();

        $this->end_controls_tabs();

        // ******************************************************************
        // ******************************************************************
        // ******************************************************************

        $this->add_control(
                'hr_options',
                [
                    'type' => Controls_Manager::DIVIDER,
                    'style' => 'thick',
                ]
        );
        $this->start_controls_tabs('carousel_options');

        // -----Tab Autoplay
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_autoplay', [
            'label' => __('Autoplay', 'e-addons'),
        ]);

        // ------------------ Autoplay ------
        $this->add_control(
                'useAutoplay', [
            'label' => __('Use Autoplay', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'autoplay', [
            'label' => __('Auto Play', 'e-addons'),
            'description' => __('Delay between transitions (in ms). If this parameter is not specified (by default), autoplay will be disabled', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '4000',
            'min' => 0,
            'max' => 15000,
            'step' => 100,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('useAutoplay') => 'yes',
            ]
                ]
        );
        $this->add_control(
                'autoplayStopOnLast', [
            'label' => __('Autoplay stop on last slide', 'e-addons'),
            'description' => __('Enable this parameter and autoplay will be stopped when it reaches last slide (has no effect in loop mode)', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('useAutoplay') => 'yes',
                $this->get_control_id('autoplay!') => '',
            ]
                ]
        );
        $this->add_control(
                'autoplayDisableOnInteraction', [
            'label' => __('Autoplay Disable on interaction', 'e-addons'),
            'description' => __('Set to "false" and autoplay will not be disabled after user interactions (swipes), it will be restarted every time after interaction', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('useAutoplay') => 'yes',
                $this->get_control_id('autoplay!') => '',
            ]
                ]
        );

        $this->end_controls_tab();

        // -----Tab freemode
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_freemode', [
            'label' => __('FreeMode', 'e-addons'),
        ]);

        // ----------- Free Mode ------
        $this->add_control(
                'freeMode', [
            'label' => __('Use Free Mode', 'e-addons'),
            'description' => __('If true then slides will not have fixed positions', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true
                ]
        );
        $this->add_control(
            'freeModeMinimumVelocity', [
                'label' => __('Velocity', 'e-addons'),
                'description' => __('Minimum touchmove-velocity required to trigger free mode momentum', 'e-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0.02,
                'min' => 0,
                'max' => 1,
                'step' => 0.01,
                'frontend_available' => true,
                'condition' => [
                    $this->get_control_id('freeMode') => 'yes',
                ]
            ]
        );
        $this->add_control(
            'freeModeSticky', [
            'label' => __('Sticky', 'e-addons'),
            'description' => __('Set \'yes\' to enable snap to slides positions in free mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
            ]
                ]
        );
        $this->add_control(
            'freeModeMomentum_heading', [
                'label' => __('FreeMode Momentum', 'e-addons'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    $this->get_control_id('freeMode') => 'yes',
                ]
            ]
        );
        $this->add_control(
                'freeModeMomentum', [
            'label' => __('Enable Momentum', 'e-addons'),
            'description' => __('If true, then slide will keep moving for a while after you release it', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
            ]
                ]
        );
        
        $this->add_control(
                'freeModeMomentumRatio', [
            'label' => __('Ratio', 'e-addons'),
            'description' => __('Higher value produces larger momentum distance after you release slider', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'min' => 0,
            'max' => 10,
            'step' => 0.1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
                $this->get_control_id('freeModeMomentum') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'freeModeMomentumVelocityRatio', [
            'label' => __('Velocity Ratio', 'e-addons'),
            'description' => __('Higher value produces larger momentum velocity after you release slider', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'min' => 0,
            'max' => 10,
            'step' => 0.1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
                $this->get_control_id('freeModeMomentum') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'freeModeMomentumBounce', [
            'label' => __('Bounce', 'e-addons'),
            'description' => __('Set to false if you want to disable momentum bounce in free mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
                $this->get_control_id('freeModeMomentum') => 'yes'
            ]
                ]
        );
        $this->add_control(
                'freeModeMomentumBounceRatio', [
            'label' => __('Bounce Ratio', 'e-addons'),
            'description' => __('Higher value produces larger momentum bounce effect', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'min' => 0,
            'max' => 10,
            'step' => 0.1,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('freeMode') => 'yes',
                $this->get_control_id('freeModeMomentum') => 'yes',
                $this->get_control_id('freeModeMomentumBounce') => 'yes'
            ]
                ]
        );
        
        

        $this->end_controls_tab();

        // -----Tab options
        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        $this->start_controls_tab('tab_carousel_options', [
            'label' => __('Options', 'e-addons'),
        ]);
        // --------------- spaceBetween ------
        $this->add_responsive_control(
                'spaceBetween', [
            'label' => __('Space Between', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0,
            'tablet_default' => '',
            'mobile_default' => '',
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'frontend_available' => true,
                ]
        );
        $this->add_responsive_control(
                'slidesOffsetBefore', [
            'label' => __('Slides Offset Before', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0,
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'frontend_available' => true,
                ]
        );
        $this->add_responsive_control(
                'slidesOffsetAfter', [
            'label' => __('Slides Offset After', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0,
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'slidesPerColumnFill', [
            'label' => '<i class="fas fa-th-large"></i> '.__('Slides per Column Fill', 'e-addons'),
            'description' => __('Tranisition effect from the slides.', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'separator' => 'before',
            'options' => [
                'row' => __('Row', 'e-addons'),
                'column' => __('Column', 'e-addons'),
            ],
            'default' => 'row',
            'frontend_available' => true,
                ]
        );
        // --------------- loop ------
        $this->add_control(
                'loop', [
            'label' => '<i class="fas fa-infinity"></i> '.__('Loop', 'e-addons'),
            'description' => __('Set to true to enable continuous loop mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'before'
                ]
        );
        // --------------- centerSlides ------
        $this->add_control(
                'centeredSlides', [
            'label' => '<i class="fas fa-plus"></i> '.__('Centered Slides', 'e-addons'),
            'description' => __('When enabled active slide will be centered, not on the left as default.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id('effects!') => ['cube', 'flip'],
            ]
                ]
        );
        $this->add_control(
                'centeredSlidesBounds', [
            'label' => __('Centered Slides Bounds', 'e-addons'),
            'description' => __('If true, then active slide will be centered without adding gaps at the beginning and end of slider. Required centeredSlides: true. Not intended to be used with loop or pagination.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                $this->get_control_id('effects!') => ['cube', 'flip'],
                $this->get_control_id('centeredSlides') => 'yes',
            ]
                ]
        );
        // --------------- autoHeight ------
        $this->add_control(
                'autoHeight', [
            'label' => '<i class="fas fa-ruler-vertical"></i> '.__('Auto Height', 'e-addons'),
            'description' => __('Set to true and slider wrapper will adopt its height to the height of the currently active slide.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'before'
                ]
        );
        // --------------- grabCursor ------	
        $this->add_control(
                'grabCursor', [
            'label' => '<i class="fas fa-hand-rock"></i> '.__('Grab Cursor', 'e-addons'),
            'description' => __('This option may a little improve desktop usability. If enabled user will see the "grab" cursor when hover on Swiper.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'before'
                ]
        );
        // --------------- Keyboard ------
        $this->add_control(
                'keyboardControl', [
            'label' => '<i class="fas fa-keyboard"></i> '.__('Keyboard Control', 'e-addons'),
            'description' => __('Enable keyboard control', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
                ]
        );
        // --------------- Wheel ------
        $this->add_control(
                'mousewheelControl', [
            'label' => '<i class="fas fa-mouse"></i> '.__('Mousewheel Control', 'e-addons'),
            'description' => __('Enables navigation through slides using mouse wheel', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
                ]
        );
        /* $this->add_control(
          'watchOverflow', [
          'label' => __('Watch Overflow', 'e-addons'),
          'description' => __('When enabled Swiper will be disabled and hide navigation buttons on case there are not enough slides for sliding.', 'e-addons'),
          'type' => Controls_Manager::SWITCHER,
          'frontend_available' => true,
          'separator' => 'before',

          ]
          ); */
        /* $this->add_control(
          'watchSlidesVisibility', [
          'label' => __('Watch Slides Visibility', 'e-addons'),
          'description' => __('WatchSlidesProgress should be enabled. Enable this option and slides that are in viewport will have additional visible class.', 'e-addons'),
          'type' => Controls_Manager::SWITCHER,
          'frontend_available' => true,
          'separator' => 'before',
          //'condition' => [
          //    'watchSlidesProgress' => 'yes',
          //]
          ]
          ); */
        $this->add_control(
                'reverseDirection', [
            'label' => '<i class="fas fa-map-signs"></i> '.__('Reverse Direction RTL', 'e-addons'),
            'description' => __('Enables autoplay in reverse direction.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'before'
                ]
        );
        /* $this->add_control(
          'nested', [
          'label' => __('Nidificato', 'e-addons'),
          'description' => __('Set to true on nested Swiper for correct touch events interception. Use only on nested swipers that use same direction as the parent one.', 'e-addons'),
          'type' => Controls_Manager::SWITCHER,
          'frontend_available' => true,
          'separator' => 'before'
          ]
          ); */
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /* protected function register_style_controls() {
      parent::register_style_controls();

      $this->start_controls_section(
      'section_style_carousel',
      [
      'label' => __( 'Carousel', 'e-addons' ),
      'tab' => Controls_Manager::TAB_STYLE,
      ]
      );
      $this->end_controls_section();
      } */

    protected function render_container_before() {
        echo '<div class="e-add-carousel-container">';
    }

    protected function render_container_after() {
        echo '<div class="e-add-carousel-controls" data-post-id="' . $this->current_id . '">';
        if ($this->get_instance_value('usePagination')) {
            //@p questi sono degli stili aggiuntivi per i bullets
            $bullets_style = $this->get_instance_value('bullets_style');
            $style_pagination = $this->get_instance_value('pagination_type');
            $dynamicBullets = $this->get_instance_value('dynamicBullets');
            $bullets_class = !empty($bullets_style) && $style_pagination == 'bullets' && !$dynamicBullets ? ' e-add-nav-style nav--' . $bullets_style : ' nav--default';
            // Add Pagination
            echo '<div class="e-add-container-pagination swiper-container-' . $this->get_instance_value('direction_slider') . '"><div class="swiper-pagination pagination-' . $this->parent->get_id() . '-' . $this->current_id . $bullets_class . '"></div></div>';
        }
        if ($this->get_instance_value('useNavigation')) {
            // Add Arrows
            echo '<div class="e-add-container-navigation swiper-container-' . $this->get_instance_value('direction_slider') . '">';
            echo '<div class="swiper-button-prev prev-' . $this->parent->get_id() . '-' . $this->current_id . '"><svg x="-10px" y="-10px"
            width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" xml:space="preserve">
            <line fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" x1="382.456" y1="298.077" x2="458.375" y2="298.077"/>
            <polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" points="416.287,331.909 382.456,298.077 
            416.287,264.245 "/>
            </svg></div>';
            echo '<div class="swiper-button-next next-' . $this->parent->get_id() . '-' . $this->current_id . '"><svg xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" xml:space="preserve">
            <line fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" x1="458.375" y1="298.077" x2="382.456" y2="298.077"/>
            <polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" points="424.543,264.245 458.375,298.077 
            424.543,331.909 "/>
            </svg></div>';
            echo '</div>';
        }
        echo '</div>';

        echo '</div>'; // END: e-add-carousel-container
    }

    protected function render_posts_after() {
        if ($this->get_instance_value('useScrollbar')) {
            echo '<div class="swiper-scrollbar"></div>';
        }
    }

    // Classes ----------
    public function get_container_class() {
        return 'swiper-container e-add-skin-' . $this->get_id();
    }

    public function get_wrapper_class() {
        return 'swiper-wrapper e-add-wrapper-' . $this->get_id();
    }

    public function get_item_class() {
        return 'swiper-slide e-add-item-' . $this->get_id();
    }

}
