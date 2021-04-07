<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use EAddonsForElementor\Core\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

/**
 * Description of term
 *
 * @author fra
 */
trait Term {

    // TERMS
    protected function render_item_postscount($settings) {
        $term_info = $this->current_data;
        //var_dump($term_info);
        //
        if( !empty($term_info->count) ){
            
            $html_tag = !empty($settings['html_tag']) ? $settings['html_tag'] : 'span';
            echo sprintf('<%1$s class="e-add-term-count">', $html_tag);
            echo $this->render_label_before_item($settings,'Posts: ');
            echo $term_info->count;
            echo sprintf('</%s>', $html_tag);
            echo $this->render_label_after_item($settings);
        }
    }

    protected function render_item_taxonomy($settings) {
        $term_info = $this->current_data;
        $taxonomy_label = $settings['taxonomy_label']; //plural - singular
        //var_dump($taxObj);
        switch ($taxonomy_label) {
            case 'plural' :
                $tax = get_taxonomy($term_info->taxonomy)->labels->name;
                break;
            case 'singular' :
            default:
                $tax = get_taxonomy($term_info->taxonomy)->labels->singular_name;
                break;
        }
        echo $tax;
    }

    protected function render_item_description($settings) {
        $textcontent_limit = $settings['textcontent_limit'];
        $term_info = $this->current_data;
        $description_content = $term_info->description;
        $description_content = Utils::get_dynamic_data($description_content);

        if ($description_content)
            if ($textcontent_limit) {
                echo substr(wp_strip_all_tags($description_content), 0, $textcontent_limit) . ' ...';
            } else {
                echo $description_content;
            }
    }

}
