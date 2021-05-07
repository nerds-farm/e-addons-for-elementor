<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;

/**
 * Description of repeater
 *
 * @author poglie
 */
trait Repeater {

    // -------------- Repeater Field ---------
    public function controls_repeaterfield_content($target) {
        
        $target->add_control(
            'acf_repeater_field',
            [
                'label' => '<b>'.__('The field Repeater (ACF)', 'e-addons').'</b>',
                'type' => 'e-query',
                'display_label' => false,
                'placeholder' => __('Select the Field', 'e-addons'),
                'label_block' => true,
                'query_type' => 'acf',

            ]
        );
    }
}
