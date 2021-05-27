<?php

namespace EAddonsForElementor\Core;

/**
 * Description of Helper
 *
 * @author fra
 */
class Helper {

    static public function get_plugin_path($file) {
        $wp_plugin_dir = str_replace('/', DIRECTORY_SEPARATOR, WP_PLUGIN_DIR);
        $wp_plugin_dir = str_replace('//', '/', $wp_plugin_dir);
        // from __FILE__
        $tmp = explode($wp_plugin_dir . DIRECTORY_SEPARATOR, $file, 2);
        if (count($tmp) == 2) {
            @list($plugin_name, $other) = explode(DIRECTORY_SEPARATOR, end($tmp));
            return $wp_plugin_dir . DIRECTORY_SEPARATOR . $plugin_name . DIRECTORY_SEPARATOR;
        }

        // from DOMAIN
        $tmp = explode('\\', $file);
        $tmp = array_filter($tmp);
        if (count($tmp) > 1) {
            $base = reset($tmp);
            $folder = self::camel_to_slug($base);
            return $wp_plugin_dir . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
        }

        return false;
    }

    public static function class_to_path($class) {
        $wp_plugin_dir = str_replace('/', DIRECTORY_SEPARATOR, WP_PLUGIN_DIR);
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $filename = self::camel_to_slug($filename);
        $filename = str_replace(DIRECTORY_SEPARATOR . '-', DIRECTORY_SEPARATOR, $filename);
        $filename = str_replace('_', '', $filename) . '.php';
        $filename = $wp_plugin_dir . DIRECTORY_SEPARATOR . $filename;
        $filename = str_replace('//', '/', $filename);
        return $filename;
    }

    public static function path_to_class($path) {
        $wp_plugin_dir = str_replace('/', DIRECTORY_SEPARATOR, WP_PLUGIN_DIR);
        $path = str_replace($wp_plugin_dir, '', $path);
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $tmp = explode(DIRECTORY_SEPARATOR, $path);
        $tmp = array_filter($tmp);
        $filename = array_pop($tmp);
        foreach ($tmp as $tkey => $atmp) {
            $tmp[$tkey] = self::slug_to_camel($atmp);
        }
        $filename = str_replace('.php', '', $filename);
        
        $is_elementor = false;
        if (reset($tmp) == 'Elementor') {
            $is_elementor = true;
            $tmp = array(reset($tmp));
        }
        
        $class = self::slug_to_camel(implode('\\', $tmp)) . '\\' . ($is_elementor ? 'Widget_' : '') . self::slug_to_camel($filename, '_');
        return $class;
    }

    public static function camel_to_slug($title, $separator = '-') {
        $label = preg_replace('/(?<=[a-z])[A-Z]|[A-Z](?=[a-z])/', ' $0', $title);
        $label = strtolower($label);
        $label = str_replace(DIRECTORY_SEPARATOR . ' ', DIRECTORY_SEPARATOR, $label);
        $label = trim($label);
        $label = str_replace('_ ', ' ', $label); // class name
        return str_replace(' ', $separator, $label);
    }

    public static function slug_to_camel($title, $separator = '') {
        $title = str_replace('-', ' ', $title);
        $title = ucwords($title);
        return str_replace(' ', $separator, $title);
    }

}
