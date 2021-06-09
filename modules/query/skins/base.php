<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use EAddonsForElementor\Base\Base_Skin;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Base Skin
 *
 * Elementor widget query-posts for e-addons
 *
 */
class Base extends Base_Skin {

    use Traits\Infinite_Scroll;
    use Traits\Pagination;
    use Traits\Hover;
    use Traits\Reveal;

    use Traits\Post;
    use Traits\Term;
    use Traits\User;
    use Traits\Media;
    use Traits\Item;
    use Traits\Common;
    use Traits\Custommeta;
    use Traits\Label;

    public $current_permalink;
    public $current_id;
    public $current_data;
    public $counter = 0;
    public $index = 0;
    protected $itemindex = 0;
    protected $depended_scripts = [];
    protected $depended_styles = [];

    public function show_in_settings() {
        return false;
    }

    public function _register_controls_actions() {

        if (!has_action('elementor/element/' . $this->parent->get_name() . '/section_items/after_section_end', [$this, 'register_controls_layout'])) {
            add_action('elementor/element/' . $this->parent->get_name() . '/section_items/after_section_end', [$this, 'register_controls_layout']);
        }
        if (!has_action('elementor/element/' . $this->parent->get_name() . '/section_items/before_section_start', [$this, 'register_controls_hovereffects'])) {
            add_action('elementor/element/' . $this->parent->get_name() . '/section_items/before_section_start', [$this, 'register_controls_hovereffects']);
        }

        add_action('elementor/preview/enqueue_scripts', [$this, 'preview_enqueue']);
    }

    public function register_controls_layout(Widget_Base $widget) {
        //$this->parent = $widget;
        // BLOCKS generic style
        $this->register_style_controls();
        // PAGINATION style
        $this->register_style_pagination_controls();
        //INFINITE SCROLL style
        $this->register_style_infinitescroll_controls();
    }

    // ---------------------------------------------------------------
    protected function register_style_controls() {
        //
        // Blocks - Style
        $this->start_controls_section(
                'section_blocks_style',
                [
                    'label' => __('Block Style', 'e-addons'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'style_items!' => 'template',
                        '_skin!' => ['table', 'list'],
                    ]
                ]
        );
        $this->add_responsive_control(
                'blocks_align', [
            'label' => __('Text Alignment', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => true,
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
                '{{WRAPPER}} .e-add-post-item' => 'text-align: {{VALUE}};',
            ],
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'heading_blocks_align_flex',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-arrows-alt" aria-hidden="true"></i> ' . __('Flex Alignment', 'e-addons'),
                    'separator' => 'before',
                    'content_classes' => 'e-add-inner-heading',
                /* 'condition' => [
                  $this->get_control_id('v_pos_postitems') => ['', 'stretch'],
                  ], */
                ]
        );
        /*
          $this->add_responsive_control(
          'blocks_align_flex', [
          'label' => __('Horizontal Flex align', 'e-addons'), //__('Flex Items Align', 'e-addons'),
          'type' => Controls_Manager::SELECT,
          'default' => '',
          'options' => [
          '' => 'Default',
          'flex-start' => 'Left',
          'center' => 'Center',
          'flex-end' => 'Right',
          'space-between' => 'Space Between',
          'space-around' => 'Space Around',
          //'stretch' => 'Stretch',
          ],
          'selectors' => [
          '{{WRAPPER}} .e-add-post-block, {{WRAPPER}} .e-add-item-area' => 'align-items: {{VALUE}} !important;',
          ],
          //'condition' => [
          //  $this->get_control_id('v_pos_postitems') => ['', 'stretch'],
          //],
          ]
          ); */

        $this->add_control(
                'blocks_align_flex', [
            'label' => __('Horizontal Flex align', 'e-addons'), //__('Flex Items Align', 'e-addons'),
            'type' => 'ui_selector',
            'toggle' => true,
            'type_selector' => 'image',
            'label_block' => true,
            'columns_grid' => 5,
            'options' => [
                '' => [
                    'title' => __('Left', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/block_left.svg',
                ],
                'center' => [
                    'title' => __('Middle', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/block_middle.svg',
                ],
                'flex-end' => [
                    'title' => __('Right', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/block_right.svg',
                ],
                'space-between' => [
                    'title' => __('Space Between', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/block_space-between.svg',
                ],
                'space-around' => [
                    'title' => __('Space Around', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/block_space-around.svg',
                ],
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-block, {{WRAPPER}} .e-add-item-area' => 'justify-content: {{VALUE}};',
            ],
                ]
        );

        /*
          $this->add_responsive_control(
          'blocks_align_justify', [
          'label' => __('Vertical Flex align', 'e-addons'), //__('Flex Justify Content', 'e-addons'),
          'type' => Controls_Manager::SELECT,
          'default' => '',
          'options' => [
          '' => 'Default',
          'flex-start' => 'Top',
          'center' => 'Middle',
          'flex-end' => 'Bottom',
          'space-between' => 'Space Between',
          'space-around' => 'Space Around',
          //'stretch' => 'Stretch',
          ],
          'selectors' => [
          '{{WRAPPER}} .e-add-post-block, {{WRAPPER}} .e-add-item-area' => 'justify-content: {{VALUE}} !important;',
          ],
          //'separator' => 'after',
          //     'condition' => [
          //      $this->get_control_id('v_pos_postitems') => ['', 'stretch'],
          //  ],
          //]
          ); */
        $this->add_control(
                'blocks_align_justify', [
            'label' => __('Vertical Flex align', 'e-addons'), //__('Flex Justify Content', 'e-addons'),
            'type' => 'ui_selector',
            'toggle' => true,
            'label_block' => true,
            'type_selector' => 'image',
            'columns_grid' => 5,
            'options' => [
                'flex-start' => [
                    'title' => __('Top', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/flex_top.svg',
                ],
                'center' => [
                    'title' => __('Center', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/flex_middle.svg',
                ],
                'flex-end' => [
                    'title' => __('Bottom', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/flex_bottom.svg',
                ],
                'space-between' => [
                    'title' => __('Space Betweens', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/flex_space-between.svg',
                ],
                'space-around' => [
                    'title' => __('Space Around', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/grid_alignments/flex_space-around.svg',
                ]
            ],
            'default' => 'flex-start',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-block, {{WRAPPER}} .e-add-item-area' => 'align-content: {{VALUE}}; align-items: {{VALUE}};',
            ],
                ]
        );

        $this->add_control(
                'blocks_bgcolor', [
            'label' => __('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-post-block' => 'background-color: {{VALUE}};'
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'blocks_border',
            'selector' => '{{WRAPPER}} .e-add-post-item .e-add-post-block',
                ]
        );
        $this->add_responsive_control(
                'blocks_padding', [
            'label' => __('Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-post-block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_control(
                'blocks_border_radius', [
            'label' => __('Border Radius', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-post-block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'blocks_boxshadow',
            'selector' => '{{WRAPPER}} .e-add-post-item .e-add-post-block',
                ]
        );
        // Vertical Alternate
        /*
          $this->add_control(
          'dis_alternate',
          [
          'type' => Controls_Manager::RAW_HTML,
          'show_label' => false,
          'separator' => 'before',
          'raw' => '<img src="' . E_ADDONS_QUERY_URL . 'assets/img/skins/alternate.png' . '" />',
          'content_classes' => 'e-add-skin-dis',
          'condition' => [
          $this->get_control_id('grid_type') => ['flex']
          ],
          ]
          );

          $this->add_responsive_control(
          'blocks_alternate', [
          'label' => __('Vertical Alternate', 'e-addons'),
          'type' => Controls_Manager::SLIDER,
          'size_units' => ['px'],
          'range' => [
          'px' => [
          'max' => 100,
          'min' => 0,
          'step' => 1,
          ],
          ],
          'selectors' => [
          '{{WRAPPER}}.e-add-col-3 .e-add-post-item:nth-child(3n+2) .e-add-post-block, {{WRAPPER}}:not(.e-add-col-3) .e-add-post-item:nth-child(even) .e-add-post-block' => 'margin-top: {{SIZE}}{{UNIT}};',
          ],
          'condition' => [
          $this->get_control_id('grid_type') => ['flex']
          ],
          ]
          ); */
        $this->end_controls_section();

        //
    }

    public function has_results($query, $querytype) {
        switch ($querytype) {
            case 'attachment':
            case 'post':
                if (!$query->found_posts) {
                    return false;
                }
                break;
            case 'user':
                if (empty($query->get_results())) {
                    return false;
                }
                break;
            case 'term':
                if (empty($query) || empty($query->get_terms()) || is_wp_error($query->get_terms())) {
                    return false;
                }
                break;
            case 'repeater':
                if ($this->parent->is_empty_repeater()) {
                    return false;
                }
                break;
            case 'items':
                // il ripetitore di contenuti statici
                $sl_items = $this->parent->get_settings_for_display('repeater_staticlist');
                if (empty($sl_items)) {
                    return false;
                }
                break;
        }
        return true;
    }

    public function render() {

        if (!$this->parent) {
            return;
        }

        $this->parent->render();

        /** @p elaboro la query... */
        $this->parent->query_the_elements();

        /** @p qui prendo il valore di $query elaborato in query-base.php */
        $query = $this->parent->get_query();
        $querytype = $this->parent->get_querytype();

        if ($this->has_results($query, $querytype)) {

            global $e_widget_query;
            $e_widget_query = $this;

            /** @p enquequo gli script e gli style... */
            $this->enqueue();

            $this->render_loop_start();

            $this->index = $this->get_index_start();
            
            switch ($querytype) {
                case 'attachment':
                case 'post':

                    /** @p qui identifico se mi trovo in un loop, altrimenti uso la wp_query */
                    if ($query->in_the_loop) {
                        $this->current_permalink = get_permalink();
                        $this->current_id = get_the_ID();
                        $this->current_data = get_post(get_the_ID());
                        //
                        $this->render_element_item();
                    } else {
                        $i = 0;
                        $j = 0;
                        $offset = $this->parent->get_settings_for_display('posts_offset');
                        $limit = $this->parent->get_settings_for_display('posts_limit');
                        while ($query->have_posts()) {
                            $i++;
                            $query->the_post();
                            $continue = false;
                            if ($limit) {
                                if ($offset) {
                                    if ($i <= $offset) {
                                        $continue = true;
                                    }
                                }
                                if (!$continue) {
                                    $j++;
                                }
                                if ($j > $limit) {
                                    $continue = true;
                                }
                            }
                            if (!$continue) {
                                $this->current_permalink = get_permalink();
                                $this->current_id = get_the_ID();
                                $this->current_data = get_post(get_the_ID());
                                //
                                $this->render_element_item();
                            }
                        }
                    }
                    wp_reset_postdata();
                    break;
                case 'user':
                    foreach ($query->get_results() as $user) {
                        $this->current_permalink = get_author_posts_url($user->ID);
                        $this->current_id = $user->ID;
                        $this->current_data = $user;
                        //
                        $this->render_element_item();
                    }
                    break;
                case 'term':
                    foreach ($query->get_terms() as $term) {
                        if ($term && !is_wp_error($term)) {
                            $link = get_term_link($term->term_id, $term->taxonomy);
                            $this->current_permalink = $link;
                            $this->current_id = $term->term_id;
                            $this->current_data = $term;
                            //
                            $this->render_element_item();
                        }
                    }
                    break;
                case 'repeater':
                    //echo 'questo è REPEATER';
                    $repeater_field = $this->parent->get_settings_for_display('repeater_field');
                    //var_dump($repeater_field);
                    //var_dump($id_target);
                    //var_dump($customfields_type);
                    if ($repeater_field) {
                        $repeater_rows = $this->parent->get_repeater_rows();
                        if (!empty($repeater_rows)) {
                            $list_items = $this->parent->get_settings_for_display('list_items');
                            $customfields_type = $this->parent->get_settings_for_display('customfields_type');
                            $data_source = $this->parent->get_settings_for_display('data_source');
                            $id_target = $customfields_type == 'user' ? get_current_user_id() : get_queried_object_id();
                            if (empty($data_source) && !empty($this->parent->get_settings_for_display('source_' . $customfields_type))) {
                                $id_target = $this->parent->get_settings_for_display('source_' . $customfields_type);
                            }
                            $repeater_field_link = $this->parent->get_settings_for_display('repeater_field_link');
                            global $wp_query;
                            $wp_query->in_repeater_loop = true;
                            foreach ($repeater_rows as $repeater_row) {

                                $repeater_values = $repeater_row;
                                //  ciclo 'list_items' e ne ricavo le chiavi
                                if (!empty($list_items)) {
                                    foreach ($list_items as $key => $item) {
                                        if (!empty($item['metafield_key'])) {
                                            $data_row = !empty($repeater_row[$item['metafield_key']]) ? $repeater_row[$item['metafield_key']] : false;
                                            $repeater_values[$item['item_type'] . '_' . $key] = $data_row;
                                            //var_dump($key);
                                        }
                                    }
                                }

                                $repeater_field_link_url = get_permalink();
                                if ($repeater_field_link) {
                                    if (!empty($repeater_row[$repeater_field_link])) {
                                        $repeater_field_link_url = $repeater_row[$repeater_field_link];
                                    }
                                }

                                $this->current_permalink = $repeater_field_link_url;
                                $this->current_id = $id_target;
                                $this->current_data = $repeater_values;
                                $this->render_element_item();
                            }
                            $wp_query->in_repeater_loop = false;
                        }
                    }

                    // ---------------------------------------
                    break;
                case 'items':
                    // il ripetitore di contenuti statici
                    $sl_items = $this->parent->get_settings_for_display('repeater_staticlist');
                    foreach ($sl_items as $item) {
                        //echo $item['sl_title'];
                        $this->current_permalink = $item['sl_link']['url'];
                        $this->current_id = '';
                        $this->current_data = $item;

                        //echo $this->current_data['sl_image']['id'];
                        //
                        $this->render_element_item();
                    }
                    break;
            }

            $this->render_loop_end();

            $this->parent->render_pagination();
        } else {
            $this->render_no_results();
        }
    }

    protected function render_no_results() {
        $query_no_result = $this->parent->get_settings_for_display('query_no_result');
        if ($query_no_result) {
            $query_no_result_txt = $this->parent->get_settings_for_display('query_no_result_txt');
            if (!empty($query_no_result_txt)) {
                $query_no_result_txt = Utils::get_dynamic_data($query_no_result_txt);
                echo $query_no_result_txt;
            }
        }
    }

    public function render_full_block_link() {
        $linkable = $this->parent->get_settings_for_display('templatemode_linkable');
        if ($linkable) {
            echo '<a class="e-full-block-link" href="' . $this->current_permalink . '"></a>';
        }
    }

    protected function render_element_item() {

        $this->index++;        

        $style_items = $this->parent->get_settings_for_display('style_items');

        $this->render_item_start();

        if ($style_items == 'template') {
            $this->render_template();
        } else {
            $this->render_items();
        }

        $this->render_item_end();

        $this->counter++;
    }

    protected function render_template() {

        $template_id = $this->parent->get_settings_for_display('template_id');
        $templatemode_enable_2 = $this->parent->get_settings_for_display('templatemode_enable_2');
        $template_2_id = $this->parent->get_settings_for_display('template_2_id');

        if ($templatemode_enable_2) {
            if ($this->counter % 2 == 0) {
                // Even
                $post_template_id = $template_id;
            } else {
                // Odd
                $post_template_id = $template_2_id;
            }
        } else {
            $post_template_id = $template_id;
        }

        if ($post_template_id)
            $this->render_e_template($post_template_id);
        //
        $this->render_full_block_link();
    }

    protected function render_e_template($id_temp) {

        $args = array();
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $args['css'] = true;
        }

        $querytype = $this->parent->get_querytype();
        if ($querytype == 'user') {
            $querytype = 'author';
        }
        $args[$querytype . '_id'] = $this->current_id;

        echo \EAddonsForElementor\Core\Managers\Template::e_template($id_temp, $args);
    }

    /* !!!!!!!!!!!!!!!!!!! ITEMS !!!!!!!!!!!!!!!!! */

    protected function render_items() {
        $_skin = $this->parent->get_settings_for_display('_skin');
        $style_items = $this->parent->get_settings_for_display('style_items');

        //@p in caso di justifiedgrid forzo lo style_items in "float"
        //@p probabilmente farò lo stesso anche per lo skin rhombus ex diamond
        if ($_skin == 'justifiedgrid') {
            $style_items = 'float';
        }

        //@p questo interviene per animare al rollhover il blocco di contenuto
        $hover_animation = $this->get_instance_value('hover_content_animation');
        $animation_class = !empty($hover_animation) && $style_items != 'float' && $_skin != 'gridtofullscreen3d' ? ' elementor-animation-' . $hover_animation : '';

        //@p questo interviene per fare gli effetti di animazione al rollhover in caso di layout-FLOAT
        $hover_effects = $this->get_instance_value('hover_text_effect');
        $hoverEffects_class = !empty($hover_effects) && $style_items == 'float' && $_skin != 'gridtofullscreen3d' ? ' e-add-hover-effect-' . $hover_effects . ' e-add-hover-effect-content e-add-close' : '';

        $hoverEffects_start = !empty($hover_effects) && $style_items == 'float' && $_skin != 'gridtofullscreen3d' ? '<div class="e-add-hover-effect-' . $hover_effects . ' e-add-hover-effect-content e-add-close">' : '';
        $hoverEffects_end = !empty($hover_effects) && $style_items == 'float' ? '</div>' : '';

        //@p NOTA:  [x timeline] una piccola considerazione a timeline....
        //          forzo la timeliine al layout default e non uso l'immagine nel content
        if (($style_items && $style_items != 'default' && $_skin != 'timeline') || $_skin == 'justifiedgrid') {
            // Layouts
            //echo 'sono un layout: left/right/alternate/textZone/overlay/float';
            echo '<div class="e-add-image-area e-add-item-area">';
            $this->render_items_image();
            echo '</div>';
            echo $hoverEffects_start . '<div class="e-add-content-area e-add-item-area' . $animation_class . '">';
            $this->render_items_content(false); //@p il false non produce l'immagine
            echo '</div>' . $hoverEffects_end;
        } else {
            // Layout-default
            //echo 'sono layout-default';

            if ($_skin == 'timeline') {
                $this->render_items_content(false); //@p il false non produce l'immagine
            } else {
                $this->render_items_content(true);
            }
        }
    }

    // IMAGE
    protected function render_items_image() {
        $_items = $this->parent->get_settings_for_display('list_items');
        $querytype = $this->parent->get_querytype();

        //QUERY_MEDIA //////////////////
        //@p l'immagine viene renderizzata sempre per il query_media widget
        if ($querytype == 'attachment') {
            $item = array(
                '_id' => 'e-add-media-image',
                'item_type' => 'item_image',
            );
            $this->render_repeateritem_start($item);
            //----------------------------------
            $this->render_item_image($this->parent->get_settings_for_display());
            //----------------------------------
            $this->render_repeateritem_end();
        } else {
            //
            // ITEMS ///////////////////////
            foreach ($_items as $key => $item) {
                //
                if (!empty($item['item_type'])) {
                    switch ($item['item_type']) {
                        case 'item_image':
                            $this->render_repeateritem_start($item);
                            //----------------------------------
                            $this->render_item_image($item, $key);
                            //----------------------------------
                            $this->render_repeateritem_end();
                            break;
                        case 'item_imageoricon':
                            $this->render_repeateritem_start($item);
                            //----------------------------------
                            $this->render_item_imageoricon($item);
                            //----------------------------------
                            $this->render_repeateritem_end();
                            break;
                        case 'item_avatar':
                            $this->render_repeateritem_start($item);
                            //----------------------------------
                            $this->render_item_avatar($item);
                            //----------------------------------
                            $this->render_repeateritem_end();
                    }
                }
            }
        }
    }

    // FIELDS
    protected function render_items_content($useimg = true) {
        $_items = $this->parent->get_settings_for_display('list_items');
        $querytype = $this->parent->get_querytype();
        //
        // QUERY_MEDIA //////////////////
        //@p l'immagine viene renderizzata sempre per il query_media widget
        if ($querytype == 'attachment' && $useimg) {
            $item = array(
                '_id' => 'e-add-media-image',
                'item_type' => 'item_image',
            );
            $this->render_repeateritem_start($item);
            //----------------------------------
            $this->render_item_image($this->parent->get_settings_for_display());
            //----------------------------------
            $this->render_repeateritem_end();
        }
        if (!empty($_items)) {
            // ITEMS ///////////////////////

            foreach ($_items as $key => $item) {


                //$custommetakey = $item['metafield_key'];

                if (!empty($item['item_type'])) {

                    $this->render_repeateritem_start($item);
                    //----------------------------------
                    //

                    switch ($item['item_type']) {
                        // commmon
                        case 'item_title': $this->render_item_title($item, $key);
                            break;
                        case 'item_date': $this->render_item_date($item, $key);
                            break;
                        // posts
                        case 'item_author': $this->render_item_author($item);
                            break;
                        case 'item_termstaxonomy': $this->render_item_termstaxonomy($item);
                            break;
                        case 'item_excerpt': $this->render_item_content($item, true);
                            break;
                        case 'item_content': $this->render_item_content($item);
                            break;
                        case 'item_posttype': $this->render_item_posttype($item);
                            break;
                        //----------------------------------
                        // ueser
                        case 'item_displayname': $this->render_item_userdata('displayname', $item);
                            break;
                        case 'item_user': $this->render_item_userdata('user', $item);
                            break;
                        case 'item_role': $this->render_item_userdata('role', $item);
                            break;
                        case 'item_firstname': $this->render_item_userdata('firstname', $item);
                            break;
                        case 'item_lastname': $this->render_item_userdata('lastname', $item);
                            break;
                        case 'item_nickname': $this->render_item_userdata('nickname', $item);
                            break;
                        case 'item_email': $this->render_item_userdata('email', $item);
                            break;
                        case 'item_website': $this->render_item_userdata('website', $item);
                            break;
                        case 'item_bio': $this->render_item_userdata('bio', $item);
                            break;
                        case 'item_registered': $this->render_item_date($item); //render_item_userdata('registered', $item);
                            break;
                        //----------------------------------
                        // terms
                        case 'item_counts': $this->render_item_postscount($item);
                            break;
                        case 'item_taxonomy': $this->render_item_taxonomy($item);
                            break;
                        case 'item_imagemeta': $this->render_item_imagemeta($item);
                            break;
                        case 'item_mimetype': $this->render_item_mimetype($item);
                            break;
                        case 'item_description': $this->render_item_description($item);
                            break;
                        //----------------------------------
                        // media
                        case 'item_caption': $this->render_item_caption($item);
                            break;
                        case 'item_alternativetext': $this->render_item_alternativetext($item);
                            break;
                        case 'item_uploadedto': $this->render_item_uploadedto($item);
                            break;
                        //----------------------------------
                        // items list
                        case 'item_subtitle': $this->render_item_subtitle($item);
                            break;
                        case 'item_descriptiontext': $this->render_item_descriptiontext($item);
                            break;
                        case 'item_imageoricon':
                            if ($useimg) {
                                $this->render_item_imageoricon($item);
                            }
                            break;
                        //----------------------------------
                        // posts/user/terms 
                        case 'item_custommeta': $this->render_item_custommeta($item, $key);
                            break;
                        case 'item_readmore': $this->render_item_readmore($item);
                            break;
                        case 'item_label': $this->render_item_labelhtml($item);
                            break;
                        case 'item_index': $this->render_item_index($item);
                            break;
                        case 'item_template': $this->render_item_template($item);
                            break;
                        case 'item_image': //(common)
                            if ($useimg) {
                                $this->render_item_image($item, $key);
                            }
                            break;
                        case 'item_avatar':
                            if ($useimg) {
                                $this->render_item_avatar($item);
                            }
                            break;
                    }

                    //----------------------------------
                    $this->render_repeateritem_end();
                }
                $this->itemindex = $key;
            }
        }
    }

    // REPEATER-ITEM start
    protected function render_repeateritem_start($item, $tag = 'div') {
        /* echo 'eadditem_' . $id . '_' . $item_type;
          $this->parent->add_render_attribute('eadditem_' . $id . '_' . $item_type, [
          'class' => [
          'e-add-item',
          'e-add-' . $item_type,
          'elementor-repeater-item-' . $id
          ],
          'data-item-id' => [
          $id
          ]
          ]
          ); */
        /*
          $width = '100';
          if (!empty($item['width'])) {
          $width = intval($item['width']);
          }
          $width = ' elementor-column elementor-col-'.$width;
          if ( ! empty( $item['width_tablet'] ) ) {
          $width .= ' elementor-md-' . $item['width_tablet'];
          }
          if ( ! empty( $item['width_mobile'] ) ) {
          $width .= ' elementor-sm-' . $item['width_mobile'];
          }

          if (!empty($item['display_inline']) && $item['display_inline'] == 'inline-block') {
          $width = '';
          }
         */

        $classItem = 'class="e-add-item e-add-' . $item['item_type'] . ' elementor-repeater-item-' . $item['_id'] . '"';
        $dataIdItem = ' data-item-id="' . $item['_id'] . '"';

        echo '<' . $tag . ' ' . $classItem . $dataIdItem /* $this->parent->get_render_attribute_string('eadditem_' . $id . '_' . $item_type) */ . '>';
    }

    // REPEATE-ITEM end
    protected function render_repeateritem_end($tag = 'div') {
        echo '</' . $tag . '>';
    }

    /////////////////////////////////////////////////////////////
    // render post item -----------------------------------------
    protected function render_item_start($key = 'post') {
        $hover_animation = $this->get_instance_value('hover_animation');
        $animation_class = !empty($hover_animation) ? ' elementor-animation-' . $hover_animation : '';

        $use_overlay_hover = $this->get_instance_value('use_overlay_hover');

        $_skin = $this->parent->get_settings_for_display('_skin');
        $style_items = $this->parent->get_settings_for_display('style_items');

        //@p in caso di justifiedgrid forzo lo style_items in "float"
        //@p probabilmente farò lo stesso anche per lo skin rhombus ex diamond
        if ($_skin == 'justifiedgrid') {
            $style_items = 'float';
        }
        //@p ..questo è il background per il block
        $overlayhover = '';
        if ($use_overlay_hover) {
            $overlayhover = ' e-add-post-overlayhover';
        }
        $hover_effects = $this->get_instance_value('hover_text_effect');
        $hoverEffects_class = !empty($hover_effects) && $style_items == 'float' ? ' e-add-hover-effects' : '';

        //@p data post ID
        $data_post_id = ' data-post-id="' . $this->current_id . '"';
        //@p data post INDEX
        $data_post_index = ' data-post-index="' . $this->counter . '"';
        //@p una classe personalizzata per lo skin
        $item_class = ' ' . $this->get_item_class();
        ?>
        <article <?php
        post_class(['e-add-post e-add-post-item e-add-post-item-' . $this->parent->get_id() . $item_class]);
        echo $data_post_id . $data_post_index;
        ?>>
            <div class="e-add-post-block e-add-post-block-<?php echo $this->counter . $overlayhover . $hoverEffects_class . $animation_class; ?>">

                <?php
            }

            protected function render_item_end() {
                ?>

            </div>
        </article>
        <?php
    }

    ////////////////////////////////////////////////////////////////
    // render loop wrapper -----------------------------------------
    protected function render_loop_start() {


        // TO DO
        /** @p qui prendo il valore di $query elaborato in query.php /base */
        /*
          $wp_query = $this->parent->get_query();

          // @p questa è una classe che c'è solo se ci sono posts, identifica se non è vuoto.
          if ( $wp_query->found_posts ) {
          $classes[] = 'e-add-grid';
          }
         */
        $this->parent->add_render_attribute('eaddposts_container', [
            'class' => [
                'e-add-posts-container',
                'e-add-posts',
                $this->get_scrollreveal_class(), //@p prevedo le classi per generare il reveal,
                $this->get_container_class(), //@p una classe personalizzata per lo skin
            ],
        ]);
        $this->parent->add_render_attribute('eaddposts_container_wrap', [
            'class' => [
                'e-add-posts-wrapper',
                $this->get_wrapper_class(), //@p una classe personalizzata per lo skin
            ],
        ]);
        ?>
        <?php $this->render_container_before(); ?>
        <div <?php echo $this->parent->get_render_attribute_string('eaddposts_container'); ?>>
            <?php $this->render_posts_before(); ?>
            <div <?php echo $this->parent->get_render_attribute_string('eaddposts_container_wrap'); ?>>
                <?php
                $this->render_postsWrapper_before();
            }

            protected function render_loop_end() {
                $this->render_postsWrapper_after();
                ?>
            </div>
            <?php
            $this->render_posts_after();
            ?>
        </div>
        <?php $this->render_container_after(); ?>
        <?php
    }

    protected function render_container_before() {
        
    }

    protected function render_container_after() {
        
    }

    protected function render_posts_before() {
        
    }

    protected function render_posts_after() {
        
    }

    protected function render_postsWrapper_before() {
        
    }

    protected function render_postsWrapper_after() {
        
    }

    // Classes ----------
    public function get_container_class() {
        return 'e-add-skin-' . $this->get_id();
    }

    public function get_wrapper_class() {
        return 'e-add-wrapper-' . $this->get_id();
    }

    public function get_item_class() {
        return 'e-add-item-' . $this->get_id();
    }

    public function get_image_class() {
        
    }

    public function get_scrollreveal_class() {
        return '';
    }

    // Utility ----------
    public function filter_excerpt_length() {
        return $this->get_instance_value('textcontent_limit');
    }

    public function filter_excerpt_more($more) {
        return '';
    }

    protected function limit_content($limit = 100) {
        $post = get_post();
        $content = $post->post_content; //do_shortcode($post['post_content']); //$content_post->post_content; //
        $content = $this->limit_text($content, $limit);
        return $content;
    }

    public function limit_text($content, $limit = 100, $extra = '...') {
        $content = wp_strip_all_tags($content);
        if (strlen($content) > $limit) {
            $content = substr($content, 0, $limit) . $extra; //
            // TODO preserve words
        }
        return $content;
    }

    public function get_item_link($settings) {
        if (!empty($settings['use_link'])) {
            switch ($settings['use_link']) {
                case 'custom':
                    if (!empty($settings['shortcode_link'])) {
                        $raw_settings = $this->parent->get_settings('list_items');
                        foreach ($raw_settings as $raw_setting) {
                            if ($raw_setting['_id'] == $settings['_id']) {
                                $type = $this->parent->get_querytype();
                                //var_dump($this->current_data); die();                                
                                $link = Utils::get_dynamic_data($raw_setting['shortcode_link'], $this->current_data, $type);
                                return $link;
                            }
                        }
                    }
                    break;
                case 'shortcode':
                    if (!empty($settings['shortcode_link'])) {
                        ob_start();
                        do_shortcode($settings['shortcode_link']);
                        $link = ob_get_clean();
                        return $link;
                    }
                    break;
                case 'popup':
                    if (!empty($settings['popup_link'])) {
                        $theme_builder = \Elementor\Plugin::instance()->modules_manager->get_modules('theme-builder');
                        if (Utils::is_plugin_active('elementor-pro')) {
                            if (!$theme_builder) {
                                $class_name = '\ElementorPro\Modules\ThemeBuilder\Module';
                                /** @var Module_Base $class_name */
                                if ($class_name::is_active()) {
                                    $theme_builder = $class_name::instance();
                                }
                            }
                        }
                        if ($theme_builder) {
                            $popup_id = $settings['popup_link'];
                            $link_action_url = \Elementor\Plugin::instance()->frontend->create_action_hash('popup:open', [
                                'id' => $popup_id,
                                'toggle' => true,
                            ]);
                            $link_action_url = str_replace(':', '%3A', $link_action_url);
                            $link_action_url = str_replace('=', '%3D', $link_action_url);
                            $link_action_url = str_replace('%3D%3D', '%3D', $link_action_url);
                            $link_action_url = str_replace('&', '%26', $link_action_url);
                            //
                            $theme_builder->get_locations_manager()->add_doc_to_location('popup', $popup_id);
                            return $link_action_url;
                        }
                    }
                    break;
                case 'custom':
                    if (!empty($settings['custom_link']['url'])) {
                        // TODO: generate dynamic url per each item
                        return $settings['custom_link']['url'];
                    }
                    break;
                case 'yes':
                default:
                    if (!is_wp_error($this->current_permalink)) {
                        return $this->current_permalink;
                    }
            }
        }
        return false;
    }

    public function get_index_start() {
        $paged = Utils::get_current_page_num();
        $offset = intval($this->parent->get_settings_for_display('posts_offset'));
        if ($paged > 1) {
            if ($this->parent->get_settings_for_display('pagination_enable')) {   
                $querytype = $this->parent->get_querytype();
                switch ($querytype) {
                    case 'attachment':
                    case 'post':
                        $no = $this->parent->get_settings_for_display('posts_per_page');
                        break;
                    case 'user':
                        $no = $this->parent->get_settings_for_display('users_per_page');
                        break;
                    case 'term':
                        $no = $this->parent->get_settings_for_display('terms_per_page');
                        break;
                }
                if (!$no || $no == -1 || $no == '-1') {
                    $no = get_option('posts_per_page');
                }
                $start = $no * ($paged-1) + $offset;
                //var_dump($start);
                return $start;
            }
        }
        return $offset;
    }

}
