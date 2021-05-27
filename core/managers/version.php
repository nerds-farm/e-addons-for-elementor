<?php

namespace EAddonsForElementor\Core\Managers;

use EAddonsForElementor\Core\Utils;

/**
 * Copyright to Worpress
 * Upgrader API: WP_Upgrader_Skin class
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 4.6.0
 
 * Generic Skin for the WordPress Upgrader classes. This skin is designed to be extended for specific purposes.
 * @since 2.8.0
 */
class E_Upgrader_Skin extends \WP_Upgrader_Skin {

    public function feedback($string, ...$args) {
        if (isset($this->upgrader->strings[$string])) {
            $string = $this->upgrader->strings[$string];
        }
        if (strpos($string, '%') !== false) {
            if ($args) {
                $args = array_map('strip_tags', $args);
                $args = array_map('esc_html', $args);
                $string = vsprintf($string, $args);
            }
        }
        if (empty($string)) {
            return;
        }
    }

}

/**
 * Version Manager
 *
 * @author fra
 * @since 1.0.1
 */
class Version {

    public function __construct() {

        $wp_content_dir = str_replace('/', DIRECTORY_SEPARATOR, WP_CONTENT_DIR);
        define('E_ADDONS_BACKUP_PATH', $wp_content_dir . DIRECTORY_SEPARATOR . 'e-backup');
        define('E_ADDONS_BACKUP_URL', WP_CONTENT_URL . '/e-backup');

        add_filter('upgrader_pre_download', array($this, '_upgrader_pre_download'), 10, 3);
        $this->addon_rollback();
    }

    // upgrader_pre_download callback
    public function _upgrader_pre_download($false, $package, $instance) {
        // get current plugin slug
        $plugin = false;
        if (property_exists($instance, 'skin')) {
            if ($instance->skin) {
                if (property_exists($instance->skin, 'plugin')) {
                    // from update page
                    if ($instance->skin->plugin) {
                        $pezzi = explode('/', $instance->skin->plugin);
                        $plugin = reset($pezzi);
                    }
                }
                if (!$plugin && isset($instance->skin->plugin_info["TextDomain"])) {
                    // ajax update
                    $plugin = $instance->skin->plugin_info["TextDomain"];
                }
            }
        }
        // only for e-addons
        if (substr($plugin, 0, 8) == 'e-addons' || isset($_POST['e_addons_version'])) {
            self::addon_backup($plugin);
            $download_file = download_url($package);
            if (is_wp_error($download_file)) {
                return new \WP_Error('download_failed', __('Error downloading the update package', 'e_addons'), $download_file->get_error_message());
            }
            return $download_file;
        }
        return $false;
    }

    static public function addon_backup($plugin) {
        // do a zip of current version
        $e_backup = !get_option('e_addons_backup_disable');
        if ($e_backup) {
            $wp_plugin_dir = str_replace('/', DIRECTORY_SEPARATOR, WP_PLUGIN_DIR);
            // create zip in backup folder
            if (!is_dir(E_ADDONS_BACKUP_PATH)) {
                mkdir(E_ADDONS_BACKUP_PATH, 0755, true);
            }
            if (!is_callable('get_plugin_data')) {
                include_once(ABSPATH . DIRECTORY_SEPARATOR . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php');
            }
            $plugin_data = get_plugin_data($wp_plugin_dir . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR . $plugin . '.php');
            if (!empty($plugin_data['Version'])) {
                $version = $plugin_data['Version'];

                $filename = E_ADDONS_BACKUP_PATH . DIRECTORY_SEPARATOR . $plugin . '.' . $version . '.zip';
                if (is_file($filename)) {
                    unlink($filename);
                }
                if (extension_loaded('zip')) {
                    Utils::compress_folder(array(
                        'source' => $wp_plugin_dir . DIRECTORY_SEPARATOR . $plugin,
                        'filename' => $filename,
                        'folder' => $plugin,
                    ));
                }
            }
            //die($options['zip_filename']);
        }
    }

    // rollback or reinstall an addon
    public function addon_rollback() {
        if (!empty($_POST['action']) && $_POST['action'] == 'e_addons_rollback') {
            if (!empty($_POST['e_addons_version']) && !empty($_POST['e_addons_plugin'])) {
                if (current_user_can('install_plugins')) {
                    $wp_plugin_dir = str_replace('/', DIRECTORY_SEPARATOR, WP_PLUGIN_DIR);
                    $plugin_name = sanitize_key($_POST['e_addons_plugin']);
                    $plugin_version = version_compare($_POST['e_addons_version'], '0.0.1', '>=') ? $_POST['e_addons_version'] : '0';
                    $backup = E_ADDONS_BACKUP_PATH . DIRECTORY_SEPARATOR . $plugin_name . '.' . $plugin_version . '.zip';
                    if (is_file($backup)) {
                        $roll_url = E_ADDONS_BACKUP_URL . '/' . $plugin_name . '.' . $plugin_version . '.zip';
                        $e_addons_path = $wp_plugin_dir . DIRECTORY_SEPARATOR . $plugin_name;
                        $rollback = $this->download_plugin($roll_url, $e_addons_path);
                        if (is_bool($rollback)) {
                            exit(wp_redirect(admin_url('?page=e_addons')));
                        } else {
                            die($rollback);
                        }
                    }
                }
            }
        }
    }

    public function download_plugin($roll_url, $e_addons_path) {
        ob_start();
        $wp_upgrader_skin = new E_Upgrader_Skin();
        $wp_upgrader = new \WP_Upgrader($wp_upgrader_skin);
        $wp_upgrader->init();
        if ($roll_url && !empty($e_addons_path)) {
            if ($e_addons_path == WP_PLUGIN_DIR || $e_addons_path.DIRECTORY_SEPARATOR == WP_PLUGIN_DIR) {
                return false;
            }
            $rollback = $wp_upgrader->run(
                    array(
                        'package' => $roll_url,
                        'destination' => $e_addons_path,
                        'clear_destination' => true
                    )
            );
            $roll_status = ob_get_clean();
            if ($rollback) {
                return true;
            } else {
                return $roll_status;
            }
        }
        $roll_status = ob_get_clean();
        return false;
    }

}
