<?php

namespace EAddonsForElementor\Base;

use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (class_exists('\ElementorPro\Modules\Forms\Fields\Field_Base')) {

    class Base_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

        use \EAddonsForElementor\Base\Traits\Base;

        /**
         * Field base constructor.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {
            parent::__construct();
        }

        public function get_type() {
            return 'e-field';
        }

        public function render($item, $item_index, $form) {

        }

        public function _render_form($content, $widget) {
            return $content;
        }

        public function start_section($element, $tab = 'style') {
            $element->start_controls_section(
                    'e_' . $this->get_type() . '_section_' . $tab,
                    [
                        'label' => '<i class="eadd-logo-e-addons eadd-ic-right"></i>' . __($this->get_name(), 'e-addons-for-elementor'),
                        'tab' => $tab,
                    ]
            );
        }

    }

} else {

    class Base_Field {

        use \EAddonsForElementor\Base\Traits\Base;

        /**
         * Field base constructor.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {

        }

    }

}
