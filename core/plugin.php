<?php

namespace EAddonsForElementor;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Managers\Modules;
use EAddonsForElementor\Core\Managers\Assets;
//use EAddonsForElementor\Core\Managers\Version;
use EAddonsForElementor\Core\Managers\Template;
use EAddonsForElementor\Core\Managers\Controls;
use EAddonsForElementor\Core\Managers\License;

/**
 * Main Plugin Class
 *
 * Register new elementor Extension
 *
 * @since 1.0.1
 */
class Plugin {

    /**
     * Instance.
     *
     * Holds the plugin instance.
     *
     * @since 1.0.1
     * @access public
     * @static
     *
     * @var Plugin
     */
    public static $instance = null;

    /**
     * Modules manager.
     *
     * Holds the modules manager.
     *
     * @since 1.0.0
     * @access public
     *
     * @var Modules_Manager
     */
    public $modules_manager;
    public $assets_manager;
    public $version_manager;
    public $template_manager;
    public $licenses_manager;
    public $ajax_manager;
    public $addons_manager = [];

    /**
     * Constructor
     *
     * @since 1.0.1
     *
     * @access public
     */
    public function __construct() {

        require_once(E_ADDONS_PATH . 'core' . DIRECTORY_SEPARATOR . 'helper.php');
        //require_once(E_ADDONS_PATH . 'core'.DIRECTORY_SEPARATOR.'dashboard'.DIRECTORY_SEPARATOR.'dashboard.php');


        spl_autoload_register([$this, 'autoload']);

        $this->setup_hooks();

        if (empty(self::$instance)) {
            // core plugin
            $this->licenses_manager = new License();
        } else {
            // extra plugin
            $this->maybe_vendor_autoload();
            self::instance()->add_addon($this);
            self::instance()->licenses_manager->init_license($this);
            do_action('e_addons/init_license', $this);
        }
    }

    /**
     * Instance.
     *
     * Ensures only one instance of the plugin class is loaded or can be loaded.
     *
     * @since 1.0.0
     * @access public
     * @static
     *
     * @return Plugin An instance of the class.
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();

            /**
             * e-addons loaded.
             *
             * Fires when e-addons was fully loaded and instantiated.
             *
             * @since 1.0.1
             */
            do_action('e_addons/instance', self::$instance);
        }

        return self::$instance;
    }

    public function autoload($class) {
        if (substr($class, 0, 7) != 'EAddons') {
            return;
        }

        if (!class_exists($class)) {

            $filename = \EAddonsForElementor\Core\Helper::class_to_path($class);
            if (is_readable($filename)) {
                include_once( $filename );
            } else {
                // fallback
                $plugin_path = \EAddonsForElementor\Core\Helper::get_plugin_path($class);
                $tmp = explode(DIRECTORY_SEPARATOR, $plugin_path);
                $tmp = array_filter($tmp);
                $plugin_name = end($tmp);

                $filename = str_replace(DIRECTORY_SEPARATOR . $plugin_name . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR, $filename);
                $plugin_search = glob($filename);
                $filename = reset($plugin_search);
                if (is_readable($filename)) {
                    include_once( $filename );
                } else {
                    //var_dump($class); var_dump($plugin_path); var_dump($filename); die();
                }
            }
        }
    }

    public function setup_hooks() {
        // fire actions
        add_action('elementor/init', [$this, 'on_elementor_init'], 9);

        add_filter("extra_plugin_headers", function ($extra_headers) {
            $extra_headers['Free'] = 'Free';
            $extra_headers['Channel'] = 'Channel';
            return $extra_headers;
        });
    }

    /**
     * Add Actions
     *
     * @since 0.0.1
     *
     * @access private
     */
    public function on_elementor_init() {

        $this->assets_manager = new Assets();
        $this->controls_manager = new Controls();
        $this->modules_manager = new Modules();
        $this->template_manager = new Template();

        $this->ajax_manager = new \EAddonsForElementor\Core\Ajax\Actions();

        if (is_admin()) {
            //$ajax = new \EAddonsForElementor\Core\Ajax\Actions();
            add_action('admin_notices', '\EAddonsForElementor\Core\Utils::e_admin_notices');
            $dash = new \EAddonsForElementor\Core\Dashboard\Dashboard();
            
        }

        // Register Category fix
        global $wp_filter;
        $tag = 'elementor/init';
        $tag_cat = 'elementor/elements/categories_registered';
        if (!empty($wp_filter[$tag]->callbacks[10])) {
            // remove_action( 'elementor/init', array( '\XXX', 'register_category' ) );
            foreach ($wp_filter[$tag]->callbacks[10] as $ckey => $callb) {
                if (strpos($ckey, 'register_category') !== false) {
                    $wp_filter[$tag_cat]->callbacks[10][$ckey] = $callb;
                    unset($wp_filter[$tag]->callbacks[10][$ckey]);
                }
            }
            //echo '<pre>';var_dump($wp_filter[ $tag ]->callbacks[10]);echo '</pre>'; die();
        }

        do_action('e_addons/init');
    }

    public function get_plugins() {
        $plugins = array();
        $wp_plugin_dir = str_replace('/', DIRECTORY_SEPARATOR, WP_PLUGIN_DIR);
        $wp_plugin_dir = str_replace('//', '/', $wp_plugin_dir);
        $e_addons_plugin = glob($wp_plugin_dir . DIRECTORY_SEPARATOR . 'e-addons*');
        foreach ($e_addons_plugin as $e_plugin) {
            if (is_dir($e_plugin)) {
                $e_plugin_name = basename($e_plugin);
                $e_plugin_file = $e_plugin . DIRECTORY_SEPARATOR . $e_plugin_name . '.php';
                if (file_exists($e_plugin_file)) {
                    $plugin = $e_plugin_name . '/' . $e_plugin_name . '.php';
                    if (!is_callable('get_plugin_data') || !is_callable('is_plugin_active')) {
                        include_once(ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php');
                    }
                    $plugins[$e_plugin_name] = get_plugin_data($e_plugin_file);
                    $plugins[$e_plugin_name]['active'] = is_plugin_active($plugin);
                    $plugins[$e_plugin_name]['plugin'] = $plugin;
                    $plugins[$e_plugin_name]['file'] = $e_plugin_file;
                    $plugins[$e_plugin_name]['path'] = $e_plugin;
                }
            }
        }
        // Core in first position
        if (!empty($plugins['e-addons-for-elementor'])) {
            $plugins = ['e-addons-for-elementor' => $plugins['e-addons-for-elementor']] + $plugins;
        }
        return $plugins;
    }

    public function get_name() {
        list($class, $none) = explode("\\", get_class($this), 2);
        $slug = \EAddonsForElementor\Core\Helper::camel_to_slug($class);
        return $slug;
    }

    public function add_addon($plugin) {
        if (empty($this->addons_manager)) {
            $addons = $this->get_addons(true); // init addons_manager
        }
        $this->addons_manager[$this->get_name()]['instance'] = $plugin;
    }

    public function get_addon($TextDomain = '') {
        $addons = $this->get_addons(true); // init addons_manager
        if (!$TextDomain) {
            $TextDomain = $this->get_name();
        }
        if (!empty($this->addons_manager[$TextDomain])) {
            return $this->addons_manager[$TextDomain];
        } else {
            $addons_more = Utils::get_addons();
            if (!empty($addons_more[$TextDomain])) {
                return $addons_more[$TextDomain];
            }
        }
        return false;
    }

    public function is_addon_valid($addon = null) {
        if (empty($addon)) {
            $addon = $this->get_addon();
        }
        if ($addon['Free']) {
            return true;
        }
        if (!$addon['active']) {
            return true;
        }
        return $addon['license_status'] == 'valid';
    }

    public function is_free($TextDomain = '') {
        $addon = $this->get_addon($TextDomain);
        if ($addon) {
            if (isset($addon['Free'])) {
                return $addon['Free'];
            }
            return !$this->compare_price($addon);
        }
        return false;
    }

    public function compare_price($addon, $price = 0, $compare = '>') {
        $addon_price = 0;
        if (is_array($addon)) {
            if (!empty($addon['price'])) {
                $addon_price = floatval($addon['price']);
            }
        } else {
            $addon_price = $addon;
        }
        switch ($compare) {
            case '>':
                return $addon_price > $price;
            case '>=':
                return $addon_price >= $price;
            case '<':
                return $addon_price < $price;
            case '<=':
                return $addon_price <= $price;
            case '=':
            case '==':
                return $addon_price == $price;
            default:
                return false;
        }
    }

    public function clear_addons() {
        $this->addons_manager = [];
    }

    public function get_addons($core = false) {
        if (empty($this->addons_manager)) {
            $plugins = $this->get_plugins();
            $addons = Utils::get_addons();
            $update_cache = get_site_transient('update_plugins');
            $update_cache = (array) $update_cache;
            //var_dump($update_cache); die();
            foreach ($plugins as $e_plugin_name => $e_plugin) {

                // local license info
                $license = get_option('e_addons_' . $e_plugin['TextDomain'] . '_license_key');
                $plugins[$e_plugin_name]['license'] = $license;
                $license_status = get_option('e_addons_' . $e_plugin['TextDomain'] . '_license_status');
                $plugins[$e_plugin_name]['license_status'] = $license_status;
                $license_expires = get_option('e_addons_' . $e_plugin['TextDomain'] . '_license_expires');
                $plugins[$e_plugin_name]['license_expires'] = $license_expires;
                if (!empty($this->addons_manager[$e_plugin_name]['instance'])) {
                    $plugins = $this->addons_manager[$e_plugin_name]['instance'];
                }

                // new version available
                $plugin_version = !empty($update_cache["response"][$e_plugin['plugin']]) ? (array) $update_cache["response"][$e_plugin['plugin']] : false;
                $plugins[$e_plugin_name]['new_version'] = false;
                if (empty($plugin_version)) {
                    if (!empty($addons[$e_plugin_name]['version'])) {
                        if (version_compare($addons[$e_plugin_name]['version'], $e_plugin['Version'], '>')) {
                            $plugins[$e_plugin_name]['new_version'] = $addons[$e_plugin_name]['version'];
                        }
                    }
                } else {
                    $plugins[$e_plugin_name]['new_version'] = $plugin_version['new_version'];
                    $plugins[$e_plugin_name]['package'] = $plugin_version['package'];
                }

                // remote info
                if (!empty($addons[$e_plugin_name])) {
                    foreach ($addons[$e_plugin_name] as $rkey => $info) {
                        $plugins[$e_plugin_name][$rkey] = $info;
                    }
                }

                $plugins[$e_plugin_name]['Channel'] = empty($e_plugin['Channel']) ? 'e-addons' : $e_plugin['Channel'];
                $plugins[$e_plugin_name]['Free'] = (!empty($e_plugin['Free']) && $e_plugin['Free'] === 'true');
                if (!empty($plugins[$e_plugin_name]['price'])) {
                    $plugins[$e_plugin_name]['Free'] = $this->compare_price($plugins[$e_plugin_name], 0, '=');
                }
            }
            $this->addons_manager = $plugins;
        } else {
            $plugins = $this->addons_manager;
        }
        if (!$core) {
            unset($plugins['e-addons-for-elementor']);
        }
        return $plugins;
    }

    /**
     * Licenses init
     *
     * @since 1.0.1
     *
     * @access private
     */
    public function init_versions() {
        if (is_admin()) {
            require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'file.php';
            require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-upgrader.php';
            require_once(E_ADDONS_PATH . 'core' . DIRECTORY_SEPARATOR . 'managers' . DIRECTORY_SEPARATOR . 'version.php');
            $this->version_manager = new \EAddonsForElementor\Core\Managers\Version();
        }
    }

    public function maybe_vendor_autoload($TextDomain = '') {
        $addon = $this->get_addon($TextDomain);
        if ($this->has_vendors($TextDomain)) {
            $file = $addon['path'] . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }

    public function has_vendors($TextDomain = '') {
        $addon = $this->get_addon($TextDomain);
        if (empty($addon['path'])) {
            return false;
        }
        $composer = $addon['path'] . DIRECTORY_SEPARATOR . 'composer.json';
        return file_exists($composer);
    }

    public function update_vendors($TextDomain = '') {
        $wdir = getcwd();
        $addon = $this->get_addon($TextDomain);
        if (!empty($addon['path'])) {
            $home = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR;
            if (!is_dir($home)) {
                // create dir
                mkdir($home, 0755, true);
            }

            $composer = $home . 'composer.phar';
            if (!file_exists($home . 'composer.phar')) {
                $composer_phar = 'https://getcomposer.org/composer-stable.phar';
                $tmp_file = download_url($composer_phar);
                // Copies the file to the final destination and deletes temporary file.
                copy($tmp_file, $composer);
                @unlink($tmp_file);
            }

            if (file_exists($composer)) {
                //var_dump(getcwd());
                chdir($addon['path']);
                $command = 'php ' . $composer . ' update 2>&1';
                //$result = shell_exec('export COMPOSER_HOME='.$home.'./.config/composer;');
                //shell_exec('COMPOSER_ALLOW_XDEBUG=1 php -d xdebug.remote_enable=0 -d xdebug.profiler_enable=0 -d xdebug.default_enable=0 composer.phar --version 2>&1');
                //$result = shell_exec('php composer.phar --version 2>&1');
                $command = "export COMPOSER_HOME=" . $home . ".config/composer; " . $command;
                $result = shell_exec($command);
                //var_dump($command); var_dump($result); die();
                //exec( $command, $output, $return_var ); var_dump($output);var_dump($return_var);
                if ($result) {
                    $msg = __('All Vendors in ' . $addon['Name'] . ' are succefully updated');
                    Utils::e_admin_notice($msg, 'success');
                }
            }

            chdir($wdir);
        }
    }

}
