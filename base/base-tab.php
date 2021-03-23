<?php

namespace EAddonsForElementor\Base;

use Elementor\Element_Base;
use Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (class_exists('Elementor\Core\Kits\Documents\Tabs\Tab_Base')) {

    class Base_Tab extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base {

        use \EAddonsForElementor\Base\Traits\Base;

        public function __construct($parent = false) {
            $this->parent = $parent;
        }

        public function get_id() {
            return 'e-tab';
        }

        public function _register_tab() {
            $this->register_tab();
        }

        protected function register_tab_controls() {

        }

    }

} else {

    class Base_Tab extends Element_Base {

        /**
         * @var Kit
         */
        protected $parent;

        use \EAddonsForElementor\Base\Traits\Base;

        public function __construct($parent = false) {
            $this->parent = $parent;
        }

        public function get_id() {
            return 'e-tab';
        }

        public function register_controls() {
            $this->register_tab();
            $this->register_tab_controls();
        }

        public function _register_tab() {
            $this->register_tab();
        }

        public function register_tab() {
            Controls_Manager::add_tab($this->get_id(), $this->get_title());
        }

        protected function register_tab_controls() {

        }

    }

}