<?php

namespace EAddonsForElementor\Core\Utils;

use EAddonsForElementor\Core\Utils;

/**
 * Description of Acf
 *
 */
class Jet {

    public static $jet_fields = [];
    public static $jet_tables = [ 'jet_post_types', 'jet_taxonomies' ];

    public static function get_jet_types() {
        $field_types = array(
            'text' => __('Text', 'jet-engine'),
            'date' => __('Date', 'jet-engine'),
            'time' => __('Time', 'jet-engine'),
            'datetime-local' => __('Datetime', 'jet-engine'),
            'textarea' => __('Textarea', 'jet-engine'),
            'wysiwyg' => __('WYSIWYG', 'jet-engine'),
            'switcher' => __('Switcher', 'jet-engine'),
            'checkbox' => __('Checkbox', 'jet-engine'),
            'iconpicker' => __('Iconpicker', 'jet-engine'),
            'media' => __('Media', 'jet-engine'),
            'gallery' => __('Gallery', 'jet-engine'),
            'radio' => __('Radio', 'jet-engine'),
            'repeater' => __('Repeater', 'jet-engine'),
            'select' => __('Select', 'jet-engine'),
            'number' => __('Number', 'jet-engine'),
            'colorpicker' => __('Colorpicker', 'jet-engine'),
            'posts' => __('Posts', 'jet-engine'),
            'html' => __('HTML', 'jet-engine'),
        );
        return array_keys($field_types);
    }
    
    public static function get_field_row_slug($meta_key) {
        // JET
        global $wpdb;
        foreach (self::$jet_tables as $table) {
            $sql = "SELECT * FROM " . $wpdb->prefix . $table . " WHERE meta_fields LIKE '%:\"" . $meta_key . "\";%'";
            $meta_fields_result = $wpdb->get_row($sql);
            if (!empty($meta_fields_result)) {
                $meta_fields_content = $meta_fields_result->meta_fields;
                $meta_fields = maybe_unserialize($meta_fields_content);
                if (!empty($meta_fields)) {
                    foreach ($meta_fields as $meta_field) {
                        if ($meta_field['name'] == $meta_key) {
                            return $meta_fields_result->slug;
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function get_meta_type($meta_key) {
        // JET
        global $wpdb;
        foreach (self::$jet_tables as $table) {
            $sql = "SELECT meta_fields FROM " . $wpdb->prefix . $table . " WHERE meta_fields LIKE '%:\"" . $meta_key . "\";%'";
            $meta_fields_result = $wpdb->get_col($sql);
            if (!empty($meta_fields_result)) {
                $meta_fields_content = reset($meta_fields_result);
                $meta_fields = maybe_unserialize($meta_fields_content);
                if (!empty($meta_fields)) {
                    foreach ($meta_fields as $meta_field) {
                        if ($meta_field['name'] == $meta_key) {
                            return $meta_field['type'];
                        }
                        if ($meta_field['type'] == 'repeater') {
                            if (!empty($meta_field['repeater-fields'])) {
                                foreach ($meta_field['repeater-fields'] as $repeater_field) {
                                    if ($repeater_field['name'] == $meta_key) {
                                        return $repeater_field['type'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return 'text';
    }

    public static function is_jet($key = '') {
        return (bool)self::get_jet_field($key);
    }
    
    public static function get_jet_field($key = '') {
        if ($key) {
            if (empty(self::$jet_fields)) {
                self::get_jet_fields();
            }
            if (!empty(self::$jet_fields[$key])) {
                return self::$jet_fields[$key];
            }
        }
        return false;
    }
    
    public static function add_jet_fields($meta_fields = array(), $jet_list = '', $slug = '', $types = array()) {   
        if (!empty($meta_fields)) {
            foreach ($meta_fields as $meta_field) {
                self::$jet_fields[$meta_field['name']] = $meta_field;
                if (empty($types) || in_array($meta_field['type'], $types)) {
                    $jet_list[$meta_field['name']] = $slug . ' > ' . $meta_field['title'] . ' [' . $meta_field['name'] . '] (' . $meta_field['type']. ')';
                }
                if ($meta_field['type'] == 'repeater') {
                    if (!empty($meta_field['repeater-fields'])) {
                        foreach ($meta_field['repeater-fields'] as $repeater_field) {
                            self::$jet_fields[$repeater_field['name']] = $repeater_field;
                            if (empty($types) || in_array($repeater_field['type'], $types) || in_array('sub_field', $types)) {
                                $jet_list[$repeater_field['name']] = $slug . ' > ' . $meta_field['title'] . ' > ' . $repeater_field['title'] . ' [' . $repeater_field['name'] . '] (' . $repeater_field['type']. ')';
                            }
                        }
                    }
                }
            }
        }
        return $jet_list;
    }

    public static function get_jet_fields($types = array(), $filter = '') {        
        $jet_list = [];

        if (is_string($types)) {
            $types = Utils::explode($types);
        }
        global $wpdb;
        
        foreach (self::$jet_tables as $table) {
            $sql = "SELECT slug, meta_fields FROM " . $wpdb->prefix . $table;
            $meta_fields_results = $wpdb->get_results($sql);
            if (!empty($meta_fields_results)) {
                foreach($meta_fields_results as $meta_fields_result) {
                    $meta_fields = maybe_unserialize($meta_fields_result->meta_fields);
                    //var_dump($meta_fields_result); die();
                    $jet_list = self::add_jet_fields($meta_fields, $jet_list, $meta_fields_result->slug, $types);
                }
            }
        }
        $jet_engine_meta_boxes = get_option('jet_engine_meta_boxes');
        if (!empty($jet_engine_meta_boxes)) {
            foreach($jet_engine_meta_boxes as $metabox) {
                if (!empty($metabox['meta_fields'])) {                    
                    $jet_list = self::add_jet_fields($metabox['meta_fields'], $jet_list, $metabox['args']['name'], $types);
                }
            }
        }
        
        
        //var_dump(self::$jet_fields); die();
        if ($filter) {
            foreach($jet_list as $key => $jet_field) {
                if (strpos($filter, $key) === false && strpos($filter, $jet_field) === false) {
                    unset($jet_list[$key]);
                }
            }
        }
        
        return $jet_list;
    }

    /*     * ********************************************************************** */
}
