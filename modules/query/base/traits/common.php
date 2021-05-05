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
        
    }

}
