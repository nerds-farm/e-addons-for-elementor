<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

/**
 * Description of Item
 *
 * @author fra
 */
trait Item {
    
    protected function render_item_subtitle($settings) {
        if(!empty($this->current_data['sl_subtitle'])){
            echo '<span class="e-add-subtitleitem">';
            echo $this->current_data['sl_subtitle'];
            echo '</span>';
        }
    }

    protected function render_item_descriptiontext($settings) {
        if(!empty($this->current_data['sl_descriptiontext'])){
            echo '<span class="e-add-descriptiontext">';
            echo $this->current_data['sl_descriptiontext'];
            echo '</span>';
        }
        
    }

    protected function render_item_imageoricon($settings) {
        $imageoricon = '';
        if(!empty($this->current_data['sl_image_or_icon'])){
            switch ($this->current_data['sl_image_or_icon']) {
                case 'icon':
                    $imageoricon = $this->render_item_icon($settings ,'sl_icon' ,'slicon','e-add-query-icon');
                    break;
                case 'image':
                    $imageoricon = $this->render_item_image($settings);
                    break;
                }
        }    
        echo $imageoricon;
    }
}
