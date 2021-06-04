<?php

namespace EAddonsForElementor\Core\Controls;

use Elementor\Base_Data_Control;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor UI (image or icon) Controls.
 * Questo control ha lo scopo di visualizzarre una lista di elementigrefici basati
 * su immagini o icone per un'approccio visivo migliore, di più facile comprensione.
 *
 * @since 1.0.0
 */
class Ui_Selector extends Base_Data_Control {

    const CONTROL_TYPE = 'ui_selector';

    /**
     * Get control type.
     *
     * Retrieve the control type, in this case `UI SELECTORS`.
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
        // Style
        wp_enqueue_style('e-addons-editor-control-ui-selector', E_ADDONS_URL.'assets/css/e-addons-editor-control-ui-selector.css');
        // Scripts
        wp_enqueue_script('e-addons-editor-control-ui-selector', E_ADDONS_URL.'assets/js/e-addons-editor-control-ui-selector.js');
    }

    /**
     * Render control output in the editor.
     *
     * @since 1.0.0
     * @access public
     */
    public function content_template() {
        $control_uid = $this->get_control_uid('{{value}}');
        ?>
        <div class="elementor-control-field">
            <label class="elementor-control-title">{{{ data.label }}}</label>

            <# if ( data.description ) { #>
            <div class="elementor-control-field-description">{{{ data.description }}}</div>
            <# } #>

            <div class="elementor-control-input-wrapper">
                <div class="elementor-uiselector elementor-uiselector-type-{{{ data.type_selector }}}">
                    <# _.each( data.options, function( options, value ) { 
                    var valueItem = value;

                    if( (data.return_val == 'image' && options.return_val != 'val') ||  options.return_val == 'image'){
                    valueItem = options.image;
                    }else if( (data.return_val == 'icon' && options.return_val != 'val') || options.return_val == 'icon' ){
                    valueItem = options.icon;
                    }
                    imageItem = options.image;

                    if(options.image_preview){
                    imageItem = options.image_preview;
                    }
                    #>
                    <div class="elementor-uiselector-item elementor-uiselector-column-{{{ columns_grid }}}" data-column-grid="{{{ columns_grid }}}">
                        <input id="<?php echo $control_uid; ?>" type="radio" name="elementor-uiselector-{{ data.name }}-{{ data._cid }}" value="{{ valueItem }}">
                        <label class="elementor-uiselector-label elementor-control-unit-1 tooltip-target" for="<?php echo $control_uid; ?>" data-tooltip="{{ options.title }}" title="{{ options.title }}">
                            <# if( data.type_selector == 'icon' ){ #>
                            <i class="{{ options.icon }}" aria-hidden="true"></i>
                            <# }else if( data.type_selector == 'image' ){ #>
                            <img src="{{ imageItem }}" />
                            <# }else if( data.type_selector == 'bgimage' ){ #>
                            <div class="elementor-uiselector-bgimage" style="background-image:url({{ imageItem }};" />
                            <# } #>
                            <span class="elementor-screen-only">{{{ options.title }}}</span>
                        </label>
                    </div>
                    <# } ); #>
                </div>
            </div>
        </div>
        <?php
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
            'columns_grid' => 3,
            'type_selector' => 'image',
            'return_val' => 'image',
            'options' => [],
            'toggle' => true,
        ];
    }

}
