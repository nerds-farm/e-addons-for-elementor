<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;
/**
 * Description of Common
 *
 * @author fra
 */
trait Custommeta {

    protected function get_value_custommeta($metakey,$i = 0) {

        if (!empty($metakey)) {
            $querytype = $this->parent->get_querytype();

            //@p solo in caso di repeater
            if($querytype == 'repeater'){
                $querytype = Query_Utils::is_type_of();
                
                //in caso di repeater uso il dato registrato in: current_data
                $meta_value = $this->current_data['item_custommeta_'.$i];
                
                return $meta_value;
            }

            // ---------------------------------------------

            if ($querytype == 'attachment') {
                $querytype = 'post';
            }
            
            //@p prendo il valore del meta (forzatamente singolo)
            $meta_value = get_metadata($querytype, $this->current_id, $metakey, true);

            return $meta_value;
        }
        return false;
    }

    // questo vale per tutti Posts - Users - Terms
    protected function render_item_custommeta($metaitem,$i = 0) {

        //la chiave del custom_meta selezionata
        $metafield_key = $metaitem['metafield_key'];

        $_id = $metaitem['_id'];

        if (!empty($metafield_key)) {


            //il tipo di rendering per il custom_meta
            $metafield_type = $metaitem['metafield_type'];
            // --------------------------------------

            $link_to = '';
            //
            $attribute_a_link = 'a_link_' . $this->counter . '_' . $_id;
            $attribute_custommeta_item = 'custommeta_item-' . $this->counter . '_' . $_id;
            //

            //@p il meta +++++++++++++++++++++++++++++++++++++++++++++
            $meta_value = $this->get_value_custommeta($metafield_key,$i);
            // +++++++++++++++++++++++++++++++++++++++++++++++++++++++

            if (empty($meta_value)) {
                if (!empty($settings['use_fallback'])) {
                    $meta_value = $settings['use_fallback'];
                }
            }

            if (empty($meta_value))
                return;

            $meta_html = '';
            
            switch ($metafield_type) {
                case 'date':
                    $metafield_date_format_source = $metaitem['metafield_date_format_source'];
                    $metafield_date_format_display = $metaitem['metafield_date_format_display'];

                    if ($metafield_date_format_source) {
                        if ($metafield_date_format_source == 'timestamp') {
                            $timestamp = $meta_value;
                        } else {
                            $d = \DateTime::createFromFormat($metafield_date_format_source, $meta_value);
                            if ($d) {
                                $timestamp = $d->getTimestamp();
                            } else {
                                $timestamp = strtotime($meta_value);
                            }
                        }
                    } else {
                        $timestamp = strtotime($meta_value);
                    }
                    $meta_html = date_i18n($metafield_date_format_display, $timestamp);
                    break;
                case 'image':
                    //$link_to = $metaitem['use_link'];
                    $link_to = $metaitem['link_to'];

                    $image_size_key = $metaitem['metafield_image_size_size'];
                    $image_attr = [
                        'class' => 'e-add-img',
                    ];
                    if (is_string($meta_value)) {
                        //echo 'string';
                        if (is_numeric($meta_value)) {
                            //echo 'N '.$image_size_key.' '.$meta_value;
                            $image_html = wp_get_attachment_image($meta_value, $image_size_key, false, $image_attr);
                            //$imageSrc = wp_get_attachment_image_src( $meta_value , 'full' );
                            //$imageSrcUrl = $imageSrc;
                        } else {
                            $image_html = '<img src="' . $meta_value . '" />';
                        }
                    } else if (is_numeric($meta_value)) {
                        //echo 'num';
                        $image_html = wp_get_attachment_image($meta_value, $image_size_key, false, $image_attr);
                    } else if (is_array($meta_value)) {
                        //echo 'array';
                        // TO DO ... da valutare come gestire il caso di un'array...

                        $imageSrc = wp_get_attachment_image_src($meta_value ['ID'], $thumbnail_size);
                        $imageSrcUrl = $imageSrc[0];
                    }
                    $meta_html = $image_html;
                    break;

                case 'button':
                    $metafield_button_label = $metaitem['metafield_button_label'];
                    $metafield_button_size = $metaitem['metafield_button_size'];
                    $metafield_button_target = $metaitem['metafield_button_target'];
                    $metafield_button_nofollow = $metaitem['metafield_button_nofollow'];
                    
                    $this->parent->add_render_attribute($attribute_a_link, 'href', $meta_value);
                    $this->parent->add_render_attribute($attribute_a_link, 'role', 'button');

                    if(!empty($metafield_button_target))
                    $this->parent->add_render_attribute($attribute_a_link, 'target', '_blank');

                    if(!empty($metafield_button_nofollow))
                    $this->parent->add_render_attribute($attribute_a_link, 'rel', 'nofollow');

                    if (!empty($metafield_button_size)) {
                        $this->parent->add_render_attribute($attribute_a_link, 'class', 'elementor-size-' . $metafield_button_size);
                    }
                    $this->parent->add_render_attribute($attribute_a_link, 'class', ['elementor-button-link', 'elementor-button', 'e-add-button']);
                    
                    //l'icona
                    $show_icon = '';
                    if (!empty($metaitem['show_icon']["value"])) {
                        $show_icon = $this->render_item_icon($metaitem, 'show_icon', 'icon', 'e-add-query-icon');
                        $this->parent->add_render_attribute($attribute_a_link, 'class', 'e-add-is_icon');
                    }
                    $meta_html = $show_icon . '<span>' . $metafield_button_label . '</span>';

                    break;
                case 'oembed':
                    //var_dump($meta_value);                    
                    if (strpos($meta_value, 'https://') !== false){
                        $meta_html = '<div class="e-add-oembed">'.wp_oembed_get($meta_value).'</div>';
                    }
                    //<iframe width="560" height="315" src="https://www.youtube.com/embed/wb-mj2ug1iI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    //$meta_html = '<div class="e-add-oembed">' . $meta_value . '</div>';
                    //vimeo
                    //$meta_html .= '<div class="e-add-videocontainer"><iframe src="https://player.vimeo.com/video/477245251" width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe></div>';

                    break;
                case 'map':
                    //var_dump($meta_value);
                    
                    $address = '';
                    if (is_array($meta_value)) {
                        if( !empty($meta_value['address']) )
                        $address = $meta_value['address'];
                    } else if (is_string($meta_value)) {
                        $address = $meta_value;
                    }

                    if ($address)
                        $meta_html = '<div class="e-add-oembed"><iframe width="600" height="500" src="https://maps.google.com/maps?q=' . urlencode($address) . '&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe></div>';
                    //<div class="mapouter"><div class="gmap_canvas"><iframe width="600" height="500" id="gmap_canvas" src="https://maps.google.com/maps?q=university%20of%20san%20francisco&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://www.whatismyip-address.com/nordvpn-coupon/"></a></div><style>.mapouter{position:relative;text-align:right;height:500px;width:600px;}.gmap_canvas {overflow:hidden;background:none!important;height:500px;width:600px;}</style></div>

                    break;
                case 'file':
                    //var_dump($meta_value);

                    $id_file = '';
                    $metafield_label = $metaitem['metafield_file_label'];

                    if (is_string($meta_value)) {
                        if (is_numeric($meta_value)) {
                            //echo 'numeric';
                            $id_file = $meta_value;
                            //$meta_value = $meta_value; //è una stringa id
                        } else {
                            //echo 'string';
                            $url_file = $meta_value;
                            //$meta_value = $meta_value; //è una stringa url
                        }
                    } else if (is_numeric($meta_value)) {
                        //echo 'num';
                        $id_file = $meta_value;
                        //$meta_value = $meta_value; //è una stringa id
                    } else if (is_array($meta_value)) {
                        //echo 'array';
                        if (!empty($meta_value['ID'])) {
                            $id_file = $meta_value['ID'];
                        }
                        if (!empty($meta_value['url'])) {
                            $url_file = $meta_value['url'];
                        }
                    }

                    if ($id_file) {
                        $url_file = wp_get_attachment_url($id_file);
                    }

                    if ($url_file) {

                        $this->parent->add_render_attribute($attribute_a_link, 'href', $url_file);
                        $this->parent->add_render_attribute($attribute_a_link, 'role', 'button');
                        $this->parent->add_render_attribute($attribute_a_link, 'class', 'e-add-file-meta');

                        //l'icona
                        $show_icon = '';
                        if (!empty($metaitem['show_icon']["value"])) {
                            $show_icon = $this->render_item_icon($metaitem, 'show_icon', 'icon', 'e-add-query-icon');
                            $this->parent->add_render_attribute($attribute_a_link, 'class', 'e-add-is_icon');
                        }

                        //$meta_html = '<a class="e-add-file-meta'.$class_icon.'" href="'.$url_file.'">';
                        $meta_html .= $show_icon . '<span>' . $metafield_label . '</span>';
                        //$meta_html .= '</a>';
                    }

                    break;
                case 'post':
                    //var_dump($meta_value);

                    $meta_html = '<ul class="e-add-list-meta e-add-list-meta-post' . '">';

                    $metavalues = array();
                    if (!is_array($meta_value)) {
                        $metavalues[0] = $meta_value;
                    } else {
                        $metavalues = $meta_value;
                    }
                    foreach ($metavalues as $k => $p) {
                        $meta_post = get_post($p);

                        if (!is_wp_error($meta_post) && !empty($meta_post)) {
                            //var_dump($meta_post);

                            $image_post = '';
                            if ($metaitem['metafield_post_image'])
                                $image_post = '<span class="e-add-post-meta-image">' . get_the_post_thumbnail($meta_post->ID, [40, 40]) . '</span>';

                            $link_a_start = '';
                            $link_a_end = '';
                            if ($metaitem['metafield_list_link']) {
                                $link_a_start = '<a href="' . get_permalink($meta_post->ID) . '">';
                                $link_a_end = '</a>';
                            }
                            $meta_html .= '<li>' . $image_post . $link_a_start . '<span class="e-add-post-meta-name">' . $meta_post->post_title . '</span>' . $link_a_end . '</li>';
                        }
                    }
                    $meta_html .= '</ul>';

                    break;
                case 'user':
                    //var_dump($meta_value);

                    $meta_html = '<ul class="e-add-list-meta e-add-list-meta-user e-add-list-meta-' . $metaitem['metafield_list_direction'] . '">';

                    $metavalues = array();
                    if (!is_array($meta_value)) {
                        $metavalues[0] = $meta_value;
                    } else {
                        $metavalues = $meta_value;
                    }
                    foreach ($metavalues as $k => $u) {
                        $meta_user = get_userdata($u);

                        if (!is_wp_error($meta_user) && !empty($meta_user)) {
                            //var_dump($meta_user);

                            $separator = '';
                            if ($k && $metaitem['metafield_list_direction'] == 'horizontal')
                                $separator = $metaitem['metafield_list_separator'];

                            $link_a_start = '';
                            $link_a_end = '';
                            if ($metaitem['metafield_list_link']) {
                                $link_a_start = '<a href="' . get_author_posts_url($meta_user->ID) . '">';
                                $link_a_end = '</a>';
                            }
                            $meta_html .= '<li>' . $separator . $link_a_start . '<span class="e-add-user-meta-name">' . $meta_user->display_name . '</span>' . $link_a_end . '</li>';
                        }
                    }
                    $meta_html .= '</ul>';


                    break;
                case 'term':
                    //var_dump($meta_value);

                    $meta_html = '<ul class="e-add-list-meta e-add-list-meta-term e-add-list-meta-' . $metaitem['metafield_list_direction'] . '">';
                    $metavalues = array();
                    if (!is_array($meta_value)) {
                        $metavalues[0] = $meta_value;
                    } else {
                        $metavalues = $meta_value;
                    }
                    foreach ($metavalues as $k => $t) {
                        $meta_term = get_term($t);

                        if (!is_wp_error($meta_term) && !empty($meta_term)) {

                            $separator = '';
                            if ($k && $metaitem['metafield_list_direction'] == 'horizontal')
                                $separator = $metaitem['metafield_list_separator'];

                            $term_count = '';
                            if ($metaitem['metafield_term_count'])
                                $term_count = '<span class="e-add-term-count">' . $meta_term->count . '</span>';

                            $link_a_start = '';
                            $link_a_end = '';
                            if ($metaitem['metafield_list_link']) {
                                $link_a_start = '<a href="' . get_term_link($meta_term->term_id) . '">';
                                $link_a_end = '</a>';
                            }
                            if (!$metaitem['metafield_term_hideempty'] || $meta_term->count)
                                $meta_html .= '<li>' . $separator . $link_a_start . '<span class="e-add-term-meta-name">' . $meta_term->name . '</span>' . $term_count . $link_a_end . '</li>';
                        }
                    }
                    $meta_html .= '</ul>';

                    break;
                case 'gallery':
                    //var_dump($meta_value[0]['ID']);
                    $gallery_type = 'grid'; //$metaitem['metafield_gallery_type']
                    $meta_html = '<ul class="e-add-list-meta e-add-list-meta-gallery e-add-list-meta-' . $gallery_type . '">';
                    $metavalues = array();
                    if (!is_array($meta_value)) {
                        $metavalues[0] = $meta_value;
                    } else {
                        $metavalues = $meta_value;
                    }
                    foreach ($metavalues as $k => $im) {
                        $meta_gallery = wp_get_attachment_image($im);

                        if (!is_wp_error($meta_gallery) && !empty($meta_gallery)) {

                            $link_a_start = '';
                            $link_a_end = '';

                            $meta_html .= '<li>' . $link_a_start . '<span class="e-add-img-meta">' . $meta_gallery . '</span>' . $link_a_end . '</li>';
                        }
                    }
                    $meta_html .= '</ul>';


                    break;
                case 'textarea':
                    $meta_html = nl2br($meta_value);
                    break;

                case 'textfield':
                case 'wysiwyg':
                    $meta_html = wpautop($meta_value);
                    break;
                
                case 'array':
                    $array_dump = $metaitem['array_dump'];
                    $array_indexes = $metaitem['array_index'];
                    if(!empty($array_dump)){
                        echo '<pre>';
                        var_dump($meta_value);
                        echo '</pre>';
                    }
                    $label_before = $this->render_label_before_item($metaitem);
                    $label_after = $this->render_label_after_item($metaitem);
                    
                    $tmp = explode('.', $array_indexes);
                    $sub_data = Utils::get_array_value($meta_value, $tmp);
                    
                    $meta_html = $label_before.$sub_data.$label_after;
                    break;
                    
                case 'number':                    
                    if ($metaitem['number_round']) {
                        $mode = $metaitem['number_round_mode'] ? PHP_ROUND_HALF_DOWN : PHP_ROUND_HALF_UP;
                        $meta_value = round($meta_value, intval($metaitem['number_round_precision']), $mode);
                    }
                    if ($metaitem['number_format']) {
                        $meta_value = number_format($meta_value, intval($metaitem['number_format_decimals']), $metaitem['number_format_decimal_separator'], $metaitem['number_format_thousands_separator']);
                    }                    
                case 'text':
                default:                
                    //$link_to = $metaitem['use_link'];
                    if (!empty($metaitem['link_to'])) {
                        $link_to = $metaitem['link_to'];
                    }

                    $html_tag_item = $metaitem['html_tag_item'];
                    if (!$html_tag_item) {
                        $html_tag_item = 'span';
                    }
                    
                    //l'icona
                    $show_icon = $show_icon_class = '';
                    if (!empty($metaitem['show_icon']["value"])) {
                        $show_icon = $this->render_item_icon($metaitem, 'show_icon', 'icon', 'e-add-query-icon');
                        $show_icon_class = ' class="e-add-is_icon"';
                    }
                    
                    $meta_html = '<' . $html_tag_item . $show_icon_class . '>' 
                            . $show_icon
                            . $this->render_label_before_item($metaitem) 
                            . Utils::to_string($meta_value)
                            . $this->render_label_after_item($metaitem) 
                            . '</' . $html_tag_item . '>';

                    break;
                    //$meta_html = Utils::to_string($meta_value);
            }

            switch ($link_to) {
                case 'post':
                    if (!is_wp_error($this->current_permalink)) {
                        $href_link = $this->current_permalink;
                    }
                    break;
                case 'custom':
                    $href_link = $metaitem['link']['url'];
                    break;
                default:
            }
            if ($link_to) {
                $this->parent->add_render_attribute($attribute_a_link, 'href', $href_link);
            }


            //@p il link del metafield
            $linkOpen = '';
            $linkClose = '';
            if ($link_to || $metafield_type == 'button' || $metafield_type == 'file') {
                $this->parent->add_render_attribute($attribute_a_link, 'class', ['e-add-link']);
                //
                $linkOpen = '<a ' . $this->parent->get_render_attribute_string($attribute_a_link) . '>';
                $linkClose = '</a>';
            }
            if (!empty($meta_html)) {
                //
                echo $linkOpen . $meta_html . $linkClose;
            }
        }
    }

}
