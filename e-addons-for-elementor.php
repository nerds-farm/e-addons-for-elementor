<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       e-addons for Elementor
 * Plugin URI:        https://e-addons.com
 * Description:       The new must-have tool-set for web experts. Exploit Elementor limitless potential, with powerful widgets & extensions, and boost your workflow.
 * Version:           2.6.1
 * Author:            Nerds Farm
 * Author URI:        https://nerds.farm
 * Text Domain:       e-addons-for-elementor
 * Domain Path:       /languages
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Elementor tested up to: 3.3.0
 * Free:              true
 *
 * @package e-addons
 * @category Core
 *
 * e-addons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * e-addons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 */
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('E_ADDONS_URL', plugins_url(DIRECTORY_SEPARATOR, __FILE__));
define('E_ADDONS_PATH', plugin_dir_path(__FILE__));

/**
 * Load Elements
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 0.1.0
 */
function e_addons_load_plugin() {
    // Load localization file
    load_plugin_textdomain('e-addons-for-elementor');
    // Notice if the Elementor is not active
    if (did_action('elementor/loaded')) {
        // Require the main plugin file
        require_once( __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'plugin.php' );
        $plugin = \EAddonsForElementor\Plugin::instance();
        $plugin->init_versions();
        do_action('e_addons/loaded');
    } else {
        add_action('admin_notices', function() {
            $message = __('You need to activate "Elementor Free" in order to use "e-addons for Elementor" plugin.', 'elementor');
            echo '<div class="notice notice-error"><p>' . $message . '</p></div>';
        });
        return;
    }
}
add_action('plugins_loaded', 'e_addons_load_plugin');

function e_addons_activate() { set_transient( 'e_addons_activation_redirect', true, MINUTE_IN_SECONDS ); }
register_activation_hook( __FILE__, 'e_addons_activate' );

