<?php

namespace EAddonsForElementor\Base;

use EAddonsForElementor\Core\Utils;
use Elementor\Element_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class Base_Extension extends Element_Base {

    public $common = false;
    public $common_sections_actions = array(
        'common' => array(
            'element' => 'common',
            'action' => '_section_style',
        ),
        'widget' => array(
            'element' => 'widget',
            'action' => '_section_style',
        ),
        'section' => array(
            'element' => 'section',
            'action' => 'section_advanced',
        ),
        'column' => array(
            'element' => 'column',
            'action' => 'section_advanced',
        )
    );
    public static $common_sections = [];

    use \EAddonsForElementor\Base\Traits\Base;

    public function __construct() {
        parent::__construct();

        // Add the advanced section required to display controls
        if ($this->common && !empty($this->common_sections_actions)) {
            foreach ($this->common_sections_actions as $action) {
                //Activate action for elements
                if ($action['element'] == 'common' || $action['element'] == 'widget') {
                    add_action('elementor/element/after_section_end', [$this, '_add_sections'], 11, 3);
                } else {
                    add_action('elementor/element/' . $action['element'] . '/' . $action['action'] . '/after_section_end', [$this, '_add_common_sections'], 11, 2);
                }
            }
        }
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue']);
    }

    public function _add_sections($element, $section_id, $args) {

        $stack_name = $element->get_name();

        if ($element->get_name() != 'common' && isset($this->common_sections_actions[$element->get_type()])
                //&& $this->common_sections_actions[$element->get_type()] == $section_id
                || ($element->get_name() == 'common' && isset($this->common_sections_actions['common']))
        ) {
            if (isset($this->common_sections_actions['common']) && in_array($element->get_type(), array('section', 'column'))) {
                if ($section_id != $this->get_section_name()) {
                    return false;
                }
            }
            //echo ' -- '; var_dump($element->get_type()); var_dump($stack_name); var_dump($section_id);
            $this->add_common_sections($element, $args);
        }
    }

    public function _add_common_sections($element, $args) {
        $this->add_common_sections($element, $args);
    }

    public function add_common_sections($element, $args) {

        $section_name = $this->get_section_name();

        if (!empty(self::$common_sections[$element->get_type()]) && in_array($section_name, self::$common_sections[$element->get_type()])) {
            return false;
        }

        // Check if this section exists
        //$section_exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack($element->get_unique_name(), $section_name);
        $section_exists = $element->get_controls($section_name);
        //if (!is_wp_error($section_exists)) {
        if (!empty($section_exists)) {
            return false;
        }

        $element->start_controls_section(
                $section_name, [
            'tab' => Controls_Manager::TAB_ADVANCED,
            'label' => '<i class="eadd-logo-e-addons eadd-ic-right"></i>' . ucwords(__($this->get_name(), 'e-addons-for-elementor')),
                ]
        );
        $element->end_controls_section();
        self::$common_sections[$element->get_type()][] = $section_name;
    }

    public function get_section_name() {
        return 'e_section_' . $this->get_name() . '_advanced';
    }

    public function add_heading($element, $heading = 'e-addons', $slug = '') {

        if (!$slug) {
            $slug = Utils::camel_to_slug($heading);
        }
        $control_id = 'heading_e_addons_' . $slug;

        // Check if this control exists
        $control_exists = $element->get_controls($control_id);
        if (!empty($control_exists)) {
            return false;
        }

        $element->add_control(
                $control_id,
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eadd-logo-e-addons" aria-hidden="true"></i> <b>' . __($heading, 'e-addons-for-elementor') . '</b>',
                    'separator' => 'before',
                ]
        );
    }

    /**
     * Get widget icon.
     *
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eadd-logo-e-add';
    }

}
