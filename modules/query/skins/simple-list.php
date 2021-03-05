<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use EAddonsForElementor\Modules\Query\Skins\Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Table Skin
 *
 * Elementor widget query-posts for e-addons
 *
 */
class Simple_List extends Base {

    public function _register_controls_actions() {
        parent::_register_controls_actions();
        add_action('elementor/element/' . $this->parent->get_name() . '/section_e_query/after_section_end', [$this, 'register_additional_controls'], 20);
    }

    public function get_style_depends() {
        return ['e-addons-common-query'];
    }

    public function get_id() {
        return 'list';
    }

    public function get_title() {
        return __('List', 'e-addons');
    }

    public function get_icon() {
        return 'eadd-skin-list';
    }

    public function register_additional_controls() {
        //var_dump($this->get_id());
        //var_dump($this->parent->get_settings('_skin')); //->get_current_skin()->get_id();

        $this->start_controls_section(
                'section_list', [
            'label' => '<i class="eaddicon eadd-skin-list"></i> ' . __('List', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control(
                'type',
                [
                    'label' => __('Ordered or Unordered', 'e-addons'),
                    'type' => Controls_Manager::CHOOSE,
                    'toggle' => false,
                    'options' => [
                        'ul' => [
                            'title' => __('Unordered', 'e-addons'),
                            'icon' => 'eicon-editor-list-ul',
                        ],
                        'ol' => [
                            'title' => __('Ordered', 'e-addons'),
                            'icon' => 'eicon-editor-list-ol',
                        ],
                    ],
                    'default' => 'ul',
                ]
        );

        $this->end_controls_section();
    }

    protected function register_style_controls() {
        parent::register_style_controls();

        $this->start_controls_section(
                'section_style_table',
                [
                    'label' => '<i class="eaddicon eadd-skin-list"></i> ' . __('List', 'e-addons'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->end_controls_section();
    }

    protected function render_element_item() {

        $this->render_item_start();

        $this->render_items();

        $this->render_item_end();

        $this->counter++;
    }

    public function get_container_class() {
        return 'e-add-skin-' . $this->get_id();
    }

    protected function render_loop_start() {
        $this->parent->add_render_attribute('eaddposts_container', [
            'class' => [
                'e-list',
                'e-add-posts-container',
                'e-add-posts',
                $this->get_scrollreveal_class(), //@p prevedo le classi per generare il reveal,
                $this->get_container_class(), //@p una classe personalizzata per lo skin
            ],
        ]);
        ?>
        <?php
        echo '<ul ' . $this->parent->get_render_attribute_string('eaddposts_container') . '>';
    }

    protected function render_loop_end() {
        echo '</ul>';
    }

    public function render_item_start($key = 'post') {
        //@p data post ID
        $data_post_id = ' data-e-add-id="' . $this->current_id . '"';
        //@p data post INDEX
        $data_post_index = ' data-e-add-index="' . $this->counter . '"';
        //@p una classe personalizzata per lo skin
        $item_class = ' ' . $this->get_item_class();
        ?>
        <li<?php
        echo ' class="e-add-item e-add-item-' . $this->parent->get_id() . $item_class . '"';
        //post_class(['e-add-post e-add-post-item e-add-post-item-' . $this->parent->get_id() . $item_class]);
        echo $data_post_id . $data_post_index;
        ?>><?php
        }

        public function render_item_end() {
            echo '</li>';
        }

        /* protected function render_items() {
          $_skin = $this->parent->get_settings_for_display('_skin');
          $this->render_items_content();
          } */

        protected function render_repeateritem_start($id, $item_type) {
            $classItem = 'class="e-add-item e-add-' . $item_type . ' elementor-repeater-item-' . $id . '"';
            $dataIdItem = ' data-item-id="' . $id . '"';
            echo '<div ' . $classItem . $dataIdItem . '>';
        }

        protected function render_repeateritem_end() {
            echo '</div>';
        }

    }
    