<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;

/**
 * Description of label
 *
 * @author fra
 */
trait Items_Content {
    /*
      POSTS:
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
      USERS:
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
      TERMS:
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
      ITEMS:
      item_image (or icon)
      item_date
      item_title
      item_subtitle
      item_descriptiontext
      item_readmore
     */

    public function controls_items_image_content($target, $type = '') {
        if (!$type) {
            $type = $this->get_querytype();
        }

        //@p se mi trovo in post scelgo tra Featured o Custom image 
        //@p se mi trovo in user scelgo tra Avatar o Custom image
        //@p se mi trovo in repeater scego il subField repeater
        if ($type == 'post' || $type == 'user') {
            //@p questa è solo l'etichetta string
            if ($type == 'post') {
                $defIm = 'featured';
            } else if ($type == 'user') {
                $defIm = 'avatar';
            }
            
            $target->add_control(
                'image_type', [
                    'label' => __('Image type', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'featuredimage' => __(ucfirst($defIm . ' image'), 'e-addons'),
                        'customimage' => __('Custom meta image', 'e-addons'),
                    ],
                    'default' => $defIm . 'image',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                            ]
                        ]
                    ]
                ]
            );

            $target->add_control(
                    'image_custom_metafield', [
                'label' => __('Image Meta Field', 'e-addons'),
                'type' => 'e-query',
                'placeholder' => __('Meta key', 'e-addons'),
                'label_block' => true,
                'query_type' => 'metas',
                'object_type' => $type,
                'separator' => 'after',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'operator' => 'in',
                            'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                        ],
                        [
                            'name' => 'image_type',
                            'value' => 'customimage'
                        ]
                    ]
                ]
                    ]
            );
        } else if ($type == 'term') {
            //@p altrimeti in termine è solo la custom
            $target->add_control(
                    'image_custom_metafield', [
                'label' => __('Image Meta Field', 'e-addons'),
                'type' => 'e-query',
                'placeholder' => __('Meta key', 'e-addons'),
                'label_block' => true,
                'query_type' => 'metas',
                'object_type' => $type,
                'separator' => 'after',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'operator' => 'in',
                            'value' => ['item_image', 'item_imageoricon']
                        ]
                    ]
                ]
                    ]
            );
        }
        $target->add_control(
            'image_content_heading', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' => '<i class="fas fa-image"></i> <b>' . __('Image', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'separator' => 'before',
                'condition' => [
                    'item_type' => 'item_imageoricon'
                ]
            ]
        );
        $target->add_group_control(
                Group_Control_Image_Size::get_type(), [
            'name' => 'thumbnail_size',
            'label' => __('Image Format', 'e-addons'),
            'default' => 'large',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon']
                    ]
                ]
            ]
                ]
        );
        if ($type == 'user') {
            $target->add_control(
                    'avatar_size', [
                'label' => __('Avatar size', 'e-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 200,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'operator' => 'in',
                            'value' => ['item_avatar', 'item_imageoricon']
                        ]
                    ]
                ]
                    ]
            );
        }
        
        $target->add_responsive_control(
                'ratio_image', [
            'label' => __('Image Ratio', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0.1,
                    'max' => 2,
                    'step' => 0.1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-img' => 'padding-bottom: calc( {{SIZE}} * 100% );', '{{WRAPPER}}:after' => 'content: "{{SIZE}}";',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ],
                    [
                        'name' => 'use_bgimage',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
        $target->add_responsive_control(
                'width_image', [
            'label' => __('Image Width', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%', 'px', 'vw'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1
                ],
                'vw' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1
                ],
                'px' => [
                    'min' => 1,
                    'max' => 800,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}:not(.e-add-timeline) .e-add-post-image, {{WRAPPER}} .e-add-timeline {{CURRENT_ITEM}} .e-add-post-image img' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ],
                    [
                        'name' => 'use_bgimage',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'use_bgimage', [
            'label' => __('Background Image', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'render_type' => 'template',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ]
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-image-area, {{WRAPPER}}.e-add-posts-layout-default .e-add-post-bgimage' => 'position: relative;',
            ],
                ]
        );
        $target->add_responsive_control(
                'height_bgimage', [
            'label' => __('Height', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','%','vh'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 800,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-post-image.e-add-post-bgimage' => 'height: {{SIZE}}{{UNIT}};'
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ],
                    [
                        'name' => 'use_bgimage',
                        'operator' => '!=',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'use_overlay', [
            'label' => __('Overlay', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'prefix_class' => 'overlayimage-',
            'render_type' => 'template',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ]
                ]
            ]
                ]
        );
        $target->add_group_control(
                Group_Control_Background::get_type(), [
            'name' => 'overlay_color',
            'label' => __('Background', 'e-addons'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-post-image.e-add-post-overlayimage:after',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ],
                    [
                        'name' => 'use_overlay',
                        'operator' => '!==',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
        $target->add_responsive_control(
                'overlay_opacity', [
            'label' => __('Opacity (%)', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 0.7,
            ],
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-post-image.e-add-post-overlayimage:after' => 'opacity: {{SIZE}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ],
                    [
                        'name' => 'use_overlay',
                        'operator' => '!==',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
    }
    // ----------------------------------------------------------
    public function controls_items_icon_content($target) {
        //Icon color-size
        $target->add_control(
                'icon_content_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-star"></i> <b>' . __('Icon', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
            'condition' => [
                'item_type' => 'item_imageoricon'
            ]
                ]
        );
        

        $target->add_control(
                'color_item_iconorimage', [
            'label' => __('Icon Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-query-icon' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'item_type' => 'item_imageoricon'
            ]

                ]
        );
        $target->add_responsive_control(
                'icon_size_imageoricon', [
            'label' => __('Icon size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 10,
                    'max' => 100,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}.e-add-item_imageoricon .e-add-query-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} {{CURRENT_ITEM}}.e-add-item_imageoricon svg' => 'width: {{SIZE}}%;',

            ],
            'condition' => [
                'item_type' => 'item_imageoricon'
            ]
                ]
        );
        
    }
    // +********************* Post: Title / Term: Title / User: User,Role,FirstName, LastName, DisplayName, NickName
    public function controls_items_title_content($target, $type = '') {
        if (!$type) {
            $type = $this->get_querytype();
        }
        $defval = 'h3';
        if ($type == 'user') {
            $defval = '';
        }
        $target->add_control(
                'html_tag', [
            'label' => __('HTML Tag', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'h1' => __('H1', 'e-addons'),
                'h2' => __('H2', 'e-addons'),
                'h3' => __('H3', 'e-addons'),
                'h4' => __('H4', 'e-addons'),
                'h5' => __('H5', 'e-addons'),
                'h6' => __('H6', 'e-addons'),
                'p' => __('p', 'e-addons'),
                'div' => __('div', 'e-addons'),
                'span' => __('span', 'e-addons'),
            ],
            'default' => $defval,
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_title',
                            'item_date',
                            'item_subtitle',
                            'item_user',
                            'item_role',
                            'item_firstname',
                            'item_lastname',
                            'item_displayname',
                            'item_nickname',
                            'item_email',
                            'item_website',
                            'item_alternativetext',
                            'item_caption',
                            'item_mimetype',
                            'item_counts',
                            'item_uploadedto'
                        ],
                    ]
                ]
            ]
                ]
        );
    }

    // +********************* Post: Content/Excerpt / term: Description / User: Biography-Description
    public function controls_items_contentdescription_content($target, $type = '') {
        if (!$type) {
            $type = $this->get_querytype();
        }

        $target->add_control(
                'textcontent_limit', [
            'label' => __('Number of characters', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'description' => __('Leave Empty for all text.', 'e-addons'),
            'min' => 1,
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_content', 'item_excerpt', 'item_bio', 'item_description'],
                    ]
                ]
            ]
                ]
        );
    }

    // +********************* Terms of Taxonomy [metadata] (Category, Tag, CustomTax)
    public function controls_items_termstaxonomy_content($target) {
        $taxonomies = Utils::get_taxonomies();
        $target->add_control(
                'separator_chart', [
            'label' => __('Separator', 'e-addons'),
            //'description' => __('Separator caracters.','e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => '/',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_termstaxonomy'
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'only_parent_terms', [
            'label' => __('Show only', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'both' => [
                    'title' => __('Both', 'e-addons'),
                    'icon' => 'fa fa-sitemap',
                ],
                'yes' => [
                    'title' => __('Parents', 'e-addons'),
                    'icon' => 'fa fa-female',
                ],
                'children' => [
                    'title' => __('Children', 'e-addons'),
                    'icon' => 'fa fa-child',
                ]
            ],
            'toggle' => false,
            'default' => 'both',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_termstaxonomy',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'block_enable', [
            'label' => __('Block', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'block',
            'render_type' => 'template',
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-term-item' => 'display: {{VALUE}}'
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_termstaxonomy',
                        //'item_date',
                        ],
                    ]
                ]
            ]
                ]
        );

        $target->add_control(
                'taxonomy_filter', [
            'label' => __('Filter Taxonomy', 'e-addons'),
            'type' => Controls_Manager::SELECT2,
            'separator' => 'before',
            'label_block' => true,
            'multiple' => true,
            'options' => $taxonomies,
            'placeholder' => __('Auto', 'e-addons'),
            'description' => __('Use only terms in selected taxonomies. If empty all terms will be used.', 'e-addons'),
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_termstaxonomy',
                    ]
                ]
            ]
                ]
        );
    }

    // +********************* Date
    public function controls_items_date_content($target, $type = '') {
        if (!$type) {
            $type = $this->get_querytype();
        }
        if ($type == 'post') {
            $target->add_control(
                    'date_type', [
                'label' => __('Date Type', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'publish' => __('Publish Date', 'e-addons'),
                    'modified' => __('Last Modified Date', 'e-addons'),
                ],
                'default' => 'publish',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'value' => 'item_date',
                        ]
                    ]
                ]
                    ]
            );
        }
        // added block_enable
        $target->add_control(
                'date_format', [
            'label' => __('Date Format', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => 'd/<b>m</b>/y',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_date', 'item_registered'],
                    ]
                ]
            ]
                ]
        );
    }

    // +********************* ReadMore
    public function controls_items_readmore_content($target) {
        $target->add_control(
                'readmore_text', [
            'label' => __('Text', 'e-addons'),
            //'description' => __('Separator caracters.','e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Read More', 'e-addons'),
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_readmore',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'readmore_size',
                [
                    'label' => __('Size', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'sm',
                    'options' => Query_Utils::get_button_sizes(),
                    'style_transfer' => true,
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_readmore',
                            ]
                        ]
                    ]
                ]
        );
    }

    // +********************* Author-box user
    public function controls_items_authorbox_content($target) {
        $target->add_control(
                'author_displayname', [
            'label' => __('Show Name', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_author',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'author_bio', [
            'label' => __('Show biography', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_author',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'author_image', [
            'label' => __('Show Avatar', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_author',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'author_image_size', [
            'label' => __('Avatar size', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '50',
            'render_type' => 'template',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_author',
                    ],
                    [
                        'name' => 'author_image',
                        'value' => 'yes',
                    ]
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-author-image .e-add-img' => 'width: {{VALUE}}px; height: {{VALUE}}px;',
            ]
                ]
        );
        $target->add_control(
                'author_user_key',
                [
                    'label' => __('Custom Field', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => __('Search User Custom Field', 'e-addons'),
                    'label_block' => true,
                    'multiple' => true,
                    'separator' => 'after',
                    'query_type' => 'fields',
                    'object_type' => 'user',
                    'default' => [],
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_author',
                            ]
                        ]
                    ]
                ]
        );
    }

    // +********************* Post Type
    public function controls_items_posttype_content($target) {
        $target->add_control(
                'posttype_label', [
            'label' => __('Post Type Label ', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => 'plural',
            'options' => [
                'plural' => __('Plural', 'e-addons'),
                'singular' => __('Singular', 'e-addons'),
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_posttype',
                    ]
                ]
            ]
                ]
        );
    }

    // +********************* Teaxonomy of term
    public function controls_items_taxonomy_content($target) {
        $target->add_control(
                'taxonomy_label', [
            'label' => __('Taxonomy Label', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => 'plural',
            'options' => [
                'plural' => __('Plural', 'e-addons'),
                'singular' => __('Singular', 'e-addons'),
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_taxonomy',
                    ]
                ]
            ]
                ]
        );
    }

    public function controls_items_imagemeta_content($target) {
        $target->add_control(
                'imagemedia_sizes', [
            'label' => __('Image Size', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => 'full',
            'options' => Query_Utils::get_available_image_sizes_options(),
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_imagemeta',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'imagemedia_metas', [
            'label' => __('Show Additional Info', 'e-addons'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'place_holder' => 'Select Additional Info',
            'default' => ['dimension'],
            'options' => [
                'dimension' => 'Dimension',
                'file' => 'File name',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_imagemeta',
                    ]
                ]
            ]
                ]
        );
    }

    // +********************* Template item
    public function controls_items_template_content($target) {
        //
        $target->add_control(
                'template_item_id',
                [
                    'label' => __('Template', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => __('Search Template', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'render_type' => 'template',
                    'object_type' => 'elementor_library',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_template',
                            ]
                        ]
                    ]
                ]
        );
    }

}
