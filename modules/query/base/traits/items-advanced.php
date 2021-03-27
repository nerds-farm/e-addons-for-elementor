<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use EAddonsForElementor\Core\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

/**
 * Description of label
 *
 * @author fra
 */
trait Items_Advanced {

    // NOTA: sono esclusi da advanced gli item_type
    // query-post: 'item_author','item_readmore','item_custommeta'
    // query-term: 'item_readmore','item_custommeta'
    // query-user: 'item_readmore','item_custommeta'
    // query-items: 'item_readmore'
    // ----------------------------------------------------------    
    public function controls_items_advanced($target) {
        //use_Link, Display(block,inline)
        $options = [
                '' => __('None'),
                'yes' => __('Current'),                
                'shortcode' => __('Shortcode'),
            ];
        if (Utils::is_plugin_active('elementor-pro') && Utils::is_plugin_active('e-addons-extended')) {
            $options['popup'] = __('Open PopUp');
        }
        $target->add_control(
                'use_link', [
            'label' => '<i class="fas fa-link"></i> ' . __('Use link', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => $options,
            'default' => 'yes',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => '!in',
                        'value' => [
                            'item_posttype',
                            'item_date',
                            'item_registered',
                            //'item_readmore',
                            'item_termstaxonomy',
                            'item_content',
                            'item_excerpt',
                            'item_description',
                            'item_taxonomy',
                            'item_custommeta',
                            'item_caption',
                            'item_alternativetext',
                            'item_imagemeta',
                            'item_mimetype',
                            'item_counts'
                        ],
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'shortcode_link', [
            'label' => __('Shortcode link', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'use_link' => 'shortcode',
            ]
                ]
        );
        if (Utils::is_plugin_active('elementor-pro') || Utils::is_plugin_active('e-addons-extended')) {
            $target->add_control(
                    'popup_link', [
                'label' => __('Open PopUp', 'e-addons'),
                'type' => 'e-query',
                'placeholder' => __('Select PopUp', 'e-addons'),
                'label_block' => true,
                'query_type' => 'posts',
                'object_type' => 'elementor_library',
                'condition' => [
                    'use_link' => 'popup',
                ]
                    ]
            );
        }
        
        $target->add_control(
                'display_inline', [
            'label' => '<i class="fas fa-clipboard-list"></i> ' . __('Display', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'default' => '',
            'toggle' => true,
            'options' => [
                'block' => [
                    'title' => __('Block', 'e-addons'),
                    'icon' => 'fas fa-bars',
                ],
                'inline-block' => [
                    'title' => __('Inline', 'e-addons'),
                    'icon' => 'fas fa-ellipsis-h',
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'display: {{VALUE}}; vertical-align: middle;',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_title',
                            'item_date',
                            'item_registered',
                            'item_posttype',
                            'item_taxonomy',
                            'item_user',
                            'item_role',
                            'item_bio',
                            'item_firstname',
                            'item_lastname',
                            'item_displayname',
                            'item_nickname',
                            'item_email',
                            'item_label',
                            'item_counts',
                            'item_caption',
                            'item_alternativetext',
                            'item_imagemeta',
                            'item_mimetype',
                            'item_uploadedto'
                        ],
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_custommeta',
                            ],
                            [
                                'name' => 'metafield_type',
                                'operator' => 'in',
                                'value' => ['file', 'text', 'number', 'image', 'array']
                            ]
                        ]
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'icon_enable', [
            'label' => __('Icon', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_termstaxonomy',
                            'item_date',
                            'item_registered',
                        ],
                    ]
                ]
            ]
                ]
        );
        /*
          item_avatar
          item_displayname
          item_user
          item_role
          item_firstname
          item_lastname
          item_nickname
          item_email
          item_website
          item_bio
          item_custommeta
          item_readmore
          item_label
         */

        /*
          item_image
          item_date
          item_title
          item_termstaxonomy
          item_content
          item_author
          item_readmore
          item_posttype
          item_custommeta
          item_label
          todo: commets...
         */

        /*
          item_image
          item_title
          item_taxonomy
          item_counts
          item_description
          item_readmore
          item_custommeta
          item_label
         */

        /*
          item_image (or icon)
          item_date
          item_title
          item_subtitle
          item_descriptiontext
          item_readmore
         */

        $target->add_control(
                'use_label_before', [
            'label' => '<i class="fas fa-minus"></i> ' . __('Before', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'description' => __('Print Label before the Field', 'e-addons'),
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_date', 
                            'item_registered',
                            'item_termstaxonomy', 
                            'item_posttype', 
                            'item_counts',
                            'item_displayname',
                            'item_user',
                            'item_role',
                            'item_firstname',
                            'item_lastname',
                            'item_nickname',
                            'item_email',
                            'item_website',
                            'item_bio',
                            'item_counts',
                            'item_caption',
                            'item_alternativetext',
                            'item_imagemeta',
                            'item_mimetype',
                            'item_uploadedto'
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_custommeta',
                            ],
                            [
                                'name' => 'metafield_type',
                                'operator' => 'in',
                                'value' => ['text', 'number', 'array']
                            ]
                        ]
                    ]
                ],
            ]
                ]
        );
        $target->add_control(
                'use_label_after', [
            'label' => __('After', 'e-addons') . ' <i class="fas fa-minus"></i>',
            'type' => Controls_Manager::TEXT,
            'description' => __('Print text after the Field', 'e-addons'),
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_date', 
                            'item_registered',
                            'item_termstaxonomy', 
                            'item_posttype', 
                            'item_counts',
                            'item_displayname',
                            'item_user',
                            'item_role',
                            'item_firstname',
                            'item_lastname',
                            'item_nickname',
                            'item_email',
                            'item_website',
                            'item_bio',
                            'item_counts',
                            'item_caption',
                            'item_alternativetext',
                            'item_imagemeta',
                            'item_mimetype',
                            'item_uploadedto'
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_custommeta',
                            ],
                            [
                                'name' => 'metafield_type',
                                'operator' => 'in',
                                'value' => ['text', 'number', 'array']
                            ]
                        ]
                    ]
                ],
            ]
                ]
        );
        $target->add_control(
                'use_fallback', [
            'label' => __('Fallback', 'e-addons') ,
            'type' => Controls_Manager::TEXT,
            'description' => __('Alternative Text for empty values', 'e-addons'),
            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_custommeta',
                    ],
                    [
                        'name' => 'metafield_type',
                        'operator' => 'in',
                        'value' => ['text', 'number', 'array']
                    ]
                ],
            ]
                ]
        );
        $target->add_control(
                'use_fallback_img', [
            'label' => __('Fallback', 'e-addons') ,
            'type' => Controls_Manager::MEDIA,
            'description' => __('Alternative Image for empty values', 'e-addons'),
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_image', 
                            'item_avatar',
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_custommeta',
                            ],
                            [
                                'name' => 'metafield_type',
                                'operator' => 'in',
                                'value' => ['image']
                            ]
                        ]
                    ]
                ],
            ]
                ]
        );
    }

}
