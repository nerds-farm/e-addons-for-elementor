<?php

namespace EAddonsForElementor\Core;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * e-addons Utils.
 *
 * @since 1.0.1
 */
class Utils {

    use \EAddonsForElementor\Core\Traits\Plugin;
    use \EAddonsForElementor\Core\Traits\Wordpress;
    use \EAddonsForElementor\Core\Traits\Post;
    use \EAddonsForElementor\Core\Traits\User;
    use \EAddonsForElementor\Core\Traits\Term;
    use \EAddonsForElementor\Core\Traits\Comment;
    use \EAddonsForElementor\Core\Traits\Elementor;
    use \EAddonsForElementor\Core\Traits\Data;    
    use \EAddonsForElementor\Core\Traits\Path;
    use \EAddonsForElementor\Core\Traits\Pagination;
    
    public static function get_dynamic_data($value, $fields = array(), $var = '') {
        if (!empty($value)) {
            if (is_array($value)) {
                foreach ($value as $key => $setting) {
                    $value[$key] = self::get_dynamic_data($setting, $fields, $var);
                }
            } else if (is_string($value)) {
                $value = apply_filters('e_addons/dynamic', $value, $fields, $var); 
                $value = do_shortcode($value);
            }
        }
        return $value;
    }

    static public function get_plugin_path($file) {
        return Helper::get_plugin_path($file);
    }

    public static function camel_to_slug($title, $separator = '-') {
        return Helper::camel_to_slug($title, $separator);
    }

    public static function slug_to_camel($title, $separator = '') {
        return Helper::slug_to_camel($title, $separator);
    }

}
