<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

/**
 * Description of label
 *
 * @author fra
 */
trait Common {

    // -------------- Label Html ---------
    public function controls_items_common_content($target) {
        $target->add_control(
                'item_text_label', [
            'label' => __('Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
                ]
        );
        
        $target->add_responsive_control(
                'width',
                [
                    'label' => __('Column Width', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => __('Default', 'elementor'),
                        '100' => '100%',
                        '80' => '80%',
                        '75' => '75%',
                        '70' => '70%',
                        '66' => '66%',
                        '60' => '60%',
                        '50' => '50%',
                        '40' => '40%',
                        '33' => '33%',
                        '30' => '30%',
                        '25' => '25%',
                        '20' => '20%',
                    ],
                ]
        );
    }

}
