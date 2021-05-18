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
                'metafield_key',
                [
                    'label' => '<b>' . __('Repeater Sub Field', 'e-addons') . '</b>',
                    'type' => 'e-query',
                    'display_label' => false,
                    'placeholder' => __('Select the Field', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'custom_fields',
                    'condition' => [
                        'item_type!' => 'item_label'
                    ]
                ]
        );
    }

}
