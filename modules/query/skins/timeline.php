<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Timeline extends Base {

    public function _register_controls_actions() {
        parent::_register_controls_actions();
        add_action('elementor/element/' . $this->parent->get_name() . '/section_e_query/after_section_end', [$this, 'register_additional_timeline_controls'], 20);
    }

    public function get_script_depends() {
        return ['e-addons-query-timeline'];
    }

    public function get_style_depends() {
        return ['e-addons-common-query', 'e-addons-query-timeline'];
    }

    public function get_id() {
        return 'timeline';
    }

    public function get_title() {
        return __('Timeline', 'e-addons');
    }

    public function get_docs() {
        return 'https://e-addons.com';
    }

    public function get_icon() {
        return 'eadd-queryviews-timeline';
    }

    public function register_additional_timeline_controls() {

        $this->start_controls_section(
                'section_timeline', [
            'label' => '<i class="eaddicon eadd-queryviews-timeline"></i> ' . __('Timeline', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_responsive_control(
                'timeline_imagesize', [
            'label' => '<i class="fas fa-image"></i>&nbsp;&nbsp;' . __('Image size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 400,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline__img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ]
                ]
        );
        $this->add_responsive_control(
                'timeline_verticalposition', [
            'label' => '<i class="fas fa-arrows-alt-v"></i>&nbsp;&nbsp;' . __('Vertical Position (%)', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'frontend_available' => true,
            'size_units' => ['%'],
            'separator' => 'before',
            'default' => [
                'size' => '50',
                'unit' => '%'
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline__content::before, {{WRAPPER}} .e-add-timeline__img, {{WRAPPER}} .e-add-item_date' => 'top: {{SIZE}}%;',
            ]
                ]
        );
        $this->add_control(
                'timeline_width', [
            'label' => '<i class="fas fa-arrows-alt-h"></i>&nbsp;&nbsp;' . __('Width', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vw'],
            'default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'max' => 1200,
                    'min' => 0,
                    'step' => 1,
                ]
            ],
            'selectors' => [
                'body[data-elementor-device-mode=desktop] {{WRAPPER}} .e-add-timeline-wrapper' => 'width: {{SIZE}}{{UNIT}};',
            ]
                ]
        );
        $this->add_control(
                'timeline_space_content', [
            'label' => '<i class="fas fa-clone"></i>&nbsp;<i class="fas fa-exchange-alt"></i>&nbsp;&nbsp;' . __('Content Space', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'separator' => 'before',
            'size_units' => ['px', '%', 'vw'],
            'default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'max' => 1200,
                    'min' => 0,
                    'step' => 1,
                ]
            ],
            'selectors' => [
                'body[data-elementor-device-mode=desktop] {{WRAPPER}} .e-add-timeline__content' => 'width: calc((100% / 2) - ({{SIZE}}{{UNIT}} / 2))',
                'body[data-elementor-device-mode=desktop] {{WRAPPER}} .e-add-timeline__block:nth-child(odd) .e-add-item_date' => 'left: calc(100% + {{SIZE}}{{UNIT}})',
                'body[data-elementor-device-mode=desktop] {{WRAPPER}} .e-add-timeline__block:nth-child(even) .e-add-item_date' => 'right: calc(100% + {{SIZE}}{{UNIT}})'
            ]
                ]
        );
        $this->add_control(
                'timeline_shift_date', [
            'label' => '<i class="far fa-calendar-alt">&nbsp;</i><i class="fas fa-exchange-alt"></i>&nbsp;&nbsp;' . __('Date shift', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'separator' => 'after',
            'size_units' => ['px'],
            'default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'max' => 200,
                    'min' => -200,
                    'step' => 1,
                ]
            ],
            'selectors' => [
                'body[data-elementor-device-mode=desktop] {{WRAPPER}} .e-add-timeline__block:nth-child(odd) .e-add-item_date' => 'margin-left: {{SIZE}}{{UNIT}};',
                'body[data-elementor-device-mode=desktop] {{WRAPPER}} .e-add-timeline__block:nth-child(even) .e-add-item_date' => 'margin-right: {{SIZE}}{{UNIT}};'
            ]
                ]
        );
        $this->add_responsive_control(
                'timeline_rowspace', [
            'label' => '<i class="fas fa-arrows-alt-v"></i>&nbsp;&nbsp;' . __('Row Space', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'size_units' => ['px', 'em'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ]
            ],
            'render_type' => 'template',
            'frontend_available' => true,
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline__block' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            //'{{WRAPPER}} .e-add-timeline-wrapper::before, {{WRAPPER}} .e-add-timeline-wrapper::after' => 'top: {{SIZE}}{{UNIT}};',
            ]
                ]
        );
        $this->end_controls_section();
    }

    protected function register_style_controls() {
        //parent::register_style_controls();

        $this->start_controls_section(
                'section_style_timeline',
                [
                    'label' => '<i class="eaddicon eadd-queryviews-timeline"></i> '.__('Timeline', 'e-addons'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );


        // ------------------- LINE - progress
        $this->add_control(
                'timeline_heading_line', [
            'label' => __('Style', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
                ]
        );
        $this->start_controls_tabs('timeline_styles');

        $this->start_controls_tab(
                'timeline_style_normal', [
            'label' => __('Normal', 'e-addons'),
                ]
        );

        $this->add_control(
            'timleline_line_color', [
                'label' => __('Line Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .e-add-timeline-wrapper::before' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .e-add-timeline__block .e-add-timeline__img' => 'border-color: {{VALUE}}'
                ],
            ]
        );
        $this->add_control(
            'timleline_bg_color', [
                'label' => __('Background Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .e-add-timeline__block .e-add-timeline__img' => 'background-color: {{VALUE}}'
                ],
            ]
        );
        $this->add_control(
                'timeline_line_size', [
            'label' => __('Line size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 30,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline-wrapper::before' => 'width: {{SIZE}}{{UNIT}};',
            ]
                ]
        );
        $this->add_control(
                'timeline_bg_color_content', [
            'label' => __('Panel Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline__block .e-add-timeline__content' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .e-add-timeline__block:nth-child(odd) .e-add-timeline__content::before' => 'border-left-color: {{VALUE}}; border-right-color: {{VALUE}};',
                '{{WRAPPER}} .e-add-timeline__block:nth-child(even) .e-add-timeline__content::before' => 'border-right-color: {{VALUE}};'
            ]
                ]
        );
        $this->add_control(
                'timeline_borderimage_size', [
            'label' => __('Border image size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 30,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline__block .e-add-timeline__img' => 'border-width: {{SIZE}}{{UNIT}};',
            ]
                ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
                'timeline_style_active', [
            'label' => __('Active', 'e-addons'),
                ]
        );

        $this->add_control(
                'timleline_activeline_color', [
            'label' => __('Active Line Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline-wrapper::after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .e-add-timeline__block.e-add-timeline__focus .e-add-timeline__img' => 'border-color: {{VALUE}}'
            ],
                ]
        );
        $this->add_control(
            'timleline_active_bg_color', [
                'label' => __('Active Background Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .e-add-timeline__block.e-add-timeline__focus .e-add-timeline__img' => 'background-color: {{VALUE}}'
                ],
            ]
        );
        $this->add_control(
                'timeline_activeline_size', [
            'label' => __('Active: Line size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 30,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline-wrapper::after' => 'width: {{SIZE}}{{UNIT}};',
            ]
                ]
        );
        $this->add_control(
                'timeline_activebg_color_content', [
            'label' => __('Active: Panel Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline__block.e-add-timeline__focus .e-add-timeline__content' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .e-add-timeline__block.e-add-timeline__focus:nth-child(odd) .e-add-timeline__content::before' => 'border-left-color: {{VALUE}}; border-right-color: {{VALUE}};',
                '{{WRAPPER}} .e-add-timeline__block.e-add-timeline__focus:nth-child(even) .e-add-timeline__content::before' => 'border-right-color: {{VALUE}};'
            ]
                ]
        );
        $this->add_control(
                'timeline_activeborderimage_size', [
            'label' => __('Active: Border image size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 30,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline__block.e-add-timeline__focus .e-add-timeline__img' => 'border-width: {{SIZE}}{{UNIT}};',
            ]
                ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();



        // ------------------- CONTENT PANEL
        $this->add_control(
            'timeline_heading_panelcontent', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' => '<i class="far fa-square"></i> <b>' . __('Panel Content', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
                'timeline_content_padding', [
            'label' => __('Content Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ]
                ]
        );
        $this->add_responsive_control(
            'timeline_radius_content', [
                'label' => __('Content Border Radius', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-timeline__content' => 'border-radius: {{SIZE}}{{UNIT}};',
                    ]
            ]
        );
        
        $this->add_control(
                'timeline_arrows_size', [
            'label' => __('Content arrows size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-timeline__content::before, {{WRAPPER}} .e-add-timeline__content::before' => 'border-width: {{SIZE}}{{UNIT}};',
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'timeline_content_boxshadow',
            'selector' => '{{WRAPPER}} .e-add-post-item .e-add-post-block',
                ]
        );
        $this->end_controls_section();
    }

    /* public function render() {
      parent::render();
      echo 'Skin: timeline';
      //echo 'is:'.$this->get_id().' skin:'.$this->parent->get_settings('_skin');
      //var_dump($this->parent->get_script_depends());
      } */

    protected function render_element_item() {
        
        $this->index++;
        
        // ID
        $p_id = $this->current_id;
        //
        $image_attr = [
            'class' => $this->get_image_class()
        ];

        $querytype = $this->parent->get_querytype();
        /*
        switch ($querytype) {
            case 'attachment':
            case 'post':

                // image
                $p_image = wp_get_attachment_image(get_post_thumbnail_id(), 'thumbnail', false, $image_attr);
                 
                break;
            case 'user':
                $user_info = $this->current_data;
                //se mi trovo in user

                // @p questa è l'mmagine avatar HTML
                $p_image = get_avatar($user_info->user_email, 200);
                break;
            case 'term':
                //se mi trovo in term
                $term_info = $this->current_data;
                $p_title = $term_info->name;

                //@p term lo devo ancora mettere insieme .....
                break;
            case 'items':

                // image
                $image_id = $this->current_data['sl_image']['id'];
                $p_image = wp_get_attachment_image($image_id, 'thumbnail', false, $image_attr);


                break;
        }*/
        //-----------------------------------------------
        // author image 
        $p_author = get_the_author_meta('display_name');
        $p_authorimage = get_avatar_url(get_the_author_meta('ID'));

        // link of post ...
        $p_link = $this->current_permalink;
        ?>
        <div class="e-add-timeline__block">
            <div class="e-add-timeline__img e-add-timeline__img--picture">
                <?php /*echo $p_image;*/$this->render_items_image() ?>
            </div> <!-- e-add-timeline__img -->

            <div class="e-add-timeline__content">

                <?php $this->render_items(); ?>

            </div> <!-- e-add-timeline__content -->
        </div> <!-- e-add-timeline__block -->

        <?php
        $this->counter++;
    }

    // Classes ----------
    public function get_container_class() {
        return 'e-add-timeline js-e-add-timeline e-add-timeline-container e-add-skin-' . $this->get_id();
    }

    public function get_wrapper_class() {
        return 'e-add-timeline-wrapper e-add-wrapper-' . $this->get_id();
    }

    public function get_item_class() {
        return 'e-add-timeline__block e-add-timeline-item e-add-item-' . $this->get_id();
    }

}
