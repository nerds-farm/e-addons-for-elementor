<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;

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
    public function controls_items_grid_debug($target) {
        $target->add_control(
            'items_grid_debug', [
                'label' => '<span style="color: #fff; background-color: #93003c; padding: 5px 10px; border-radius: 20px;">' . __('Show grid for DEBUG', 'e-addons') . '</span>',
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'e-add-grid-debug-',
                'separator' => 'before',
                'condition' => [
                    '_skin!' => ['table'],
                ],
            ]
        );
        
    }

}
