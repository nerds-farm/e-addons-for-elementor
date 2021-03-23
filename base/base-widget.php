<?php

namespace EAddonsForElementor\Base;

use EAddonsForElementor\Core\Utils;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class Base_Widget extends Widget_Base {

    use \EAddonsForElementor\Base\Traits\Base;

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_assets']);
    }

    /**
     * Enqueue admin styles in Editor
     *
     * @access public
     */
    public function enqueue_editor_assets() {

    }

    public function get_categories() {
        $plugin = $this->get_plugin_name();
        $tmp = explode('-', $plugin, 3);
        if (count($tmp) > 2) {
            if (end($tmp) == 'for-elementor') {
                return ['e-addons'];
            }
            return [end($tmp)];
        }
        return [$plugin];
    }

}
