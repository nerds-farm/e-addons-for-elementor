<?php

namespace EAddonsForElementor\Base;

use Elementor\Core\DynamicTags\Tag;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class Base_Tag extends Tag {

    public $is_data = false;

    use \EAddonsForElementor\Base\Traits\Base;

    public function get_group() {
        return 'e-addons';
    }

    public static function _group() {
        return self::_groups('e-addons');
    }

    public static function _groups($group = '') {
        $groups = array(
            'e-addons' => array('name' => 'e-addons', 'title' => 'e-addons'),
            'user' => array('name' => 'user', 'title' => __('User', 'e-addons-for-elementor')),
            'term' => array('name' => 'term', 'title' => __('Term', 'e-addons-for-elementor')),
            'post' => array('name' => 'post', 'title' => __('Post', 'e-addons-for-elementor')),
            'author' => array('name' => 'author', 'title' => __('Author', 'e-addons-for-elementor')),
            'site' => array('name' => 'site', 'title' => __('Site', 'e-addons-for-elementor')),
        );
        if ($group) {
            if (isset($groups[$group])) {
                return $groups[$group];
            }
            return reset($groups);
        }
        return $groups;
    }

    public function get_categories() {
        return Utils::get_dynamic_tags_categories();
    }
    
    /*public function get_panel_template_setting_key() {
            return 'key';
    }*/

    /**
     * @since 2.0.0
     * @access public
     *
     * @param array $options
     *
     * @return string
     */
    public function get_content(array $options = []) {
        $settings = $this->get_settings();
        
        if ($this->is_data) {
            $value = $this->get_value($options);
        } else {
            ob_start();
            $this->render();
            $value = ob_get_clean();
        }

        if (Utils::empty($value)) {
            if (!Utils::empty($settings, 'fallback')) {
                $value = $settings['fallback'];
                $value = Utils::get_dynamic_data($value);
            }
        }

        if (!Utils::empty($value) && !$this->is_data) {

            // TODO: fix spaces in `before`/`after` if WRAPPED_TAG ( conflicted with .elementor-tag { display: inline-flex; } );
            if (!Utils::empty($settings, 'before')) {
                $value = wp_kses_post($settings['before']) . $value;
            }

            if (!Utils::empty($settings, 'after')) {
                $value .= wp_kses_post($settings['after']);
            }

            if (static::WRAPPED_TAG) :
                $value = '<span id="elementor-tag-' . esc_attr($this->get_id()) . '" class="elementor-tag">' . $value . '</span>';
            endif;
        }

        return $value;
    }
    
    public function get_value(array $options = []) {
        return false;
    }

}
