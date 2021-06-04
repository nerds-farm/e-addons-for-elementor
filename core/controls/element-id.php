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
class Element_Id extends \Elementor\Base_Data_Control {
    
    const CONTROL_TYPE = 'element-id';

    /**
     * Get control type.
     *
     * Retrieve the control type, in this case `FILESELECT`.
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
        wp_enqueue_script('e-addons-editor-control-element-id', E_ADDONS_URL.'assets/js/e-addons-editor-control-element-id.js');
        wp_enqueue_style('e-addons-editor-control-element-id', E_ADDONS_URL.'assets/css/e-addons-editor-control-element-id.css');
    }

    /**
     * Get default settings.
     *
     * @since 1.0.0
     * @access protected
     *
     * @return array Control default settings.
     */
    protected function get_default_settings() {
        return [
            'label_block' => true,
            'dynamic' => [
                    'active' => true,
                    'categories' => [
                            TagsModule::BASE_GROUP,
                            TagsModule::TEXT_CATEGORY,
                    ],
            ],
        ];
    }

    /**
     * Render control output in the editor.
     *
     * @since 1.0.0
     * @access public
     */
    public function content_template() {
        $control_uid = $this->get_control_uid();
        ?>
        <div class="elementor-control-field">
            <# if ( data.label ) {#>
                <label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
            <# } #>
            <div class="elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper elementor-control-unit-5">
                <input type="text" class="elementor-input  elementor-control-tag-area e-elemend-id" id="<?php echo esc_attr($control_uid); ?>" data-setting="{{ data.name }}">
                <div class="elementor-control-element-id-target tooltip-target elementor-control-unit-1" data-tooltip="<?php echo __( 'Select Element', 'elementor' ); ?>">
                        <i class="eicon-drag-n-drop" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="elementor-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }

}
