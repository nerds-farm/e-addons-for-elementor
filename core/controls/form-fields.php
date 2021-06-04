<?php

namespace EAddonsForElementor\Core\Controls;

use \Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * FileSelect control.
 *
 * A control for selecting any type of files.
 *
 * @since 1.0.0
 */
class Form_Fields extends \Elementor\Control_Select {
    
    const CONTROL_TYPE = 'form_fields';

    /**
     * Get control type.
     *
     * Retrieve the control type, in this case `form_fields`.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Control type.
     */
    public function get_type() {
        return self::CONTROL_TYPE;
    }

    /**
     * Enqueue control scripts and styles.
     *
     * Used to register and enqueue custom scripts and styles
     * for this control.
     *
     * @since 1.0.0
     * @access public
     */
    public function enqueue() {
        wp_enqueue_style('e-addons-editor-control-form-fields', E_ADDONS_URL.'assets/css/e-addons-editor-control-form-fields.css');
        // Scripts
        wp_enqueue_script('e-addons-editor-control-form-fields', E_ADDONS_URL.'assets/js/e-addons-editor-control-form-fields.js');
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
        $template = str_replace('<select ', '<# var multiple = ( data.multiple ) ? \'multiple\' : \'\'; #><# var select_type = ( data.multiple ) ? \'select2\' : \'select\'; #><select class="elementor-{{ select_type }}" type="{{ select_type }}" {{ multiple }} ', $template);
        echo $template;
    }

}
