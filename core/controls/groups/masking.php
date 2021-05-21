<?php

namespace EAddonsForElementor\Core\Controls\Groups;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Masking any elements this control is a group control.
 *
 * A control for transformer any elements (rotation, transition, scale  with perspective and origin )
 *
 * @since 1.0.0
 */
class Masking extends Group_Control_Base {

    protected static $fields;

    public static function get_type() {
        return 'masking';
    }

    protected function init_fields() {
        $controls = [];
        $logo = $controls['masking_type'] = [
            'type' => Controls_Manager::HIDDEN,
            'default' => 'custom',
        ];

        $controls['mask_type'] = [
            'label' => __('Enable Mask', 'e-addons-for-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'options' => [
                'none' => [
                    'title' => 'None',
                    'icon' => 'fa fa-ban',
                ],
                'image' => [
                    'title' => 'Image',
                    'icon' => 'fa fa-image',
                ],
                'clippath' => [
                    'title' => 'Clip-Path',
                    'icon' => 'fa fa-circle',
                ],
            ],
            'label_block' => false,
            'default' => 'none',
        ];

        $controls['images_mask'] = [
            'label' => __('Select IMAGE mask', 'e-addons-for-elementor', 'smoothscroll'),
            'type' => 'ui_selector',
            'label_block' => true,
            'toggle' => false,
            'type_selector' => 'image',
            'columns_grid' => 4,
            'default' => E_ADDONS_URL . 'assets/img/masking/splash.png',
            'options' => [
                'mask1' => [
                    'title' => 'Splash',
                    'image' => E_ADDONS_URL . 'assets/img/masking/splash.png',
                    'image_preview' => E_ADDONS_URL . 'assets/img/masking/low/splash.jpg'
                ],
                'mask2' => [
                    'title' => 'Blobber',
                    'image' => E_ADDONS_URL . 'assets/img/masking/blobber.png',
                    'image_preview' => E_ADDONS_URL . 'assets/img/masking/low/blobber.jpg'
                ],
                'mask3' => [
                    'title' => 'Diagonal Lines',
                    'image' => E_ADDONS_URL . 'assets/img/masking/diagonal_lines.png',
                    'image_preview' => E_ADDONS_URL . 'assets/img/masking/low/diagonal_lines.jpg'
                ],
                'mask4' => [
                    'title' => 'Rhombus Pattern',
                    'image' => E_ADDONS_URL . 'assets/img/masking/rombs_pattern.png',
                    'image_preview' => E_ADDONS_URL . 'assets/img/masking/low/rombs_pattern.jpg'
                ],
                'mask5' => [
                    'title' => 'Waves',
                    'image' => E_ADDONS_URL . 'assets/img/masking/waves_horizontal.png',
                    'image_preview' => E_ADDONS_URL . 'assets/img/masking/low/waves_horizontal.jpg'
                ],
                'mask6' => [
                    'title' => 'Drawing',
                    'image' => E_ADDONS_URL . 'assets/img/masking/drawing.png',
                    'image_preview' => E_ADDONS_URL . 'assets/img/masking/low/drawing.jpg'
                ],
                'mask7' => [
                    'title' => 'Sketching',
                    'image' => E_ADDONS_URL . 'assets/img/masking/sketching.png',
                    'image_preview' => E_ADDONS_URL . 'assets/img/masking/low/sketching.jpg'
                ],
                'custom_mask' => [
                    'title' => 'Custom mask',
                    //'icon' => 'fa fa-list-ul',
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'assets/img/custom.jpg',
                    'image_preview' => E_ADDONS_URL . 'assets/img/custom.jpg',
                ],
            ],
            'condition' => [
                'mask_type' => 'image'
            ],
            'selectors' => [
                '{{SELECTOR}}' => '-webkit-mask-image: url({{VALUE}}); mask-image: url({{VALUE}}); -webkit-mask-position: 50% 50%; mask-position: 50% 50%; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-size: contain; mask-size: contain;',
            ]
        ];
        $controls['custom_image_mask'] = [
            'label' => __('Select PNG:', 'e-addons-for-elementor'),
            'type' => Controls_Manager::MEDIA,
            'dynamic' => [
                'active' => true,
            ],
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
            'condition' => [
                'images_mask' => 'custom_mask',
                'mask_type' => 'image'
            ],
            'selectors' => [
                '{{SELECTOR}}' => '-webkit-mask-image: url({{URL}}); mask-image: url({{URL}}); -webkit-mask-position: 50% 50%; mask-position: 50% 50%; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-size: contain; mask-size: contain;',
            ]
        ];
        $controls['position_image_mask'] = [
            'label' => __('Position', 'e-addons-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => [
                '' => __('Default', 'e-addons-for-elementor'),
                'center center' => __('Center Center', 'e-addons-for-elementor'),
                'center left' => __('Center Left', 'e-addons-for-elementor'),
                'center right' => __('Center Right', 'e-addons-for-elementor'),
                'top center' => __('Top Center', 'e-addons-for-elementor'),
                'top left' => __('Top Left', 'e-addons-for-elementor'),
                'top right' => __('Top Right', 'e-addons-for-elementor'),
                'bottom center' => __('Bottom Center', 'e-addons-for-elementor'),
                'bottom left' => __('Bottom Left', 'e-addons-for-elementor'),
                'bottom right' => __('Bottom Right', 'e-addons-for-elementor'),
                'initial' => __('Custom', 'e-addons-for-elementor'),
            ],
            'condition' => [
                'mask_type' => 'image',
            ],
            'selectors' => [
                '{{SELECTOR}}' => '-webkit-mask-position: {{VALUE}}; mask-position: {{VALUE}};',
            ]
        ];
        $controls['xpos_image_mask'] = [
            'label' => __('X Position', 'e-addons-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', '%', 'vw'],
            'default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'tablet_default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'mobile_default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'range' => [
                'px' => [
                    'min' => -800,
                    'max' => 800,
                ],
                'em' => [
                    'min' => -100,
                    'max' => 100,
                ],
                '%' => [
                    'min' => -100,
                    'max' => 100,
                ],
                'vw' => [
                    'min' => -100,
                    'max' => 100,
                ],
            ],
            'condition' => [
                'mask_type' => 'image',
                'position_image_mask' => ['initial'],
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'mask-position: {{SIZE}}{{UNIT}} {{ypos_image_mask.SIZE}}{{ypos_image_mask.UNIT}}',
            ]
        ];
        $controls['ypos_image_mask'] = [
            'label' => __('Y Position', 'e-addons-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', '%', 'vh'],
            'default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'tablet_default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'mobile_default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'range' => [
                'px' => [
                    'min' => -800,
                    'max' => 800,
                ],
                'em' => [
                    'min' => -100,
                    'max' => 100,
                ],
                '%' => [
                    'min' => -100,
                    'max' => 100,
                ],
                'vh' => [
                    'min' => -100,
                    'max' => 100,
                ],
            ],
            'condition' => [
                'mask_type' => 'image',
                'position_image_mask' => ['initial'],
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'mask-position: {{xpos_image_mask.SIZE}}{{xpos_image_mask.UNIT}} {{SIZE}}{{UNIT}}',
            ]
        ];
        $controls['repeat_image_mask'] = [
            'label' => __('Repeat', 'e-addons-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => [
                '' => __('Default', 'e-addons-for-elementor'),
                'no-repeat' => __('No-repeat', 'e-addons-for-elementor'),
                'repeat' => __('Repeat', 'e-addons-for-elementor'),
                'repeat-x' => __('Repeat-x', 'e-addons-for-elementor'),
                'repeat-y' => __('Repeat-y', 'e-addons-for-elementor'),
            ],
            'condition' => [
                'mask_type' => 'image',
            ],
            'selectors' => [
                '{{SELECTOR}}' => '-webkit-mask-repeat: {{VALUE}}; mask-repeat: {{VALUE}};',
            ]
        ];
        $controls['size_image_mask'] = [
            'label' => __('Size', 'e-addons-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => [
                '' => __('Default', 'e-addons-for-elementor'),
                'auto' => __('Auto', 'e-addons-for-elementor'),
                'cover' => __('Cover', 'e-addons-for-elementor'),
                'contain' => __('Contain', 'e-addons-for-elementor'),
                'initial' => __('Custom', 'e-addons-for-elementor'),
            ],
            'condition' => [
                'mask_type' => 'image',
            ],
            'selectors' => [
                '{{SELECTOR}}' => '-webkit-mask-size: {{VALUE}}; mask-size: {{VALUE}};',
            ]
        ];
        $controls['width_image_mask'] = [
            'label' => __('Width', 'e-addons-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', '%', 'vw'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'vw' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'default' => [
                'size' => 100,
                'unit' => '%',
            ],
            'required' => true,
            'condition' => [
                'mask_type' => 'image',
                'size_image_mask' => ['initial'],
            ],
            'selectors' => [
                '{{SELECTOR}}' => '-webkit-mask-size: {{SIZE}}{{UNIT}} auto; mask-size: {{SIZE}}{{UNIT}} auto;',
            ]
        ];
        /*
          $controls['svg_masking'] = [
          'label' => __( 'Icon', 'elementor-pro' ),
          'type' => Controls_Manager::ICONS,
          'default' => [
          'value' => 'fas fa-circle',
          'library' => 'fa-solid',
          ],
          'recommended' => [
          'fa-solid' => [
          'circle',
          'dot-circle',
          'square-full',
          ],
          'fa-regular' => [
          'circle',
          'dot-circle',
          'square-full',
          ],
          ],
          'condition' => [
          'mask_type' => 'svg'
          ],
          //'skin' => 'inline',
          //'label_block' => false,
          //'exclude_inline_options' => [ 'icon' ],
          // da valutare ed implementare
          'selectors' => [
          '{{SELECTOR}}' => '.......',
          ]
          ];
         */
        $controls['clippath_mask'] = [
            'label' => __('Predefined Clip-Path', 'e-addons-for-elementor'),
            'type' => 'ui_selector',
            'toggle' => false,
            'label_block' => true,
            'type_selector' => 'image',
            'columns_grid' => 5,
            'default' => 'polygon(50% 0%, 0% 100%, 100% 100%)',
            'options' => [
                'polygon(50% 0%, 0% 100%, 100% 100%)' => [
                    'title' => 'Triangle',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/triangle.svg'
                ],
                'polygon(20% 0%, 80% 0%, 100% 100%, 0% 100%)' => [
                    'title' => 'Trapezoid',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/trapezoid.svg'
                ],
                'polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%)' => [
                    'title' => 'Parallelogram',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/parallelogram.svg'
                ],
                'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)' => [
                    'title' => 'Rombus',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/rombus.svg'
                ],
                'polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%)' => [
                    'title' => 'Pentagon',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/pentagon.svg'
                ],
                'polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%)' => [
                    'title' => 'Hexagon',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/hexagon.svg'
                ],
                'polygon(50% 0%, 90% 20%, 100% 60%, 75% 100%, 25% 100%, 0% 60%, 10% 20%)' => [
                    'title' => 'Heptagon',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/heptagon.svg'
                ],
                'polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%)' => [
                    'title' => 'Octagon',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/octagon.svg'
                ],
                'polygon(50% 0%, 83% 12%, 100% 43%, 94% 78%, 68% 100%, 32% 100%, 6% 78%, 0% 43%, 17% 12%)' => [
                    'title' => 'Nonagon',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/nonagon.svg'
                ],
                'polygon(50% 0%, 80% 10%, 100% 35%, 100% 70%, 80% 90%, 50% 100%, 20% 90%, 0% 70%, 0% 35%, 20% 10%)' => [
                    'title' => 'Decagon',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/decagon.svg'
                ],
                'polygon(20% 0%, 80% 0%, 100% 20%, 100% 80%, 80% 100%, 20% 100%, 0% 80%, 0% 20%)' => [
                    'title' => 'Bevel',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/bevel.svg'
                ],
                'polygon(0% 15%, 15% 15%, 15% 0%, 85% 0%, 85% 15%, 100% 15%, 100% 85%, 85% 85%, 85% 100%, 15% 100%, 15% 85%, 0% 85%)' => [
                    'title' => 'Rabbet',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/rabbet.svg'
                ],
                'polygon(40% 0%, 40% 20%, 100% 20%, 100% 80%, 40% 80%, 40% 100%, 0% 50%)' => [
                    'title' => 'Left arrow',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/leftarrow.svg'
                ],
                'polygon(0% 20%, 60% 20%, 60% 0%, 100% 50%, 60% 100%, 60% 80%, 0% 80%)' => [
                    'title' => 'Right arrow',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/rightarrow.svg'
                ],
                'polygon(25% 0%, 100% 1%, 100% 100%, 25% 100%, 0% 50%)' => [
                    'title' => 'Left point',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/leftpoint.svg'
                ],
                'polygon(0% 0%, 75% 0%, 100% 50%, 75% 100%, 0% 100%)' => [
                    'title' => 'Right point',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/rightpoint.svg'
                ],
                'polygon(100% 0%, 75% 50%, 100% 100%, 25% 100%, 0% 50%, 25% 0%)' => [
                    'title' => 'Left chevron',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/leftchevron.svg'
                ],
                'polygon(75% 0%, 100% 50%, 75% 100%, 0% 100%, 25% 50%, 0% 0%)' => [
                    'title' => 'Right Chevron',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/rightchevron.svg'
                ],
                'polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%)' => [
                    'title' => 'Star',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/star.svg'
                ],
                'polygon(10% 25%, 35% 25%, 35% 0%, 65% 0%, 65% 25%, 90% 25%, 90% 50%, 65% 50%, 65% 100%, 35% 100%, 35% 50%, 10% 50%)' => [
                    'title' => 'Cross',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/cross.svg'
                ],
                'polygon(0% 0%, 100% 0%, 100% 75%, 75% 75%, 75% 100%, 50% 75%, 0% 75%)' => [
                    'title' => 'Message',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/message.svg'
                ],
                'polygon(20% 0%, 0% 20%, 30% 50%, 0% 80%, 20% 100%, 50% 70%, 80% 100%, 100% 80%, 70% 50%, 100% 20%, 80% 0%, 50% 30%)' => [
                    'title' => 'Close',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/close.svg'
                ],
                'polygon(0% 0%, 0% 100%, 25% 100%, 25% 25%, 75% 25%, 75% 75%, 25% 75%, 25% 100%, 100% 100%, 100% 0%)' => [
                    'title' => 'Frame',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/frame.svg'
                ],
                'circle(50% at 50% 50%)' => [
                    'title' => 'Circle',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/circle.svg'
                ],
                'ellipse(25% 40% at 50% 50%)' => [
                    'title' => 'Ellipse',
                    'return_val' => 'val',
                    'image_preview' => E_ADDONS_URL . 'assets/img/clippath/ellipse.svg'
                ],
            ],
            'condition' => [
                'mask_type' => 'clippath'
            ],
            'selectors' => [
                '{{SELECTOR}}' => '-webkit-clip-path: {{VALUE}}; clip-path: {{VALUE}};',
            ]
        ];
        return $controls;
    }

    protected function get_default_options() {
        return [
            'popover' => false,
            'show_label' => true
        ];
    }

}
