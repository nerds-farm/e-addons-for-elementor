<?php

namespace EAddonsForElementor\Core\Utils;

use EAddonsForElementor\Core\Utils;

/**
 * Description of Acf
 *
 */
class Acf {

    public static $acf_fields = [];

    public static function get_acf_types() {
        if (!empty(acf()->fields)) {
            $types = acf()->fields->get_field_types();
            return array_keys($types);
        }
        return array("text", "textarea", "number", "range", "email", "url", "password", "image", "file", "wysiwyg", "oembed", "gallery", "select", "checkbox", "radio", "button_group", "true_false", "link", "post_object", "page_link", "relationship", "taxonomy", "user", "google_map", "date_picker", "date_time_picker", "time_picker", "color_picker", "message", "accordion", "tab", "group", "repeater", "flexible_content", "clone");
    }
    
    public static function get_post_meta_name($meta_key) {
        $acf = get_field_object($meta_key);
        if ($acf) {
            return $acf['label'];
        }        
        return $meta_key;
    }

    public static function get_meta_type($meta_key) {
        // ACF
        global $wpdb;
        $sql = "SELECT post_content FROM " . $wpdb->prefix . "posts WHERE post_excerpt = '" . $meta_key . "' AND post_type = 'acf-field';";
        $acf_result = $wpdb->get_col($sql);
        if (!empty($acf_result)) {
            $acf_content = reset($acf_result);
            $acf_field_object = maybe_unserialize($acf_content);
            if ($acf_field_object && is_array($acf_field_object) && isset($acf_field_object['type'])) {
                return $acf_field_object['type'];
            }
        }
        return 'text';
    }

    public static function is_acf($key = '') {
        if ($key) {
            return self::get_acf_field_id($key);
        }
        return false;
    }
    
    public static function get_acf_field($key = '') {
        if ($key) {
            global $wpdb;
            $sql = 'SELECT * FROM ' . $wpdb->prefix . "posts WHERE post_excerpt = '" . esc_sql($key) . "' AND post_type = 'acf-field'";
            $acf_fields = (array)$wpdb->get_row($sql);
            if (!empty($acf_fields)) {
                if (!empty($acf_fields['post_content'])) {
                    $acf_field = maybe_unserialize($acf_fields['post_content']);
                    if ($acf_field && is_array($acf_field) && isset($acf_field['type'])) {
                        foreach($acf_field as $acf_key => $acf_val) {
                            $acf_fields[$acf_key] = $acf_val;
                        }
                    }
                }
                $acf_fields['key'] = $acf_fields['post_name'];
                return $acf_fields;
            }
        }
        return false;
    }

    public static function get_acf_fields($types = array(), $filter = '') {

        $acf_list = [];

        if (is_string($types)) {
            $types = Utils::explode($types);
        }

        $acf_fields = get_posts(array('post_type' => 'acf-field', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'suppress_filters' => false));
        if (!empty($acf_fields)) {
            foreach ($acf_fields as $acf_field) {
                $acf_field_parent = false;
                if ($acf_field->post_parent) {   
                    $acf_field_parent = get_post($acf_field->post_parent);
                    $acf_field_parent_settings = acf_get_raw_field($acf_field->post_parent);
                }
                $acf_field_settings = maybe_unserialize($acf_field->post_content);
                if (!empty($acf_field_settings['type'])) {
                    $acf_field_type = $acf_field_settings['type'];                    
                    if (empty($types) || in_array($acf_field_type, $types)) {
                        if ($acf_field_parent) {
                            if (isset($acf_field_parent_settings['type']) && $acf_field_parent_settings['type'] == 'group') {
                                $acf_list[$acf_field_parent->post_excerpt . '_' . $acf_field->post_excerpt] = $acf_field_parent->post_title . ' > ' . $acf_field->post_title . ' [' . $acf_field->post_excerpt . '] (' . $acf_field_type . ')'; //.$acf_field->post_content; //post_name,
                            } else {
                                $acf_list[$acf_field->post_excerpt] = $acf_field_parent->post_title . ' > ' . $acf_field->post_title . ' [' . $acf_field->post_excerpt . '] (' . $acf_field_type . ')'; //.$acf_field->post_content; //post_name,
                            }
                        }                        
                    }
                    if ($acf_field_type == 'repeater' && in_array('sub_field', $types)) {
                        $acf_sub_fields = get_posts(array('post_parent' => $acf_field->ID, 'post_type' => 'acf-field', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'suppress_filters' => false));
                        if (!empty($acf_sub_fields)) {
                            foreach ($acf_sub_fields as $acf_sub_field) {
                                $acf_field_settings = maybe_unserialize($acf_sub_field->post_content);
                                if (!empty($acf_field_settings['type'])) {
                                    $acf_sub_field_type = $acf_field_settings['type'];
                                    $acf_list[$acf_sub_field->post_excerpt] = $acf_field->post_title . ' > ' . $acf_sub_field->post_title . ' [' . $acf_sub_field->post_excerpt . '] (' . $acf_sub_field_type . ')'; //.$acf_field->post_content; //post_name,
                                }
                            }
                        }
                    }
                }
            }
        }
        
        if ($filter) {
            foreach($acf_list as $key => $acf_field) {
                if (strpos($filter, $key) === false && strpos($filter, $acf_field) === false) {
                    unset($acf_list[$key]);
                }
            }
        }
        
        return $acf_list;
    }

    public static function get_acf_field_id($key, $multi = false) {
        if (isset(self::$acf_fields[$key]['ID'])) {
            return self::$acf_fields[$key]['ID'];
        }        
        $post = acf_get_field_post($key);
        if ($post) {
            self::$acf_fields[$key]['ID'] = $post->ID;
            return $post;
        }
        return false;
    }
    
    public static function get_target($id_target, $customfields_type = 'post') {
        switch ($customfields_type) {
            case 'attachment':
            case 'post':
                $id_target = (string)$id_target;
                break;
            case 'term':
                $id_target = 'term_' . $id_target;
                break;
            case 'user':
                $id_target = 'user_' . $id_target;
                break;
            case 'option':
                $id_target = 'option';
                break;
        }
        return $id_target;
    }
    
    public static function set_repeater($selector, $data, $obj_id, $type = 'post') {
        $id_target = self::get_target($obj_id, $type);            
        $repeater = get_field_object($selector, $id_target, false);
        if (!$repeater) $repeater = self::get_acf_field($selector);        
        if (!empty($repeater['type']) && $repeater['type'] == 'repeater') {
            // https://www.advancedcustomfields.com/resources/delete_row/
            $rows = get_metadata($type, $obj_id, $selector);
            $rows = intval($rows);
            for ($i=1; $i<=$rows; $i++) {
                delete_row($selector, $i, $id_target);
            }
            // https://www.advancedcustomfields.com/resources/update_sub_field/
            // Update "caption" within the first row of "repeater".                            
            if (!empty($data)) {
                foreach($data as $row => $sub_fields) {
                    $row++; // start from 1 (not 0)
                    foreach($sub_fields as $sub => $sub_field) {
                        update_sub_field( array($selector, $row, $sub), $sub_field, $id_target );
                    }
                }
                update_metadata($type, $obj_id, $selector, $row);
                update_metadata($type, $obj_id, '_'.$selector, $repeater['key']);
                return true;
            }
        }
        return false;
    }


    /* *********************************************************************** */
    
    

}
