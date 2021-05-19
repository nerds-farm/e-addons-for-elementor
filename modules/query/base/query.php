<?php

namespace EAddonsForElementor\Modules\Query\Base;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;
use EAddonsForElementor\Core\Controls\Groups\Transform;
use EAddonsForElementor\Core\Controls\Groups\Masking;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Query
 *
 * Elementor widget for E-Addons
 *
 */
class Query extends Base_Widget {

    use Traits\Common;
    use Traits\Pagination;
    use Traits\Infinite_Scroll;
    use Traits\Custommeta;
    use Traits\Label;
    use Traits\Items_Content;
    use Traits\Items_Style;
    use Traits\Items_Advanced;
    use Traits\Repeater;

    //@ questa è una variabile globale che memorizza la query in corso
    protected $query = null;
    //@ questa è una variabile globale che memorizza se la query è: 1-post, 2-user, 3-term, 4-repeater_list 5-items 
    protected $querytype = null;
    //@ questo serve a rimuovere lo skin default perché non voglio fare nessun render direttamente nel widget
    protected $_has_template_content = false;

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
    }

    public function get_name() {
        return 'e-query-base';
    }

    public function get_categories() {
        return ['query'];
    }

    public function get_script_depends() {
        return [
            'jquery-fitvids',
            'infiniteScroll',
            'e-addons-frontend-query',
            'e-addons-query-base',
        ];
    }

    //
    public function get_style_depends() {

        return [
            /* 'font-awesome-5-all', */'font-awesome', 'elementor-icons-fa-solid', 'animatecss',
        ];
    }

    //@ questo metodo restituisce la query in corso
    public function get_query() {
        return $this->query;
    }

    //@ questo metodo restituisce il tipo di query in corso
    public function get_querytype() {
        return $this->querytype;
    }

    protected function _register_controls() {

        $taxonomies = Utils::get_taxonomies();
        $types = Utils::get_post_types();
        //$templates = Utils::get_all_template();

        $this->start_controls_section(
                'section_e_query', [
            'label' => '<i class="eaddicon ' . $this->get_icon() . '"></i><i class="eadd-logo-e-addons eadd-ic-right"></i> ' . $this->get_title(),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );
        // skin: Template
        $this->add_control(
                'skin_dis_customtemplate',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-circle"></i><i class="eaddicon-skin eicon-elementor-square"></i>',
                    //'raw' => '<img src="'.E_ADDONS_QUERY_URL . 'assets/img/skins/template.png'.'" />',
                    'content_classes' => 'e-add-skin-dis e-add-ect-dis',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider'],
                        'style_items' => 'template',
                    ],
                ]
        );
        // skin: Row
        /* $this->add_control(
          'skin_dis_default',
          [
          'type' => Controls_Manager::RAW_HTML,
          'show_label' => false,
          'raw' => '<img src="'.$this->get_module_url() . 'assets/img/skins/default.png'.'" />',
          'content_classes' => 'e-add-skin-dis',
          'condition' => [
          '_skin' => 'row'
          ],
          ]
          ); */
        // skin: Grid
        $this->add_control(
                'skin_dis_grid',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-queryviews-grid"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'grid'
                    ],
                ]
        );
        // skin: Carousel
        $this->add_control(
                'skin_dis_carousel',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-queryviews-carousel"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'carousel'
                    ],
                ]
        );
        // skin: Filters
        $this->add_control(
                'skin_dis_filters',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-queryviews-filters"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'filters'
                    ],
                ]
        );
        // skin: Table
        $this->add_control(
                'skin_dis_table',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-skin-table"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'table'
                    ],
                ]
        );
        
        // skin: simple list
        $this->add_control(
                'skin_dis_simplelist',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-skin-simple-list"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'list'
                    ],
                ]
        );
         // skin: horizontal scroll
         $this->add_control(
            'skin_dis_horizontalscroll',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-scroll-horizzontal"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'horizontalscroll'
                    ],
                ]
        );
        // skin: cards
        $this->add_control(
            'skin_dis_cards',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-queryviews-cards"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'cards'
                    ],
                ]
        );
        // skin: circular
        $this->add_control(
            'skin_dis_circular',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-queryviews-circular"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'circular'
                    ],
                ]
        );
        // skin: piling
        $this->add_control(
            'skin_dis_piling',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-queryviews-piling"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'piling'
                    ],
                ]
        );

        // skin: Dualslider
        $this->add_control(
                'skin_dis_dualslider',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-queryviews-dualslider"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'dualslider'
                    ],
                ]
        );
        // skin: timeline
        $this->add_control(
                'skin_dis_timeline',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-queryviews-timeline"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'timeline'
                    ],
                ]
        );
        // skin: nextpost
        $this->add_control(
                'skin_dis_nextpost',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-queryviews-next-post"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'nextpost'
                    ],
                ]
        );
        // skin: Justified Gallery
        $this->add_control(
                'skin_dis_justifiedgrid',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-gallery-grid-justified"></i>',
                    'content_classes' => 'e-add-skin-dis',
                    'condition' => [
                        '_skin' => 'justifiedgrid'
                    ],
                ]
        );
        // skin: pagination classic
        $this->add_control(
                'skin_dis_pagination',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-numeric-pagination"></i>',
                    //'raw' => '<img src="'.E_ADDONS_QUERY_URL . 'assets/img/skins/pagination.png'.'" />',
                    'content_classes' => 'e-add-skin-dis e-add-pagination-dis',
                    'condition' => [
                        //@p il massimo è che la paginazione funzioni con tutti gli skins...
                        //'_skin' => ['', 'grid', 'filters', 'timeline'],
                        'pagination_enable' => 'yes',
                        'infiniteScroll_enable' => ''
                    ],
                ]
        );
        // skin: infinitescroll
        $this->add_control(
                'skin_dis_infinitescroll',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-infinite-pagination"></i>',
                    //'raw' => '<img src="'.E_ADDONS_QUERY_URL . 'assets/img/skins/infinitescroll.png'.'" />',
                    'content_classes' => 'e-add-skin-dis e-add-pagination-dis',
                    'condition' => [
                        '_skin' => ['', 'grid', 'list', 'table', 'filters', 'timeline'],
                        'pagination_enable' => 'yes',
                        'infiniteScroll_enable' => 'yes'
                    ],
                ]
        );

        //@p qui infilo i controllo relativamente agli items..
        $this->items_query_controls();

        //@p qui infilo i controllo relativamente a query-repeater..
        $this->repeater_query_controls();

        $this->add_control(
                'heading_pagination',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-pager"></i> &nbsp;' . __('PAGINATION:', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        //@p il massimo è che la paginazione funzioni con tutti gli skins...
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'justifiedgrid', 'gridtofullscreen3d'],
                        'infiniteScroll_enable' => '',
                        'query_type' => ['automatic_mode', 'get_cpt', 'get_tax', 'get_users_and_roles', 'get_attachments']
                    ],
                ]
        );
        //@p questo metodo produce i 2 switcher per abilitare la paginazione 8in caso di items_list è vuoto)
        $this->paginations_enable_controls();

        $this->end_controls_section();

        // ------------------------------------------------------------------ [SECTION LAYOUTS BLOCKS ]
        $this->start_controls_section(
                'section_layout_blocks', [
            'label' => '<i class="eaddicon eicon-info-box" aria-hidden="true"></i> ' . __('Block Layout', 'e-addons'),
            'condition' => [
                '_skin!' => ['justifiedgrid', 'timeline', 'nextpost', 'table', 'list', 'piling'],
            ],
                ]
        );
        // ------------------------------------
        $this->add_control(
                'style_items', [
            'label' => __('Items Style', 'e-addons'),
            'type' => 'ui_selector',
            'label_block' => true,
            'toggle' => false,
            'type_selector' => 'image',
            'columns_grid' => 4,
            'separator' => 'before',
            'options' => [
                /* '' => [
                  'title' => __('Default','e-addons'),
                  'return_val' => 'val',
                  'image' => E_ADDONS_QUERY_URL . 'assets/img/layout/default.png',
                  ], */
                'default' => [
                    'title' => __('Default', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/top.png',
                ],
                'left' => [
                    'title' => __('Left', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/left.png',
                ],
                'right' => [
                    'title' => __('Right', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/right.png',
                ],
                'alternate' => [
                    'title' => __('Alternate', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/alternate.png',
                ],
                'textzone' => [
                    'title' => __('Text Zone', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/textzone.png',
                ],
                'overlay' => [
                    'title' => __('Overlay', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/overlay.png',
                ],
                'float' => [
                    'title' => __('Float', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/float.png',
                ],
                'template' => [
                    'title' => __('Elementor Template', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/template.png',
                ],
            ],
            'toggle' => false,
            'render_type' => 'template',
            'prefix_class' => 'e-add-posts-layout-', // ....da cambiare ......
            'default' => 'default',
            //'tablet_default' => '',
            //'mobile_default' => '',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
            ],
                ]
        );
        // +********************* Style: Left, Right, Alternate
        $this->add_responsive_control(
                'image_rate', [
            'label' => __('Distribution (%)', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '50',
                'unit' => '%',
            ],
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-image-area' => 'width: {{SIZE}}%;',
                '{{WRAPPER}} .e-add-content-area' => 'width: calc( 100% - {{SIZE}}% );',
            ],
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items' => ['left', 'right', 'alternate'],
            ],
                ]
        );

        // +********************* Float Hover style descripton:
        $this->add_control(
                'float_hoverstyle_description',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon eicon-image-rollover" aria-hidden="true"></i> ' . __('Float style allows you to create animations between the content and the underlying image, from "Hover effect" Panel you can set the features.', 'e-addons'),
                    'content_classes' => 'e-add-info-panel',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                        'style_items' => ['float'],
                    ],
                ]
        );
        // +********************* Image Zone Style:
        $this->add_control(
                'heading_imagezone',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="far fa-image"></i> &nbsp;' . __('IMAGE:', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                        'style_items!' => ['default', 'template'],
                    ],
                ]
        );
        // +********************* Image Zone: Mask
        $this->add_control(
                'imagemask_popover', [
            'label' => __('Mask', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => __('Default', 'e-addons'),
            'label_on' => __('Custom', 'e-addons'),
            'return_value' => 'yes',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items!' => ['default', 'template'],
            ],
                ]
        );
        $this->start_popover();
        $this->add_control(
                'mask_heading',
                [
                    'label' => __('Mask', 'e-addons'),
                    'description' => __('Shape Parameters', 'e-addons'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                        'style_items!' => ['default', 'template'],
                        'imagemask_popover' => 'yes',
                    ],
                ]
        );
        $this->add_group_control(
                Masking::get_type(),
                [
                    'name' => 'mask',
                    'label' => __('Mask', 'e-addons'),
                    'selector' => '{{WRAPPER}} .e-add-posts-container .e-add-post-image',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                        'style_items!' => ['default', 'template'],
                        'imagemask_popover' => 'yes',
                    ],
                ]
        );
        $this->end_popover();
        // +********************* Image Zone: Transforms
        $this->add_control(
                'imagetransforms_popover',
                [
                    'label' => __('Transforms', 'plugin-name'),
                    'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                    'return_value' => 'yes',
                    'render_type' => 'ui',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                        'style_items!' => ['default', 'template'],
                    ],
                ]
        );
        $this->start_popover();

        $this->add_group_control(
                Transform::get_type(),
                [
                    'name' => 'transform_image',
                    'label' => 'Transform image',
                    'selector' => '{{WRAPPER}} .e-add-post-item .e-add-image-area',
                    'separator' => 'before',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                        'style_items!' => ['default', 'template'],
                        'imagetransforms_popover' => 'yes',
                    ],
                ]
        );
        $this->end_popover();
        // +********************* Image Zone: Filters
        $this->add_group_control(
                Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'imagezone_filters',
                    'label' => 'Filters',
                    'render_type' => 'ui',
                    'selector' => '{{WRAPPER}} .e-add-post-block .e-add-post-image img',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                        'style_items!' => ['default', 'template'],
                    ],
                ]
        );
        // +********************* Content Zone Style:
        $this->add_control(
                'heading_contentzone',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-align-left"></i> &nbsp;' . __('CONTENT:', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                        'style_items!' => ['default', 'template'],
                    ],
                ]
        );
        // +********************* Content Zone: Style
        $this->add_control(
                'contentstyle_popover', [
            'label' => __('Style', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => __('Default', 'e-addons'),
            'label_on' => __('Custom', 'e-addons'),
            'return_value' => 'yes',
            'render_type' => 'ui',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items!' => ['default', 'template'],
            ],
                ]
        );
        $this->start_popover();
        $this->add_control(
                'contentzone_bgcolor', [
            'label' => __('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-content-area' => 'background-color: {{VALUE}};'
            ],
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items!' => ['default', 'template'],
                'contentstyle_popover' => 'yes',
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'contentzone_border',
            'selector' => '{{WRAPPER}} .e-add-post-item .e-add-content-area',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items!' => ['default', 'template'],
                'contentstyle_popover' => 'yes',
            ],
                ]
        );
        $this->add_responsive_control(
                'contentzone_padding', [
            'label' => __('Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-content-area' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items!' => ['default', 'template'],
                'contentstyle_popover' => 'yes',
            ],
                ]
        );
        $this->add_control(
                'contentzone_border_radius', [
            'label' => __('Border Radius', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            //'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-content-area' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items!' => ['default', 'template'],
                'contentstyle_popover' => 'yes',
            ],
                ]
        );

        $this->end_popover();

        // +********************* Content Zone Transform: Overlay, TextZone, Float
        $this->add_control(
                'contenttransform_popover', [
            'label' => __('Transform', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => __('Default', 'e-addons'),
            'label_on' => __('Custom', 'e-addons'),
            'return_value' => 'yes',
            'render_type' => 'ui',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items' => ['overlay', 'textzone', 'float'],
            ],
                ]
        );
        $this->start_popover();
        $this->add_responsive_control(
                'contentzone_x', [
            'label' => __('X', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%'],
            'default' => [
                'size' => '',
                'unit' => '%',
            ],
            'range' => [
                '%' => [
                    'min' => -100,
                    'max' => 100,
                    'step' => 0.1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-content-area' => 'margin-left: {{SIZE}}%;',
            ],
            'condition' => [
                'contenttransform_popover' => 'yes',
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items' => ['overlay', 'textzone', 'float'],
            ],
                ]
        );
        $this->add_responsive_control(
                'contentzone_y', [
            'label' => __('Y', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => '%',
            ],
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => -100,
                    'max' => 100,
                    'step' => 0.1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-content-area' => 'margin-top: {{SIZE}}%;',
            ],
            'condition' => [
                'contenttransform_popover' => 'yes',
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items' => ['overlay', 'textzone', 'float'],
            ],
                ]
        );
        $this->add_responsive_control(
                'contentzone_width', [
            'label' => __('Width (%)', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => '%',
            ],
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 0.1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-content-area' => 'width: {{SIZE}}%;',
            ],
            'condition' => [
                'contenttransform_popover' => 'yes',
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items' => ['overlay', 'textzone', 'float'],
            ],
                ]
        );
        $this->add_responsive_control(
                'contentzone_height', [
            'label' => __('Height (%)', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => '%',
            ],
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 0.1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-content-area' => 'height: {{SIZE}}%;',
            ],
            'condition' => [
                'contenttransform_popover' => 'yes',
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items' => ['float'],
            ],
                ]
        );
        $this->end_popover();
        // +********************* Content Zone: BoxShadow 
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'contentzone_box_shadow',
            'selector' => '{{WRAPPER}} .e-add-post-item .e-add-content-area',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items!' => ['default', 'template'],
            ],
            'popover' => true
                ]
        );

        /* Responsive --------------- */
        $this->add_control(
                'force_layout_default', [
            'label' => '<i class="eaddicon eicon-device-mobile" aria-hidden="true"></i> ' . __('Force default layout on mobile', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'prefix_class' => 'force-default-mobile-',
            'condition' => [
                '_skin' => ['', 'grid', 'filters', 'carousel', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items' => ['left', 'right', 'alternate']
            ],
                ]
        );
        // +********************* Style: Elementor TEMPLATE 
        $this->add_control(
                'template_id',
                [
                    'label' => __('Template', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => __('Select Template', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'render_type' => 'template',
                    'object_type' => 'elementor_library',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                        'style_items' => 'template',
                    //'native_templatemode_enable' => ''
                    ],
                ]
        );
        $this->add_control(
                'templatemode_enable_2', [
            'label' => __('Template ODD', 'e-addons'),
            'description' => __('Enable a template to manage the appearance of the odd elements.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'render_type' => 'template',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items' => 'template',
            //'native_templatemode_enable' => '',
            ],
                ]
        );

        $this->add_control(
                'template_2_id',
                [
                    'label' => __('Template odd', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => __('Select Template', 'e-addons'),
                    'label_block' => true,
                    'show_label' => false,
                    'query_type' => 'posts',
                    'object_type' => 'elementor_library',
                    'render_type' => 'template',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                        'style_items' => 'template',
                        'templatemode_enable_2!' => '',
                    //'native_templatemode_enable' => ''
                    ],
                ]
        );

        /* $options = [
          '' => __('None'),
          'yes' => __('Current'),
          'shortcode' => __('Shortcode'),
          'custom' => __('Custom'),
          ];
          if (Utils::is_plugin_active('elementor-pro') && Utils::is_plugin_active('e-addons-extended')) {
          $options['popup'] = __('Open PopUp');
          } */
        $this->add_control(
                'templatemode_linkable', [
            'label' => __('Linkable', 'e-addons'),
            'description' => __('Extended link on the full block.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            //'options' => $options,
            'separator' => 'before',
            'render_type' => 'template',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'circular'],
                'style_items' => 'template',
            ],
                ]
        );
        $this->end_controls_section();

        $this->add_pagination_section();

        $this->add_infinite_scroll_section();
    }

    public function add_no_result_section() {
        //@p il TAB Query
        // ------------------------------------------------------------------ [SECTION - QUERY no_result]
        $this->start_controls_section(
                'section_query_no_result', [
            'label' => '<i class="eaddicon eicon-warning" aria-hidden="true"></i> ' . __('No results', 'e-addons'),
            'tab' => 'e_query',
                ]
        );
        $this->add_control(
                'query_no_result', [
            'label' => __('Print a Fallback Content', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
                ]
        );
        $this->add_control(
                'query_no_result_txt', [
            'label' => __('No results Text', 'e-addons'),
            'default' => __('Sorry, no results found', 'e-addons'),
            'type' => Controls_Manager::WYSIWYG,
            'condition' => [
                'query_no_result!' => '',
            ]
                ]
        );
        $this->end_controls_section();
    }

    // -------------- Render method ---------
    public function render() {
        $is_imagemask = $this->get_settings('imagemask_popover');
        if ($is_imagemask) {
            $mask_shape_type = $this->get_settings('mask_shape_type');

            //$this->render_svg_mask($mask_shape_type);
        }
    }

    // -------------- Override Laghtbox (assurdo ... ma inevitabile .. da valutare) ---------
    public function add_lightbox_data_attributes($element, $id = null, $lightbox_setting_key = null, $group_id = null, $overwrite = false) {
        $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();

        $is_global_image_lightbox_enabled = 'yes' === $kit->get_settings('global_image_lightbox');

        if ('no' === $lightbox_setting_key) {
            if ($is_global_image_lightbox_enabled) {
                $this->add_render_attribute($element, 'data-elementor-open-lightbox', 'no', true); //<-- @p !!savebbe da aggiungerre questo true per evitare il mio override.
            }

            return $this;
        }

        if ('yes' !== $lightbox_setting_key && !$is_global_image_lightbox_enabled) {
            return $this;
        }

        $attributes['data-elementor-open-lightbox'] = 'yes';

        if ($group_id) {
            $attributes['data-elementor-lightbox-slideshow'] = $group_id;
        }

        if ($id) {
            $lightbox_image_attributes = \Elementor\Plugin::$instance->images_manager->get_lightbox_image_attributes($id);

            if (!empty($lightbox_image_attributes['title'])) {
                $attributes['data-elementor-lightbox-title'] = $lightbox_image_attributes['title'];
            }

            if (!empty($lightbox_image_attributes['description'])) {
                $attributes['data-elementor-lightbox-description'] = $lightbox_image_attributes['description'];
            }
        }

        $this->add_render_attribute($element, $attributes, null, $overwrite);

        return $this;
    }

    // -------------- Methods ---------
    // @p questo metodo viene usato da items_list per igniettare gli elementi ripetitore 
    public function items_query_controls() {
        
    }
    // @p questo metodo viene usato da repeater per igniettare gli elementi ripetitore 
    public function repeater_query_controls() {
        
    }
    // il metodo (che viene ereditato) e che esegue le query su: POSTS - USERS - TERMS
    public function query_the_elements() {
        
    }

    protected function render_svg_mask($mask_shape_type) {
        $widgetId = $this->get_id();
        $shape_numbers = $this->get_settings('shape_numbers');

        /* $image_url = Group_Control_Image_Size::get_attachment_image_src($this->get_settings('mask_image')['id'], 'image', $settings);
          $image_masking_url = Group_Control_Image_Size::get_attachment_image_src($this->get_settings('image_masking')['id'], 'size_masking', $settings); */

        if ($this->get_settings('image_masking')['url']) {
            $image_masking_url = $this->get_settings('image_masking')['url'];
        }
    }

    protected function limit_content($limit) {
        
    }

    protected function limit_excerpt($limit) {
        
    }

    public function get_terms_query($settings = null, $id_page = null) {
        
    }

}
