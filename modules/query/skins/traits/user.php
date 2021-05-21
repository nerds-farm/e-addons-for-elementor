<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

use EAddonsForElementor\Core\Utils;
/**
 * Description of User
 *
 * @author fra
 */
trait User {

    // USERS
    protected function render_item_avatar($settings) {
        $user_info = $this->current_data;
        // Settings ------------------------------
        $use_bgimage = $settings['use_bgimage'];
        $use_overlay = $settings['use_overlay'];
        $use_overlayimg_hover = $this->get_instance_value('use_overlayimg_hover');
        
        $use_link = $this->get_item_link($settings);
        $blanklink  = $settings['blanklink_enable'];


        //
        // ---------------------------------------
        // @p preparo il dato in base a 'thumbnail_size'
        $avatarsize = $settings['avatar_size'];
        $avatar_url = '';
        $avatar_html = '';
        $image_attr = [
            'class' => $this->get_image_class()
        ];

        if (!empty($settings['image_custom_metafield'])) {
            $meta_value = get_metadata('user', $this->current_id, $settings['image_custom_metafield'], true);
            $image_id = $meta_value;
            
            $img_url = wp_get_attachment_image_src($image_id, $avatarsize, false);
            $avatar_url = $img_url[0];
            $avatar_html = wp_get_attachment_image($image_id, $avatarsize, false);
        }

        if(empty($avatar_url)){
            // @p questa è l'mmagine avatar URL
            $avatar_url = get_avatar_url($user_info->user_email, $avatarsize);
        }
        if(empty($avatar_html)){
            // @p questa è l'mmagine avatar HTML
            $avatar_html = get_avatar($user_info->user_email, $avatarsize);
        }

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

        $html_tag = 'div';
        $attribute_link = '';
        $attribute_target = '';

        if ($use_link) {
            $html_tag = 'a';
            $attribute_link = ' href="' . $use_link . '"';

            if( !empty($blanklink))
            $attribute_target = ' target="_blank"';
        }
        echo '<' . $html_tag . ' class="e-add-post-image' . $bgimage . $overlayimage . $overlayhover . '"' . $attribute_link . $attribute_target .'>';

        if ($use_bgimage) {
            echo '<figure class="e-add-img e-add-bgimage" style="background: url(' . $avatar_url . ') no-repeat center; background-size: cover; display: block;"></figure>';
        } else {
            echo '<figure class="e-add-img">' . $avatar_html . '</figure>';
        }

        echo '</' . $html_tag . '>';
    }

    protected function render_item_userdata($usertype, $settings) {
        $user_id = $this->current_id;
        $user_info = $this->current_data;
        $c = $this->counter;

        $use_link = $this->get_item_link($settings);
        $blanklink  = $settings['blanklink_enable'];

        $html_tag = !empty($settings['html_tag']) ? $settings['html_tag'] : 'div';
        
        $start_a = '';
        $end_a = '';
        if ($use_link) {
            $attribute_link = 'href="' . $use_link . '"';

            // in caso di email
            if ($usertype == 'email')
                $attribute_link = 'href="mailto:' . $user_info->user_email . '"';

            // in caso di website
            if ($usertype == 'website')
                $attribute_link = 'href="' . $user_info->user_url ;

            $attribute_target = '';
            if( !empty($blanklink))
            $attribute_target = ' target="_blank"';
            
            $start_a = '<a ' . $attribute_link . $attribute_target . '>';
            $end_a = '</a>';
        }
        
        echo sprintf('<%1$s class="e-add-queryuser-%2$s">', $html_tag, $usertype) . $start_a;

        switch ($usertype) {
            case 'displayname' :
                //echo 'sono Display Name';
                echo $this->render_label_before_item($settings,'Name: ');
                echo $user_info->display_name;                
                break;
            case 'user' :
                //echo 'sono l\'user';
                echo $this->render_label_before_item($settings,'User: ');
                echo $user_info->user_login;
                break;
            case 'role' :
                //echo 'sono il ruolo';
                echo $this->render_label_before_item($settings,'Role: ');
                echo Utils::to_string($user_info->roles); 
                break;
            case 'firstname' :
                //echo 'sono il first name';
                echo $this->render_label_before_item($settings,'First Name: ');
                echo $user_info->first_name;
                break;
            case 'lastname' :
                //echo 'sono il last name';
                echo $this->render_label_before_item($settings,'Last Name: ');
                echo $user_info->last_name;
                break;
            case 'nickname' :
                //echo 'sono il Nickname';
                echo $this->render_label_before_item($settings,'Nick Name: ');
                echo $user_info->user_nicename;
                break;
            case 'email' :
                //echo 'sono l\'email'; 
                echo $this->render_label_before_item($settings,'Email: ');
                echo $user_info->user_email;
                break;
            case 'website' :
                //echo 'sono il website';
                echo $this->render_label_before_item($settings,'Website: ');
                echo $user_info->user_url;
                break;
            case 'bio' :
                //echo 'sono la bio';
                echo $this->render_label_before_item($settings,'Description: ');
                $description_content = $user_info->description;
                $description_content = Utils::get_dynamic_data($description_content);
                echo $description_content;
                break;
            case 'registered' :
                //echo 'sono la user_registered';
                echo $this->render_label_before_item($settings,'Registered: ');
                echo $user_info->user_registered;
                break;
        }
        echo $this->render_label_after_item($settings);
        echo $end_a . sprintf('</%s>', $html_tag);
    }

}
