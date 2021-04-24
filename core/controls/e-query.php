<?php

namespace EAddonsForElementor\Core\Controls;

use \Elementor\Control_Select2;
use \Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Control Query
 */
class E_Query extends Control_Select2 {

    const CONTROL_TYPE = 'e-query';

    /**
     * Module constructor.
     *
     * @since 1.0.1
     * @param array $args
     */
    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    /**
     * Get control type.
     *
     * @since 1.0.1
     * @access public
     *
     * @return string Control type.
     */
    public function get_type() {
        return self::CONTROL_TYPE;
    }

    /**
     * Get e-query control default settings.
     *
     * Retrieve the default settings of the text control. Used to return the
     * default settings while initializing the text control.
     *
     * @since 1.0.1
     * @access public
     *
     * @return array Control default settings.
     */
    public function get_default_settings() {
        $settings = parent::get_default_settings();
        //$settings['sortable'] = false;
        $settings['dynamic'] = [
            'active' => true,
            'categories' => [
                'base', //TagsModule::BASE_GROUP,
                'text', //TagsModule::TEXT_CATEGORY,
                'number', //TagsModule::NUMBER_CATEGORY,
            ],
        ];
        return $settings;
    }

    /**
     * Render e-query control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     * @since 1.0.1
     * @access public
     */
    public function content_template() {
        ob_start();
        parent::content_template();
        $template = ob_get_clean();
        $template = str_replace('elementor-control-input-wrapper', 'elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper', $template);
        $template = str_replace('elementor-select2', 'elementor-select2 elementor-control-tag-area', $template);
        echo $template;
    }

    /**
     * Add Actions
     * 
     * Registeres actions to Elementor hooks
     *
     * @since  1.0.1
     * @return void
     */
    public function add_actions() {
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'elementor_editor_after_enqueue_scripts']);
    }

    /**
     * Enqueue editor assets
     *
     * @since 1.0.1
     *
     * @access public
     */
    public function elementor_editor_after_enqueue_scripts() {
        wp_enqueue_style('e-addons-editor-control-e-query');
        wp_enqueue_script('e-addons-editor-control-e-query');
    }

    /**
     * Enqueue control scripts and styles.
     */
    public function enqueue() {
        if ($this->get_settings('sortable')) {
            wp_enqueue_script('jquery-ui-sortable');
        }
    }

    /**
     * @param string|array $value
     * @param array $config
     *
     * @return string|array
     */
    public function before_save($value, array $config) {
        return $value;
    }

}
