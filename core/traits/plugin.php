<?php

namespace EAddonsForElementor\Core\Traits;

/**
 * @author francesco
 */
trait Plugin {

    public static $plugins_active = [];

    public static function is_plugin_active($plugin) {
        
        if ($plugin == 'elementor-pro') {
            return defined('ELEMENTOR_PRO__FILE__');
        }
        
        if (!empty(self::$plugins_active[$plugin]) && self::$plugins_active[$plugin]) {
            return true;
        }
        $active = self::is_plugin($plugin, 'must_use') || self::is_plugin($plugin, 'local') || self::is_plugin($plugin, 'network');
        $active = apply_filters('e_addons/plugin/active', $active, $plugin);
        self::$plugins_active[$plugin] = $active;
        return $active;
    }

    public static function is_plugin($plugin, $mode = 'local') {
        switch ($mode) {
            case 'must_use':
                $plugins = wp_get_mu_plugins();
                if (is_dir(WPMU_PLUGIN_DIR)) {
                    $dir_plugins = glob(WPMU_PLUGIN_DIR . '/*/*.php');
                    if (!empty($dir_plugins)) {
                        foreach ($dir_plugins as $aplugin) {
                            $plugins[] = $aplugin;
                        }
                    }
                }
                break; 
            case 'network':
                $plugins = get_site_option('active_sitewide_plugins');
                $plugins = ($plugins) ? array_keys($plugins) : false;
                break;            
            case 'local':
            default:
                $plugins = get_option('active_plugins', array());
                
        }
        return self::plugin_check($plugin, $plugins);
    }

    public static function plugin_check($plugin, $plugins = array()) {
        if (!empty($plugins)) {
            if (in_array($plugin, $plugins)) {
                return true;
            }
            foreach ($plugins as $aplugin) {
                $tmp = basename($aplugin);
                $tmp = pathinfo($tmp, PATHINFO_FILENAME);
                if ($plugin == $tmp) {
                    return true;
                }                
                $tmp = explode('/', $aplugin);
                $tmp = reset($tmp);
                if ($plugin == $tmp) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function get_addons($core = false) {
        $all_addons = array();

        // PLUGIN CACHE
        $e_addons_plugin = E_ADDONS_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'e-addons.json';

        $e_addons_json_content = file_get_contents($e_addons_plugin);
        $all_addons = json_decode($e_addons_json_content, true);
        if (!$core) {
            unset($all_addons['e-addons-for-elementor']);
        }
        $all_addons = apply_filters('e_addons/addons/remote', $all_addons, $e_addons_plugin, $core);
        return $all_addons;
    }

}
