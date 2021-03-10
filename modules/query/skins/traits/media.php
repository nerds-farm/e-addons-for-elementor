<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;

/**
 * Description of Common
 *
 * @author fra
 */
trait Media {

    protected function render_item_caption($settings) {
        //var_dump($this->current_data);
        //echo wp_get_attachment_caption( get_the_ID() );    
        if ($this->current_data->post_excerpt)
            echo '<' . $settings['html_tag'] . '>' . $this->current_data->post_excerpt . '</' . $settings['html_tag'] . '>';
    }

    protected function render_item_mimetype($settings) {
        if ($this->current_data->post_mime_type)
            echo '<' . $settings['html_tag'] . '>' . $this->current_data->post_mime_type . '</' . $settings['html_tag'] . '>';
    }

    protected function render_item_alternativetext($settings) {
        $alt = get_post_meta(get_the_ID(), '_wp_attachment_image_alt', TRUE);
        if ($alt)
            echo '<' . $settings['html_tag'] . '>' . $alt . '</' . $settings['html_tag'] . '>';
    }

    protected function render_item_imagemeta($settings) {

        //var_dump(wp_get_attachment_metadata($this->current_data->ID)['image_meta']);
        //var_dump(wp_get_attachment_metadata($this->current_data->ID));
        $metadata = wp_get_attachment_metadata($this->current_data->ID);
        $sizeim = $settings['imagemedia_sizes'];
        $metas = $settings['imagemedia_metas'];

        if (!empty($metas))
            foreach ($metas as $m) {
                echo '<div class="e-add-imagemeta e-add-imagemeta-' . $m . '">';
                if ($m == 'dimension') {
                    if ($sizeim == 'full') {
                        echo $metadata['width'] . 'px x ' . $metadata['height'] . 'px';
                    } else {
                        echo $metadata['sizes'][$sizeim]['width'] . 'px x ' . $metadata['sizes'][$sizeim]['height'] . 'px';
                    }
                }
                if ($m == 'file') {
                    if ($sizeim == 'full') {
                        echo $metadata['file'];
                    } else {
                        echo $metadata['sizes'][$sizeim]['file'];
                    }
                }
                //@p todo: EXIF
                /*
                  ["image_meta"]=>
                  array(12) {
                  ["aperture"] => string(1) "0"
                  ["credit"] => string(0) ""
                  ["camera"] => string(0) ""
                  ["caption"] => string(0) ""
                  ["created_timestamp"] => string(1) "0"
                  ["copyright"] => string(0) ""
                  ["focal_length"] => string(1) "0"
                  ["iso"] => string(1) "0"
                  ["shutter_speed"] => string(1) "0"
                  ["title"] => string(0) ""
                  ["orientation"] => string(1) "0"
                  ["keywords"] => array(0) {
                  }
                  }
                 */
                echo '</div>';
            }


        //https://developer.wordpress.org/reference/functions/wp_get_attachment_metadata/
    }

    protected function render_item_uploadedto($settings) {
        if ($this->current_data->post_parent)
            echo '<' . $settings['html_tag'] . '>' . get_the_title($this->current_data->post_parent) . '</' . $settings['html_tag'] . '>';
    }

}
