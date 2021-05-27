<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;

/**
 * Description of custommmeta
 *
 * @author fra
 */
trait Custommeta {

    // -------------- Custommeta SOURCE query_type Posts/Users/Terms ---------
    //@p leggo un campo personalizzato di tipo relationship oppure users oppure terms nel post, user o termine in cuii mi trovo
    public function custommeta_source_items($target, $type_q = '') {
        if (!$type_q) {
            $type_q = $this->get_querytype();
        }

        /*$target->add_control(
                'avviso_meta_custommeta_source',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-exclamation-circle"></i> ' . __('Select the custom meta field', 'e-addons'),
                    'content_classes' => 'e-add-info-panel',
                    'condition' => [
                        'query_type' => 'custommeta_source',
                    ],
                ]
        );*/

        //@p qui seleziono il tipo di sorgente
        $target->add_control(
                'custommeta_source_querytype', [
            'label' => __('Custom Field from:', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => $type_q,
            'options' => [
                'post' => __('Post', 'e-addons'), //
                'term' => __('Term', 'e-addons'),
                'user' => __('User', 'e-addons'), //
                'attachment' => __('Media attachment', 'e-addons'), //
            //'comment' => __(Comment,'e-addons'),
            ],
            'condition' => [
                'query_type' => 'custommeta_source',
            ],
                ]
        );

        


        // @p custommeta_source from meta field ..non più tramite acf
        // ---------------- post
        $target->add_control(
                'custommeta_source_key_post', [
            'label' => __('Post Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search Post Custom Field', 'e-addons'),
            'query_type' => 'metas',
            'object_type' => 'post',
            'label_block' => true,
            'condition' => [
                'query_type' => 'custommeta_source',
                'custommeta_source_querytype' => 'post'
            ],
                ]
        );
        if ($type_q == 'post') {
            $target->add_control(
                    'custommeta_source_reverse', [
                'label' => __('Reverse Relationship', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'query_type' => 'custommeta_source',
                    'custommeta_source_querytype' => 'post'
                ],
                    ]
            );
        }
        
        // ---------------- term
        $target->add_control(
                'custommeta_source_key_term', [
            'label' => __('Term Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search Term Custom Field', 'e-addons'),
            'query_type' => 'metas',
            'object_type' => 'term',
            'label_block' => true,
            'condition' => [
                'query_type' => 'custommeta_source',
                'custommeta_source_querytype' => 'term'
            ],
                ]
        );
        // ---------------- user
        $target->add_control(
                'custommeta_source_key_user', [
            'label' => __('User Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search User Custom Field', 'e-addons'),
            'query_type' => 'metas',
            'object_type' => 'user',
            'label_block' => true,
            'condition' => [
                'query_type' => 'custommeta_source',
                'custommeta_source_querytype' => 'user'
            ],
                ]
        );
        // ---------------- attachment
        $target->add_control(
                'custommeta_source_key_attachment', [
            'label' => __('Media Custom Field', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Search Media Custom Field', 'e-addons'),
            'query_type' => 'metas',
            'object_type' => 'attachment',
            'label_block' => true,
            'condition' => [
                'query_type' => 'custommeta_source',
                'custommeta_source_querytype' => 'attachment'
            ],
                ]
        );

        // ---------------- post
        $target->add_control(
            'custommeta_source_post', [
            'label' => '<i class="fas fa-external-link-alt"></i> '.__('Source Post', 'e-addons'),
            'separator' => 'before',
            'type' => 'e-query',
            'placeholder' => __('Current or Search Post', 'e-addons'),
            'description' => __('Leave empty for Current Post', 'e-addons'),
            'query_type' => 'posts',
            'label_block' => true,
            'condition' => [
                'query_type' => 'custommeta_source',
                'custommeta_source_querytype' => 'post'
            ],
                ]
        );
        // ---------------- term
        $target->add_control(
            'custommeta_source_term', [
                'label' => '<i class="fas fa-external-link-alt"></i> '.__('Source Term', 'e-addons'),
                'separator' => 'before',
                'type' => 'e-query',
                'placeholder' => __('Current or Search Term', 'e-addons'),
                'description' => __('Leave empty for Current Term or Post Term', 'e-addons'),
                'query_type' => 'terms',
                'label_block' => true,
                'condition' => [
                    'query_type' => 'custommeta_source',
                    'custommeta_source_querytype' => 'term'
                ],
            ]
        );
        $target->add_control(
            'custommeta_source_author', [
                'label' => __('From Author', 'e-addons'),
                'separator' => 'before',
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'query_type' => 'custommeta_source',
                    'custommeta_source_querytype' => 'user'
                ],
            ]
        );
        // ---------------- user
        $target->add_control(
            'custommeta_source_user', [
                'label' => '<i class="fas fa-external-link-alt"></i> '.__('Source User', 'e-addons'),
                'type' => 'e-query',
                'placeholder' => __('Current or Search User', 'e-addons'),
                'description' => __('Leave empty for Current Logged In User', 'e-addons'),
                'query_type' => 'users',
                'label_block' => true,
                'condition' => [
                    'custommeta_source_author' => '',
                    'query_type' => 'custommeta_source',
                    'custommeta_source_querytype' => 'user'
                ],
            ]
        );
        // ---------------- attachment
        $target->add_control(
            'custommeta_source_attachment', [
                'label' => '<i class="fas fa-external-link-alt"></i> '.__('Source Media', 'e-addons'),
                'separator' => 'before',
                'type' => 'e-query',
                'placeholder' => __('Current or Search Media', 'e-addons'),
                'description' => __('Leave empty for Current Media or Post', 'e-addons'),
                'query_type' => 'posts',
                'object_type' => 'attachment',
                'label_block' => true,
                'condition' => [
                    'query_type' => 'custommeta_source',
                    'custommeta_source_querytype' => 'attachment'
                ],
            ]
        );
    }

    // -------------- Custom Fields for Posts/Users/Terms ---------
    public function custommeta_items($target, $type_q = '') {
        if (!$type_q) {
            $type_q = $this->get_querytype();
        }
        if($type_q != 'repeater'){
            //Repeater Field Control
            //$this->controls_repeaterfield_content($target);
            //}else{
            //Key
            $target->add_control(
                'metafield_key', [
                    'label' => __('META Field', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => __('Search Meta key or Field Name', 'e-addons'),
                    'label_block' => true,
                    //$type_q
                    //'query_type' => 'posts', 
                    //'object_type' => 'elementor_library',
                    //'query_type' => 'users',
                    //'object_type' => 'role',
                    'query_type' => 'metas', //'fields',
                    'object_type' => $type_q,
                    //'query_type' => 'fields',
                    //'object_type' => 'term',
                    //'query_type' => 'fields',
                    //'object_type' => 'post',
                    //--------
                    //'query_type' => 'post',
                    //'object_type' => 'meta',
                    //'query_type' => 'terms',
                    //'object_type' => 'tags',
                    //'query_type' => 'taxonomies',
                    //'query_type'    => 'metas',
                    //'object_type'   => $type_mf,
                    'default' => '',
                    'dynamic' => [
                        'active' => false,
                    ],
                    'condition' => [
                        'item_type' => 'item_custommeta'
                    ]
                ]
            );
        }
        
        //Type
        $target->add_control(
                'metafield_type', [
            'label' => __('Return Format', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => 'text',
            'separator' => 'after',
            'options' => [
                //'' => __('Select Return Format', 'e-addons'), //
                'text' => __('Text', 'e-addons'),
                'image' => __('Image', 'e-addons'),
                'number' => __('Number', 'e-addons'),
                'oembed' => __('oEmbed', 'e-addons'), //
                'date' => __('Date', 'e-addons'), //                
                'textarea' => __('Textarea', 'e-addons'), //
                'button' => __('Button (url)', 'e-addons'), //
                'map' => __('Map (address)', 'e-addons'), //
                'file' => __('Media (ID)', 'e-addons'), //
                'post' => __('Post (ID)', 'e-addons'), //
                'user' => __('Users (ID)', 'e-addons'), //
                'term' => __('Terms (ID)', 'e-addons'), //
                'gallery' => __('Gallery', 'e-addons'), //
                'array' => __('Array', 'e-addons'), //
            ],
            'condition' => [
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        //...'metafield_type!' => ['','textarea','date','button','file','oembed','map','term','post','user','gallery','array'],
        //
        //
        //
        
        //Number
        $target->add_control(
                'number_round', [
            'label' => __('Number Round', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'metafield_type' => 'number',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'number_round_precision', [
            'label' => __('Number Round Precision', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'condition' => [
                'number_round!' => '',
                'metafield_type' => 'number',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'number_round_mode', [
            'label' => __('Number Round Mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Ceil'),
            'label_off' => __('Upper'),
            'condition' => [
                'number_round!' => '',
                'metafield_type' => 'number',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        
        $target->add_control(
                'number_format', [
            'label' => __('Number Format', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'metafield_type' => 'number',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'number_format_decimals', [
            'label' => __('Number Format Decimals', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'condition' => [
                'number_format!' => '',
                'metafield_type' => 'number',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'number_format_decimal_separator', [
            'label' => __('Number Format Decimal Separator', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => '.',
            'condition' => [
                'number_format!' => '',
                'metafield_type' => 'number',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'number_format_thousands_separator', [
            'label' => __('Number Format Thousands Separator', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => ',',
            'condition' => [
                'number_format!' => '',
                'metafield_type' => 'number',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        
        
        //Array
        $target->add_control(
                'array_dump', [
            'label' => __('Show array dump', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'metafield_type' => 'array',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'array_index', [
            'label' => __('Array Indexes', 'e-addons'),
            'description' => __('Write the sub array keys separated by comma (example: 0.1.2, name.val). Empty it\'s all', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'example: 1.val',
            'default' => '',
            'condition' => [
                'metafield_type' => 'array',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        //Image
        $target->add_group_control(
                Group_Control_Image_Size::get_type(), [
            'name' => 'metafield_image_size',
            'label' => __('Image Format', 'e-addons'),
            'default' => 'large',
            'condition' => [
                'metafield_type' => 'image',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_responsive_control(
                'metafield_image_width', [
            'label' => __('Image Width', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%', 'px'],
            'range' => [
                '%' => [
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
                '{{WRAPPER}} {{CURRENT_ITEM}}.e-add-item_custommeta img' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'metafield_type' => 'image',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        // list of post-users-terms
        /* $target->add_control(
          'metafield_gallery_type', [
          'label' => __('Gallery type', 'e-addons'),
          'type' => Controls_Manager::CHOOSE,
          'toggle' => false,
          'options' => [
          'grid' => [
          'title' => __('Grid', 'e-addons'),
          'icon' => 'fas fa-ellipsis-h',
          ],
          'carousel' => [
          'title' => __('Carousel', 'e-addons'),
          'icon' => 'fas fa-ellipsis-v',
          ]
          ],
          'default' => 'grid',
          'condition' => [
          'metafield_type' => 'gallery',
          'item_type' => 'item_custommeta'
          ]
          ]
          ); */
        //Date
        $target->add_control(
                'metafield_date_format_source', [
            'label' => __('Date Format: SOURCE', 'e-addons'),
            'description' => '<a target="_blank" href="https://www.php.net/manual/en/function.date.php">' . __('Use standard PHP format character') . '</a>',
            'type' => Controls_Manager::TEXT,
            'default' => get_option('date_format'),
            'placeholder' => __('YmdHis, d/m/Y, m-d-y', 'e-addons'),
            'condition' => [
                'metafield_type' => 'date',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'metafield_date_format_display', [
            'label' => __('Date Format: DISPLAY', 'e-addons'),
            'placeholder' => __('YmdHis, d/m/Y, m-d-y', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => get_option('date_format'),
            'condition' => [
                'metafield_type' => 'date',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        // button
        $target->add_control(
                'metafield_button_label', [
            'label' => __('Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Click me', 'e-addons'),
            'condition' => [
                'metafield_type' => 'button',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'metafield_button_size',
                [
                    'label' => __('Size', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'sm',
                    'options' => Query_Utils::get_button_sizes(),
                    'style_transfer' => true,
                    'condition' => [
                        'metafield_type' => 'button',
                        'item_type' => 'item_custommeta'
                    ]
                ]
        );
        $target->add_control(
                'metafield_button_target', [
            'label' => __('Target', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('Self', 'e-addons'),
            'label_on' => __('Blank', 'e-addons'),
            'condition' => [
                'metafield_type' => 'button',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'metafield_button_nofollow', [
            'label' => __('Nofollow', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'metafield_type' => 'button',
                'item_type' => 'item_custommeta'
            ]
                ]
        );

        //Text
        $target->add_control(
                'html_tag_item', [
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
            'condition' => [
                'metafield_type' => ['text', 'number'],
                'item_type' => 'item_custommeta'
            ],
            'default' => 'span',
                ]
        );
        //Terms
        $target->add_control(
                'metafield_term_count', [
            'label' => __('Show count', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'metafield_type' => 'term',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'metafield_term_hideempty', [
            'label' => __('Hide Empty', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'condition' => [
                'metafield_type' => 'term',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        //Users
        //Posts
        $target->add_control(
                'metafield_post_image', [
            'label' => __('Show image', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'condition' => [
                'metafield_type' => 'post',
                'item_type' => 'item_custommeta'
            ]
                ]
        );

        // list of post-users-terms
        $target->add_control(
                'metafield_list_direction', [
            'label' => __('Direction', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'options' => [
                'horizontal' => [
                    'title' => __('Horizontal', 'e-addons'),
                    'icon' => 'fas fa-ellipsis-h',
                ],
                'vertical' => [
                    'title' => __('Vertical', 'e-addons'),
                    'icon' => 'fas fa-ellipsis-v',
                ]
            ],
            'default' => 'horizontal',
            'condition' => [
                'metafield_type' => ['user', 'term'],
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        $target->add_control(
                'metafield_list_separator', [
            'label' => __('Separator', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => ',',
            'condition' => [
                'metafield_type' => ['user', 'term'],
                'metafield_term_style' => 'horizontal',
                'item_type' => 'item_custommeta'
            ]
                ]
        );

        //File
        $target->add_control(
                'metafield_file_label', [
            'label' => __('Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => 'File',
            'condition' => [
                'metafield_type' => 'file',
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        //l'icona vale per text, button o file
        $target->add_control(
                'show_icon',
                [
                    'label' => __('Icon', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => '',
                        'library' => 'fa-solid',
                    ],
                    'skin' => 'inline',
                    'label_block' => false,
                    'fa4compatibility' => 'icon',
                    'condition' => [
                        'metafield_type' => ['button', 'file', 'text', 'number'],
                        'item_type' => 'item_custommeta'
                    ]
                ]
        );
        //The Link... 
        // @p abilito il link se lo voglio x i custommmeta di tipo users, terms, posts
        $target->add_control(
                'metafield_list_link', [
            'label' => __('Link', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'metafield_type' => ['user', 'term', 'post'],
                'item_type' => 'item_custommeta'
            ]
                ]
        );
        // @p in caso di immagine posso decidere che il link è:
        // 1 - al post (naturale)
        // 2 - custom (ad altro)
        $target->add_control(
            'link_to', [
                'label' => __('Link to', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'separator' => 'before',
                'options' => [
                    '' => __('None', 'e-addons'),
                    'post' => strtoupper($type_q) . ' URL',
                    'custom' => __('Custom URL', 'e-addons'),
                ],
                'condition' => [
                    //'','textarea','date','button','file','oembed','map','term','post','user','gallery'
                    // 
                    'metafield_type' => ['image', 'text'],
                    'item_type' => 'item_custommeta'
                ]
            ]
        );
        $target->add_control(
                'link', [
            'label' => __('Link', 'e-addons'),
            'type' => Controls_Manager::URL,
            'placeholder' => __('http://your-link.com', 'e-addons'),
            'condition' => [
                'metafield_type' => ['image', 'text'],
                'link_to' => 'custom',
                'item_type' => 'item_custommeta'
            ],
            'dynamic' => [
                'active' => true
            ],
            'default' => [
                'url' => '',
            ],
            'show_label' => false,
                ]
        );
    }

    public function get_custom_meta_source_value($settings) {
        switch ($settings['custommeta_source_querytype']) {
            case 'user':
                $type_of_location = 'user';
                if (!empty($settings['custommeta_source_author'])) {
                    $id_of_location = get_the_author_meta('ID');
                } else {
                    if (!empty($settings['custommeta_source_' . $type_of_location])) {
                        $id_of_location = $settings['custommeta_source_' . $type_of_location];
                    } else {
                        $id_of_location = get_current_user_id();
                    }
                }
                break;
            case 'media':
            case 'attachment':
                $type_of_location = 'attachment';
                if (!empty($settings['custommeta_source_' . $type_of_location])) {
                    $id_of_location = $settings['custommeta_source_' . $type_of_location];
                } else {
                    $id_of_location = get_the_ID();
                }
                break;
            case 'term':
                $type_of_location = 'term';
                if (!empty($settings['custommeta_source_' . $type_of_location])) {
                    $id_of_location = $settings['custommeta_source_' . $type_of_location];
                } else {
                    $id_of_location = Utils::get_term_id();
                }
                break;
            case 'post':
            default:
                $type_of_location = 'post';
                
                if (!empty($settings['custommeta_source_' . $type_of_location])) {
                    $id_of_location = $settings['custommeta_source_' . $type_of_location];
                } else {
                    $id_of_location = get_the_ID();
                }
        }
        $custommeta_source_key = $settings['custommeta_source_key_' . $type_of_location];
        if ($type_of_location == 'attachment') {
            $type_of_location = 'post';
        }
        
        $custommeta_source = get_metadata($type_of_location, $id_of_location, $custommeta_source_key);
        if (is_array($custommeta_source)) {
            if (!empty($custommeta_source)) {
                $custommeta_source_first = reset($custommeta_source);                
                if (count($custommeta_source) > 1) {                    
                    if (is_array($custommeta_source_first)) {
                        if (!empty($custommeta_source_first['ID'])) {
                            $tmp = array();
                            foreach ($custommeta_source as $key => $value) {
                                $tmp[] = $value['ID'];
                            }
                            // PODS
                            return $tmp;
                        }                        
                    }   
                    // JET
                    return $custommeta_source;
                }
                // ACF
                return reset($custommeta_source); // single meta
            }
            // empty, no custom field
            return false;
        }
        return $custommeta_source;
    }

}