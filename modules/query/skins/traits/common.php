<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use EAddonsForElementor\Core\Utils;

/**
 * Description of Common
 *
 * @author fra
 */
trait Common {

    protected function render_item_image($settings, $i = 0) {

        $querytype = $this->parent->get_querytype();
        $image_id = false;
        //considero se l'immagine è un metavalue invece della featured image
        
        if (!empty($settings['image_custom_metafield'])) {
            $meta_value = get_metadata($querytype, $this->current_id, $settings['image_custom_metafield'], true);
            $image_id = $meta_value;
        } else {

            switch ($querytype) {
                case 'attachment':
                    //se mi trovo in media basta l'id dell'attachment
                    $image_id = get_the_ID();
                    break;
                case 'post':
                    //se mi trovo in post
                    $image_id = get_post_thumbnail_id();
                    break;
                case 'term':
                    //se mi trovo in term (nnativamente non ho immagine )
                    $image_id = ''; //$meta_value;
                    break;
                case 'items':
                    //se mi trovo in item_list
                    $image_id = $this->current_data['sl_image']['id'];
                    break;
                case 'repeater':
                    //se mi trovo in repeater
                    if (!empty($this->current_data['item_image_' . $i])) {
                        $image_id = $this->current_data['item_image_' . $i];
                    }
                    break;
            }
        }

        // Settings ------------------------------
        $use_bgimage = $settings['use_bgimage'];
        $use_overlay = $settings['use_overlay'];
        $use_overlayimg_hover = $this->get_instance_value('use_overlayimg_hover');
        //
        $bgimage = '';
        if ($use_bgimage) {
            $bgimage = ' e-add-post-bgimage';
        }
        $overlayimage = '';
        if ($use_overlay) {
            $overlayimage = ' e-add-post-overlayimage';
        }
        $overlayhover = '';
        if ($use_overlayimg_hover) {
            $overlayhover = ' e-add-post-overlayhover';
        }
        //
        // @p definisco se l'immagine è linkata
        $use_link = $this->get_item_link($settings);
        $blanklink = !empty($settings['blanklink_enable']);

        // ---------------------------------------
        // @p preparo il dato in base a 'thumbnail_size'
        $setting_key = $settings['thumbnail_size_size'];

        $image_attr = [
            'class' => $this->get_image_class()
        ];

        if (!$image_id) {
            //var_dump($settings['use_fallback_img']);
            if (!empty($settings['use_fallback_img']['id'])) {
                $image_id = $settings['use_fallback_img']['id'];
            }
        }

        if ($image_id) {
            // @p questa è l'mmagine via HTML
            switch ($querytype) {
                case 'attachment':
                    $use_link = !empty($settings['gallery_link']) && $settings['gallery_link'] != 'none' ? $settings['gallery_link'] : '';
                    $page_permalink = false;
                    //@p se il lightbox è attivo $page_permalink va in false
                    $open_lightbox = $this->parent->get_settings_for_display('open_lightbox');
                    if ($open_lightbox == 'no' || $use_link == 'attachment') {
                        $page_permalink = true;
                    }
                    if ($use_link) {
                        //se mi trovo in media questo mi serve per generare il lightbox
                        add_filter('wp_get_attachment_link', [$this->parent, 'add_lightbox_data_to_image_link'], 10, 2);
                        $thumbnail_html = wp_get_attachment_link($image_id, $setting_key, $page_permalink, true, false, $image_attr);
                        remove_filter('wp_get_attachment_link', [$this->parent, 'add_lightbox_data_to_image_link']);
                    } else {
                        $thumbnail_html = wp_get_attachment_image($image_id, $setting_key, true, $image_attr);
                    }

                    break;
                default:
                    //se mi trovo in post
                    $thumbnail_html = wp_get_attachment_image($image_id, $setting_key, false, $image_attr);
                    break;
            }

            // @p [lo lascio come appunto storico.] sarò scemo io ma dopo 3 ore che provo questo in tutti i modi, non funziona, ipotizzo perché il size è un control nel repeater quindi nidificato.
            //$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings, $setting_key );
        } else {
            return;
        }


        $html_tag = 'div';

        $attribute_link = '';
        $attribute_target = '';
        if ($use_link && $querytype != 'attachment') {
            $html_tag = 'a';
            $attribute_link = ' href="' . $use_link . '"';

            if (!empty($blanklink))
                $attribute_target = ' target="_blank"';
        }
        echo '<' . $html_tag . ' class="e-add-post-image' . $bgimage . $overlayimage . $overlayhover . '"' . $attribute_link . $attribute_target . '>';

        if ($use_bgimage) {
            // @p questa è l'mmagine via URL
            //$image_url = Group_Control_Image_Size::get_attachment_image_src($image_id, 'thumbnail_size', $settings);
            $image_url = wp_get_attachment_image_src($image_id, $setting_key, true);
            echo '<figure class="e-add-img e-add-bgimage" style="background: url(' . $image_url[0] . ') no-repeat center; background-size: cover; display: block;"></figure>';
        } else {
            echo '<figure class="e-add-img">' . $thumbnail_html . '</figure>';
        }

        echo '</' . $html_tag . '>';
    }

    protected function render_item_title($settings, $i = 0) {
        // Settings ------------------------------
        $html_tag = !empty($settings['html_tag']) ? $settings['html_tag'] : 'h3';
        //
        $use_link = $this->get_item_link($settings);
        $blanklink = $settings['blanklink_enable'];

        // ---------------------------------------
        echo sprintf('<%1$s class="e-add-post-title">', $html_tag);
        ?>
        <?php
        if ($use_link) {
            $attribute_link = ' href="' . $use_link . '"';

            $attribute_target = '';
            if (!empty($blanklink))
                $attribute_target = ' target="_blank"';

            echo '<a' . $attribute_link . $attribute_target . '>';
        }
        $querytype = $this->parent->get_querytype();

        switch ($querytype) {
            case 'attachment':
            case 'post':
                //se mi trovo in post
                get_the_title() ? the_title() : the_ID();
                break;
            case 'term':
                //se mi trovo in term
                $term_info = $this->current_data;
                echo $term_info->name;
                break;
            case 'items':
                //se mi trovo in item_list
                echo $this->current_data['sl_title'];
                break;
            case 'repeater':
                //se mi trovo in item_list
                // ..... echo $this->current_data['sl_title'];
                echo $this->current_data['item_title_' . $i];
                //echo $settings['item_type'].' - '.$i;
                //echo $settings['item_type'].' - '.$this->counter.' - '.$this->itemindex;
                break;
        }
        ?>
        <?php if ($use_link) {
            echo '</a>';
        } ?>
        <?php
        echo sprintf('</%s>', $html_tag);
        ?>
        <?php
    }

    protected function render_item_date($settings, $i = 0) {
        $querytype = $this->parent->get_querytype();
        // Settings ------------------------------
        $date_format = $settings['date_format'];
        $icon_enable = $settings['icon_enable'];
        // ---------------------------------------
        if (empty($date_format)) {
            $date_format = get_option('date_format');
        }
        $icon = '';
        if (!empty($icon_enable)) {
            $icon = '<i class="e-add-icon e-add-query-icon fas fa-calendar" aria-hidden="true"></i> ';
        }
        $date = '';
        switch ($querytype) {
            case 'attachment':
                $date = get_the_date($date_format, '');
                break;
            case 'post':
                $date_type = $settings['date_type'];
                //se mi trovo in post
                switch ($date_type) {
                    case 'modified' :
                        $date = get_the_modified_date($date_format, '');

                        break;

                    case 'publish' :
                    default:
                        $date = get_the_date($date_format, '');

                        break;
                }
                break;
            case 'user':
                $date = $this->current_data->user_registered;
                $date = date($date_format, strtotime($date));
                break;
            case 'items':
                //se mi trovo in item_list
                //$date = $this->current_data['sl_date'];
                if (!empty($this->current_data['sl_date'])) {
                    $date = date_create($this->current_data['sl_date']);
                    $date = date_format($date, $date_format);
                }
                break;
            case 'repeater':
                //se mi trovo in repeater
                //$date = $this->current_data['sl_date'];

                if (!empty($this->current_data['item_date_' . $i])) {
                    $date = date_create($this->current_data['item_date_' . $i]);
                    $date = date_format($date, $date_format);
                }
                break;
        }

        if (!empty($date)) {
            $html_tag = !empty($settings['html_tag']) ? $settings['html_tag'] : 'div';
            //@p label before
            echo $this->render_label_before_item($settings, 'Date: ');
            echo '<' . $html_tag . ' class="e-add-post-date">' . $icon . $date . '</' . $html_tag . '>';
            echo $this->render_label_after_item($settings);
        }
        ?>
        <?php
    }

    //@p il read_more è praticamente su tutti
    protected function render_item_readmore($settings) {
        // Settings ------------------------------
        $readmore_text = $settings['readmore_text'];
        $readmore_size = $settings['readmore_size'];
        // ---------------------------------------
        $attribute_button = 'button_' . $this->counter;

        $use_link = $this->get_item_link($settings);
        $blanklink = $settings['blanklink_enable'];

        $this->parent->add_render_attribute($attribute_button, 'href', $use_link);

        if (!empty($blanklink))
            $this->parent->add_render_attribute($attribute_button, 'target', '_blank');
        //$this->parent->add_render_attribute($attribute_button, 'rel', 'nofollow');

        $this->parent->add_render_attribute($attribute_button, 'class', ['elementor-button-link', 'elementor-button', 'e-add-button']);
        $this->parent->add_render_attribute($attribute_button, 'role', 'button');

        if (!empty($readmore_size)) {
            $this->parent->add_render_attribute($attribute_button, 'class', 'elementor-size-' . $readmore_size);
        }

        if (!empty($this->current_permalink) && !is_wp_error($this->current_permalink)) {
            ?>
            <div class="e-add-post-button">
                <a <?php echo $this->parent->get_render_attribute_string($attribute_button); ?>>
            <?php echo $readmore_text; ?>
                </a>
            </div>
            <?php
        }
    }

    //@p il read_more è praticamente su tutti
    protected function render_item_template($settings) {
        $item_template_id = $settings['template_item_id'];
        if (!empty($item_template_id))
            $this->render_e_template($item_template_id);
    }

    // per gestire l'icona 
    protected function render_item_icon($metaitem, $icon5_key, $icon4_key, $class_icon = '') {

        $querytype = $this->parent->get_querytype();
        if ($querytype == 'items') {
            $ic = $this->current_data['sl_icon'];
            $metaitem[$icon5_key] = $ic;
        }

        $migrated = isset($metaitem['__fa4_migrated'][$icon5_key]);
        $is_new = empty($metaitem[$icon4_key]) && Icons_Manager::is_migration_allowed();
        //
        if (!empty($metaitem[$icon4_key]) || !empty($metaitem[$icon5_key]['value'])) {
            ob_start();
            if ($is_new || $migrated) {
                Icons_Manager::render_icon($metaitem[$icon5_key], ['aria-hidden' => 'true', 'class' => $class_icon]);
            } else {
                $class_icon = $class_icon ? $class_icon . ' ' : '';
                ?>
                <i class="e-add-icon <?php echo $class_icon . esc_attr($metaitem[$icon4_key]); ?>" aria-hidden="true"></i>
                <?php
            }
            return ob_get_clean();
        }
        return '';
    }

}
