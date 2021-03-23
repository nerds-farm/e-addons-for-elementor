<?php

namespace EAddonsForElementor\Base;

use Elementor\Element_Base;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Form;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!Utils::is_plugin_active('elementor-pro') || !class_exists('ElementorPro\Modules\Forms\Classes\Action_Base')) {

    class Base_Action extends Element_Base {

        use \EAddonsForElementor\Base\Traits\Base;
    }

} else {

    class Base_Action extends \ElementorPro\Modules\Forms\Classes\Action_Base {

        use \EAddonsForElementor\Base\Traits\Base;

        public function __construct() {
            //parent::__construct();

            add_filter('e_addons/dynamic', [$this, 'filter_form'], 10, 3);
        }

        public function filter_form($value = '', $fields = array(), $var = 'form') {
            //var_dump($value); die();
            $value = \EAddonsForElementor\Core\Utils\Form::do_setting_shortcodes($value, $fields);
            $value = \EAddonsForElementor\Core\Utils\Form::replace_content_shortcodes($value, $fields);
            return $value;
        }

        /**
         * Get Label
         *
         * Returns the action label
         *
         * @access public
         * @return string
         */
        public function get_label() {
            return __('e-addons Form PRO Action', 'e-addons-for-elementor');
        }

        /**
         * Register Settings Section
         *
         * Registers the Action controls
         *
         * @access public
         * @param \Elementor\Widget_Base $widget
         */
        public function register_settings_section($widget) {

        }

        /**
         * Run
         *
         * Runs the action after submit
         *
         * @access public
         * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
         * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
         */
        public function run($record, $ajax_handler) {
            $fields = Form::get_form_data($record);
            $settings = $record->get('form_settings');
        }

        public function get_settings($dynamic = true, $fields = array()) {
            $post_id = !empty($_POST['post_id']) ? absint($_POST['post_id']) : false;
            $form_id = $this->get_form_id();
            $document = $widget = false;
            $settings = array();
            if ($form_id) {
                if ($post_id) {
                    $document = \Elementor\Plugin::$instance->documents->get($post_id);
                    if ($document) {
                        $form = \ElementorPro\Modules\Forms\Module::find_element_recursive($document->get_elements_data(), $form_id);
                        if ($form) {
                            $widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance($form);
                        }
                    }
                }
                if (!$widget) {
                    $widget = \EAddonsForElementor\Core\Utils::get_element_instance_by_id($form_id);
                }
            }
            if ($widget) {
                if ($dynamic) {
                    $settings = $widget->get_settings_for_display();
                } else {
                    $settings = $widget->get_settings();
                }
            }
            if ($dynamic) {
                $settings = Utils::get_dynamic_data($settings, $fields, 'form');
            }
            return $settings;
        }

        public function get_form_id($settings = array()) {
            if (!empty($settings['id'])) {
                return $settings['id'];
            }
            if (!empty($_REQUEST['form_id'])) {
                return sanitize_key($_REQUEST['form_id']);
            }
            return false;
        }

        /**
         * On Export
         *
         * Clears form settings on export
         * @access Public
         * @param array $element
         */
        public function on_export($element) {
            $tmp = array();
            if (!empty($element)) {
                foreach ($element as $key => $value) {
                    if (substr($key, 0, 2) == 'e_') {
                        $element[$key];
                    }
                }
            }
        }

        public function start_controls_section($widget) {
            $widget->start_controls_section(
                    'section_' . $this->get_name(),
                    [
                        'label' => '<i class="eadd-logo-e-addons eadd-ic-right"></i>' . $this->get_label(),
                        'condition' => [
                            'submit_actions' => $this->get_name(),
                        ],
                    ]
            );
        }

        public function fields_filter($fields) {
            $tmp = array();
            if (!empty($fields) && is_array($fields)) {
                foreach ($fields as $akey => $adata) {
                    if ($adata != '') {
                        $tmp[$akey] = $adata;
                    }
                }
            }
            return $tmp;
        }

        public function get_obj_id($obj_id, $type, $ajax_handler) {
            $obj_id = Utils::get_dynamic_data($obj_id);
            if (is_string($obj_id) && is_numeric($obj_id)) {
                $obj_id = intval($obj_id);
            }
            if (!$obj_id) {
                $ajax_handler->add_error_message(__($type . ' ID not valid', 'e-addons'));
                return false;
            }
            switch ($type) {
                case 'post':
                    $obj_check = get_post($obj_id);
                    break;
                case 'user':
                    $obj_check = get_user_by('ID', $obj_id);
                    break;
                case 'term':
                    $obj_check = get_term($obj_id);
                    break;
                case 'comment':
                    $obj_check = get_comment($obj_id);
                    break;
            }

            if (!$obj_check) {
                $ajax_handler->add_error_message(__($type . ' not exist', 'e-addons'));
                return false;
            }
            return $obj_id;
        }

    }

}
