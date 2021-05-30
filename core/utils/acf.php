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
            $sql = 'SELECT post_content FROM ' . $wpdb->prefix . "posts WHERE post_excerpt = '" . esc_sql($key) . "' AND post_type = 'acf-field'";
            $acf_field = $wpdb->get_col($sql);
            if (!empty($acf_field)) {
                $acf_field = reset($acf_field);
                $acf_field = maybe_unserialize($acf_field);
                if ($acf_field && is_array($acf_field) && isset($acf_field['type'])) {
                    return $acf_field;
                }
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
            foreach($acf_list as $key => $acf_list) {
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


    /* *********************************************************************** */
    
    

}
