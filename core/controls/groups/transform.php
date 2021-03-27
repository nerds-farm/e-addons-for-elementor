<?php
namespace EAddonsForElementor\Core\Controls\Groups;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Transform any elements this control is a group control.
 *
 * A control for transformer any elements (rotation, transition, scale  with perspective and origin )
 *
 * @since 1.0.0
 */
class Transform extends Group_Control_Base {
    
    protected static $fields;

    public static function get_type() {
        return 'transform';
    }

    protected function init_fields() {
        $controls = [];
        $controls['transform_type'] = [
            'type' => Controls_Manager::HIDDEN,
            'default' => 'custom',
        ];
        
        $controls['transformations'] = [
            'label' => '<i class="fas fa-vector-square"></i> '._x( 'Transformations', 'Transform Control', 'elementor' ),
            'type' => 'transformations',
            'responsive' => true,
            'render_type' => 'ui',
            'default' => [
                'angle' => 0,
                'rotate_x' => 0,
                'rotate_y' => 0,
                'translate_x' => 0,
                'translate_y' => 0,
                'translate_z' => 0,
                'scale' => 1,
            ],
            'tablet_default' => [
                'angle' => '',
                'rotate_x' => '',
                'rotate_y' => '',
                'translate_x' => '',
                'translate_y' => '',
                'translate_z' => '',
                'scale' => '',
            ],
            'mobile_default' => [
                'angle' => '',
                'rotate_x' => '',
                'rotate_y' => '',
                'translate_x' => '',
                'translate_y' => '',
                'translate_z' => '',
                'scale' => '',
            ],
            'condition' => [
                'transform_type' => 'custom',
            ],
            'selectors' => [
                '{{SELECTOR}} > *:first-child' => 'transform: rotateZ({{ANGLE}}deg) rotateX({{ROTATE_X}}deg) rotateY({{ROTATE_Y}}deg) scale({{SCALE}}) translate3D({{TRANSLATE_X}}%,{{TRANSLATE_Y}}%,{{TRANSLATE_Z}}px);',
            ],
        ];
        
        /*$controls['origin_hr'] = [
            'type' => Controls_Manager::DIVIDER,
            'style' => 'thick',
        ];*/
        $controls['perspective'] = [
            'label' => _x( 'Perspective', 'Perspective Control', 'e-addoons' ),
            'type' => Controls_Manager::SLIDER,
            'responsive' => true,
            //'render_type' => 'template',
            'render_type' => 'ui',
            'default' => [
                'size' => '',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                  'max' => 2000,
                  'min' => 0,
                  'step' => 1,
               ],
            ],
            
            'selectors' => [
                '{{SELECTOR}}' => 'perspective: {{SIZE}}{{UNIT}}; -webkit-perspective: {{SIZE}}{{UNIT}};',
            ],
        ];
        $controls['transform_origin'] = [
			'label' => _x( 'Transform Origin', 'Transform', 'e-addons-for-elementor' ),
			'type' => Controls_Manager::SELECT,
			'default' => '',
			'responsive' => true,
			'options' => [
				'' => _x( 'Default', 'Transform origin', 'e-addons-for-elementor' ),
				'center center' => _x( 'Center Center', 'Transform origin', 'e-addons-for-elementor' ),
				'center left' => _x( 'Center Left', 'Transform origin', 'e-addons-for-elementor' ),
				'center right' => _x( 'Center Right', 'Transform origin', 'e-addons-for-elementor' ),
				'top center' => _x( 'Top Center', 'Transform origin', 'e-addons-for-elementor' ),
				'top left' => _x( 'Top Left', 'Transform origin', 'e-addons-for-elementor' ),
				'top right' => _x( 'Top Right', 'Transform origin', 'e-addons-for-elementor' ),
				'bottom center' => _x( 'Bottom Center', 'Transform origin', 'e-addons-for-elementor' ),
				'bottom left' => _x( 'Bottom Left', 'Transform origin', 'e-addons-for-elementor' ),
				'bottom right' => _x( 'Bottom Right', 'Transform origin', 'e-addons-for-elementor' ),
				//'initial' => _x( 'Custom', 'Transform origin', 'e-addons-for-elementor' ),

			],
			'selectors' => [
				'{{SELECTOR}} > *:first-child' => 'transform-origin: {{VALUE}}; -webkit-transform-origin: {{VALUE}};',
			],
		];
        /*
        $controls['transform-origin-position'] = [
            'label' => _x( 'Transform origin', 'Transform', 'e-addons-for-elementor' ),
            'type' => 'position',
            'responsive' => true,
            'render_type' => 'ui',
            'condition' => [
                'transform_type' => 'custom',
            ],
            'selectors' => [
                '{{SELECTOR}} > *:first-child' => 'transform-origin: {{X}}% {{Y}}%; -webkit-transform-origin: {{X}}% {{Y}}%;',
            ],
        ];
        */
        /*
        $controls['transformx'] = [
			'label' => _x( 'Origin X', 'Transform', 'e-addons-for-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'responsive' => true,
			'size_units' => [ '%' ],
			'default' => [
				'unit' => '%',
				'size' => 0,
			],
			'tablet_default' => [
				'unit' => '%',
				'size' => 0,
			],
			'mobile_default' => [
				'unit' => '%',
				'size' => 0,
			],
			'range' => [
				'%' => [
					'min' => -100,
					'max' => 100,
				],
			],
			'selectors' => [
				'{{SELECTOR}} > *:first-child' => 'transform-origin: {{SIZE}}% {{transformy.SIZE}}%; -webkit-transform-origin: {{SIZE}}% {{transformy.SIZE}}%;',
            ],
            'condition' => [
				'position' => [ 'initial' ],
			],
			'required' => true,
			'device_args' => [
				Controls_Stack::RESPONSIVE_TABLET => [
					'selectors' => [
						'{{SELECTOR}} > *:first-child' => 'transform-origin: {{SIZE}}% {{transformy_tablet.SIZE}}%; -webkit-transform-origin: {{SIZE}}% {{transformy_tablet.SIZE}}%;',
                    ],
                    'condition' => [
						'position_tablet' => [ 'initial' ],
					],
				],
				Controls_Stack::RESPONSIVE_MOBILE => [
					'selectors' => [
						'{{SELECTOR}} > *:first-child' => 'transform-origin: {{SIZE}}% {{transformy_mobile.SIZE}}%; -webkit-transform-origin: {{SIZE}}% {{transformy_mobile.SIZE}}%;',
                    ],
                    'condition' => [
						'position_mobile' => [ 'initial' ],
					],
				],
			],
		];
        $controls['transformy'] = [
			'label' => _x( 'Origin Y', 'Transform', 'e-addons-for-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'responsive' => true,
			'size_units' => [ '%' ],
			'default' => [
				'unit' => '%',
				'size' => 0,
			],
			'tablet_default' => [
				'unit' => '%',
				'size' => 0,
			],
			'mobile_default' => [
				'unit' => '%',
				'size' => 0,
			],
			'range' => [
				'%' => [
					'min' => -100,
					'max' => 100,
				]
			],
			'selectors' => [
				'{{SELECTOR}} > *:first-child' => 'transform-origin: {{transformx.SIZE}}{{transformx.UNIT}} {{SIZE}}{{UNIT}}; -webkit-transform-origin: {{transformx.SIZE}}{{transformx.UNIT}} {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
				'position' => [ 'initial' ],
			],
			'required' => true,
			'device_args' => [
				Controls_Stack::RESPONSIVE_TABLET => [
					'selectors' => [
						'{{SELECTOR}} > *:first-child' => 'transform-origin: {{transformx_tablet.SIZE}}{{transformx_tablet.UNIT}} {{SIZE}}{{UNIT}}; -webkit-transform-origin: {{transformx_tablet.SIZE}}{{transformx_tablet.UNIT}} {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
						'position_tablet' => [ 'initial' ],
					],
				],
				Controls_Stack::RESPONSIVE_MOBILE => [
					'selectors' => [
						'{{SELECTOR}} > *:first-child' => 'transform-origin: {{transformx_mobile.SIZE}}{{transformx_mobile.UNIT}} {{SIZE}}{{UNIT}}; -webkit-transform-origin: {{transformx_mobile.SIZE}}{{transformx_mobile.UNIT}} {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
						'position_mobile' => [ 'initial' ],
					],
				],
			],
		];
        */
        
        /*
        $controls['perspective-origin'] = [
            'label' => _x( 'Perspective origin', 'Perspective-origin x/y Control', 'elementor' ),
            'type' => 'position',
            'condition' => [
                'transform_type' => 'custom',
            ],
            'selectors' => [
                '{{SELECTOR}} > *:first-child' => 'perspective-origin: {{X}}% {{Y}}%; -webkit-perspective-origin:: {{X}}% {{Y}}%;',
            ],
        ];
        */
        
        return $controls;
    }
    
    protected function prepare_fields( $fields ) {
        //var_dump($fields);
        //echo 'pppp-';
        array_walk( $fields, function ( &$field, $field_name ) {
            if ( in_array( $field_name, [ 'transform_element', 'popover_toggle' ] ) ) {
                return;
            }
            /*
            //echo $field_name.'<br>';
            if($field_name == 'transform'){
                //echo '------> {{VALUE}}';
                $selector_value = 'transform: ';
               
                $valore_angle = '';
                $valore_rotatex = '';
                $valore_rotatey = '';
                $valore_scale = '';
                $valore_translatex = '';
                $valore_translatey = '';
                $valore_translatez = '';


                $dato_angle = '{{ANGLE}}';
                $dato_rotatex = '{{ROTATE_X}}';
                $dato_rotatey = '{{ROTATE_Y}}';
                $dato_scale = '{{SCALE}}';
                $dato_translatex = '{{TRANSLATE_X}}';
                $dato_translatey = '{{TRANSLATE_Y}}';
                $dato_translatez = '{{TRANSLATE_Z}}';
                if($dato_angle != '') $valore_angle = ' rotateZ({{ANGLE}}deg)';
                //echo 'v: '.var_export($dato_angle);
                //echo '{{VALUE.SCALE}}';
                $field['selectors'] = [
                    '{{SELECTOR}} > *:first-child' => 'transform:'.$valore_angle.' rotateX({{ROTATE_X}}deg) rotateY({{ROTATE_Y}}deg) scale({{SCALE}}) translateX({{TRANSLATE_X}}px) translateY({{TRANSLATE_Y}}px) translateZ({{TRANSLATE_Z}}px);',
                ];
                
            }
            
            if(isset($field['selector_value'])){
                
                $field['selectors'] = [
                    '{{SELECTOR}} > *:first-child' => $selector_value.';',
                ];
            }*/
            /*$field['condition'] = [
                'transform_element' => 'custom',
            ];*/
        } );

        return parent::prepare_fields( $fields );
    }

    protected function get_default_options() {
        return [
            'popover' => false,
            /*'popover' => [
                'starter_title' => _x( 'Transform', 'Transform Control', 'e-addoons' ),
                'starter_name' => 'transform_element',
            ],*/
        ];
    }
    
}
