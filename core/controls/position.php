<?php

namespace EAddonsForElementor\Core\Controls;

use Elementor\Control_Base_Multiple;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Get control type.
 *
 * Retrieve the control type, in this case `position`.
 *
 * @since 1.0.0
 * @access public
 *
 * @return string Control type.
 */
class Position extends Control_Base_Multiple {

    /**
     * Get control type.
     *
     * Retrieve the control type, in this case `Position`.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Control type.
     */
    public function get_type() {
        return 'position';
    }

    public function enqueue() {
        // Style
        //wp_enqueue_style('e-addons-editor-control-position');
        // Scripts
        wp_enqueue_script('e-addons-editor-control-position', E_ADDONS_URL . 'assets/js/e-addons-editor-control-position.js');
    }

    /**
     * Get position control default value.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Control default value.
     */
    public function get_default_value() {
        return array_merge(
                parent::get_default_value(), [
            'x' => '',
            'y' => '',
                ]
        );
    }

    /* public function get_default_value() {
      return parent::get_default_value();
      } */

    protected function get_default_settings() {
        return array_merge(
                parent::get_default_settings(), [
            'label_block' => false,
                ]
        );
    }

    /**
     * Get position control sliders.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Control sliders.
     */
    public function get_sliders() {
        return [
            'x' => [
                'label' => 'X',
                'min' => 0,
                'max' => 100,
                'step' => 1
            ],
            'y' => [
                'label' => 'Y',
                'min' => 0,
                'max' => 100,
                'step' => 1
            ],
        ];
    }

    /**
     * Render position control output in the editor.
     *
     * @since 1.0.0
     * @access public
     */
    public function content_template() {
        $control_uid = $this->get_control_uid();
        ?>
        <div class="elementor-control-field elementor-control-field-position">
            <label class="elementor-control-title control-title-first control-title-first-position">{{{ data.label }}}</label>
            <button href="#" class="e-add-reset-controls" title="Reset"><i class="fa fa-close"></i></button>
        </div>
        <?php
        foreach ($this->get_sliders() as $slider_name => $slider) :
            $control_uid = $this->get_control_uid($slider_name);
            ?>
            <div class="elementor-control-field elementor-control-type-slider elementor-control-type-slider-position">
                <label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title-position"><?php echo $slider['label']; ?></label>
                <div class="elementor-control-input-wrapper">
                    <div class="elementor-slider" data-input="<?php echo esc_attr($slider_name); ?>"></div>
                    <div class="elementor-slider-input">
                        <input id="<?php echo esc_attr($control_uid); ?>" type="number" min="<?php echo esc_attr($slider['min']); ?>" max="<?php echo esc_attr($slider['max']); ?>" step="<?php echo esc_attr($slider['step']); ?>" data-setting="<?php echo esc_attr($slider_name); ?>"/>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php
    }

}
