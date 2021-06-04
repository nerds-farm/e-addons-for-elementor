<?php

namespace EAddonsForElementor\Core\Controls;

use Elementor\Control_Base_Units;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Transformations control.
 *
 * A control for transforming properties: scale x-y-z rotation and position.
 *
 * @since 1.0.0
 */
class Transformations extends Control_Base_Units {

    /**
     * Get control type.
     *
     * Retrieve the control type, in this case `transformations`.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Control type.
     */
    public function get_type() {
        return 'transformations';
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
        wp_enqueue_style('e-addons-editor-control-transformations', E_ADDONS_URL.'assets/css/e-addons-editor-control-transformations.css');
        // Scripts
        wp_enqueue_script('e-addons-editor-control-transformations', E_ADDONS_URL.'assets/js/e-addons-editor-control-transformations.js');
    }

    /**
     * Get transformations control default value.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Control default value.
     */
    /*
      public function get_default_value() {
      return [
      'angle' => 0,
      'rotate_x' => 0,
      'rotate_y' => 0,
      'translate_x' => 0,
      'translate_y' => 0,
      'translate_z' => 0,
      'scale' => 1,
      ];
      }
     */
    public function get_default_value() {
        return array_merge(
                parent::get_default_value(), [
            'angle' => 0,
            'rotate_x' => 0,
            'rotate_y' => 0,
            'translate_x' => 0,
            'translate_y' => 0,
            'translate_z' => 0,
            'scale' => 1,
            'sizes' => [],
                ]
        );
    }

    /*
      protected function get_default_settings() {
      return [
      'show_label' => true,
      'label_block' => false,
      ];
      }
     */

    protected function get_default_settings() {
        return array_merge(
                parent::get_default_settings(), [
            'show_label' => true,
            'label_block' => false,
                ]
        );
    }

    /**
     * Get transformations control sliders.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Control sliders.
     */
    public function get_sliders() {
        return [
            'angle' => [
                'label' => __('Angle', 'e-addons-for-elementor'),
                'min' => -360,
                'max' => 360,
                'step' => 1
            ],
            'rotate_x' => [
                'label' => __('Rotate X', 'e-addons-for-elementor'),
                'min' => -360,
                'max' => 360,
                'step' => 1
            ],
            'rotate_y' => [
                'label' => __('Rotate Y', 'e-addons-for-elementor'),
                'min' => -360,
                'max' => 360,
                'step' => 1
            ],
            'translate_x' => [
                'label' => __('X (%)', 'e-addons-for-elementor'),
                'size_units' => ['px', '%'],
                'min' => -200,
                'max' => 200,
                'step' => 1
            ],
            'translate_y' => [
                'label' => __('Y (%)', 'e-addons-for-elementor'),
                'min' => -200,
                'max' => 200,
                'step' => 1
            ],
            'translate_z' => [
                'label' => __('Z (px)', 'e-addons-for-elementor'),
                'min' => -1000,
                'max' => 1000,
                'step' => 1
            ],
            'scale' => [
                'label' => __('Scale', 'e-addons-for-elementor'),
                'min' => 0.1,
                'max' => 3,
                'step' => 0.1
            ],
        ];
    }

    /**
     * Render transformations control output in the editor.
     *
     * @since 1.0.0
     * @access public
     */
    public function content_template() {
        $control_uid = $this->get_control_uid();
        ?>
        <div class="elementor-control-field elementor-control-field-transformations">
            <label class="elementor-control-title control-title-first control-title-first-transformations">{{{ data.label }}}</label>
            <button href="#" class="e-add-reset-controls" title="Reset"><i class="fa fa-close"></i></button>
        </div>
        <?php
        foreach ($this->get_sliders() as $slider_name => $slider) :
            $control_uid = $this->get_control_uid($slider_name);
            ?>
            <?php $this->print_units_template(); ?>
            <div class="elementor-control-field elementor-control-type-slider elementor-control-type-slider-transformations">
                <label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title-transformations"><?php echo $slider['label']; ?></label>
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
