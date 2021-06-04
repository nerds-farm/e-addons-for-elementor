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
class File extends \Elementor\Base_Data_Control {
    
    const CONTROL_TYPE = 'file';

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
        wp_enqueue_media();
        wp_enqueue_style('thickbox');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        // Scripts
        wp_enqueue_script('e-addons-editor-control-file', E_ADDONS_URL.'assets/js/e-addons-editor-control-file.js');
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
                            TagsModule::MEDIA_CATEGORY,
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
            <label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
            <div class="elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper elementor-control-unit-5">
                <div class="elementor-control-tag-area">
                    <# var placeholder = ( data.placeholder ) ? data.placeholder : 'Choose / Upload File'; #>
                    <# var multiple = ( data.multiple ) ? 'true' : 'false'; #>
                    <input type="text" class="e-selected-file" id="<?php echo esc_attr($control_uid); ?>" data-setting="{{ data.name }}" data-multiple="{{ multiple }}">
                    <a href="#" class="e-select-file elementor-button elementor-button-default" id="select-file-<?php echo esc_attr($control_uid); ?>" >
                        <i class="eicon-document-file"></i> {{ placeholder }}
                    </a>
                    
                </div>
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="elementor-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }

}
