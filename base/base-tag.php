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
            'form' => array('name' => 'form', 'title' => __('Form', 'e-addons-for-elementor')),
            'archive' => array('name' => 'archive', 'title' => __('Archive', 'elementor')),
            'repeater' => array('name' => 'repeater', 'title' => __('Repeater', 'e-addons-for-elementor')),
            'woocommerce' => array('name' => 'woocommerce', 'title' => __('WooCommerce', 'e-addons-for-elementor')),
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

    /* public function register_controls() {
      //parent::register_controls();
      if (version_compare(ELEMENTOR_VERSION, '3.1.0') < 0) {
      $this->_register_controls();
      }
      } */

    public function _register_controls() {
        //parent::register_controls();
        //$tmp = explode('-', ELEMENTOR_VERSION); $version = reset($tmp);
        //if (version_compare($version, '3.1.0') < 0 || version_compare($version, '3.2.0') >= 0) {
        //var_dump($version); die();
        $this->register_controls();
        //}
    }

    /**
     * Register controls.
     *
     * Used to add new controls to any element type. For example, external
     * developers use this method to register controls in a widget.
     *
     * Should be inherited and register new controls using `add_control()`,
     * `add_responsive_control()` and `add_group_control()`, inside control
     * wrappers like `start_controls_section()`, `start_controls_tabs()` and
     * `start_controls_tab()`.
     *
     * @since 1.4.0
     * @access protected
     */
    protected function register_controls() {

    }

    /* public function get_panel_template_setting_key() {
      return 'key';
      } */

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
