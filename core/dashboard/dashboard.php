<?php

namespace EAddonsForElementor\Core\Dashboard;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Module_Base;
use Elementor\Settings;
use EAddonsForElementor\Includes\Edd\Edd;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Dashboard {

    public function __construct() {
        $this->maybe_redirect_to_getting_started();
        add_action( 'admin_init', [ $this, 'on_admin_init' ] );
        add_action('admin_menu', array($this, 'e_addons_menu'), 200);
        add_action('admin_enqueue_scripts', [$this, 'add_admin_dash_assets']);

        add_action("elementor/admin/after_create_settings/elementor", array($this, 'e_addons_elementor'));

        if (empty($_GET['page']) || $_GET['page'] != 'e_addons_getting_started') {
            add_action('admin_notices', [$this, 'e_admin_notice__license']);
            add_action('admin_notices', [$this, 'e_admin_notice__banner']);
        }
        add_action('wp_ajax_e_addons_banner_dismiss_notification', array($this, 'e_addons_banner_dismiss_notification'));
        
        $addons = \EAddonsForElementor\Plugin::instance()->get_addons();
        foreach ($addons as $akey => $addon) {
            add_filter('plugin_action_links_' . $addon['plugin'], [$this, 'e_plugin_action_links_settings']);
            if (!\EAddonsForElementor\Plugin::instance()->is_addon_valid($addon)) {
                add_filter('plugin_action_links_' . $addon['plugin'], [$this, 'e_plugin_action_links_license'], 10 , 2);
            }
        }
        
        add_filter('admin_init', [$this, 'e_plugin_dash_actions']);
                
                
    }
    
    public function e_plugin_dash_actions() {
        if (!empty($_GET['page']) && $_GET['page'] == 'e_addons' && !empty($_REQUEST['action'])) {
            $e_addons = \EAddonsForElementor\Plugin::instance();
            $all_addons = Utils::get_addons(true);
            $action = sanitize_key($_REQUEST['action']);
            
            if (in_array($action, array('add', 'update'))) {
                $addon_url = esc_url_raw($_POST['url']);
                if (!empty($_POST['addon'])) {
                    $addon_name = sanitize_key($_POST['addon']);
                } else {
                    list($dwn, $addon_name) = explode('addon=', $addon_url);
                }

                $wp_plugin_dir = str_replace('/', DIRECTORY_SEPARATOR, WP_PLUGIN_DIR);
                $e_addons_path = $wp_plugin_dir . DIRECTORY_SEPARATOR . $addon_name;
                $version_manager = \EAddonsForElementor\Plugin::instance()->version_manager;
                $version_manager->addon_backup($addon_name);
                $version_manager->download_plugin($addon_url, $e_addons_path);
                $license = get_option('e_addons_' . $addon_name . '_license_key');
                if ($license) {
                    $all_addons[$addon_name]['TextDomain'] = $addon_name;
                    $edd = new EAddonsForElementor\Modules\Update\Edd\Edd($all_addons[$addon_name]);
                    $activation = $edd->activate_license($license);
                }
                $e_addons->clear_addons();
            }

            if (in_array($action, array('vendors'))) {
                if (!empty($_GET['plugin'])) {
                    $addon = sanitize_key($_REQUEST['plugin']);
                    $e_addons->update_vendors($addon);
                }
            }

            if (in_array($action, array('license_remove'))) {
                //var_dump($all_addons); die();
                foreach ($all_addons as $text_domain => $addon) {
                    $edd = new \EAddonsForElementor\Modules\Update\Edd\Edd($addon);
                    // deactivate & clear license key
                    $edd->deactivate_license();
                    delete_option('e_addons_' . $text_domain . '_license_key');
                }
                $msg = __('All licenses have been removed!');
                Utils::e_admin_notice($msg, 'success');
                $e_addons->clear_addons();
            }
            
            if (in_array($action, array('license_update'))) {
                $e_addons_plugins = $e_addons->get_addons(true);
                $not_installed = array_diff_key($all_addons, $e_addons_plugins);
                if (!empty($_REQUEST['all-access-pass'])) {
                    $all_access_pass = $_REQUEST['all-access-pass'];
                    $all_access_pass = sanitize_text_field($all_access_pass);
                    //$this->activate_license($license);
                    foreach ($not_installed as $text_domain => $addon) {
                        if (floatval($addon['price'])) {
                            update_option('e_addons_' . $text_domain . '_license_key', $all_access_pass);
                        }
                    }
                }
                foreach ($not_installed as $text_domain => $addon) {
                    if (floatval($addon['price'])) {
                        if (!empty($_REQUEST[$text_domain])) {
                            $license = $_REQUEST[$text_domain];
                            $license = sanitize_text_field($license);
                            update_option('e_addons_' . $text_domain . '_license_key', $license);
                        }
                    }
                }
                //$e_addons->clear_addons();
                wp_redirect(admin_url('?page=e_addons'));
            }

            /* if (Utils::is_plugin_active('e-addons-manager')) {
              $manager = new \EAddonsForElementor\Modules\License\Globals\Activation();
              $manager->execute_action($action);
              } */
            do_action('e_addons/dash/action', $action);
        }
    }
    
    public function e_plugin_action_links_license($actions, $plugin_file) {
        //var_dump($plugin_file); die();
        $actions['license'] = '<a style="color:brown;" title="Activate license" href="' . admin_url() . 'admin.php?page=e_addons"><b>' . __('License', 'e_addons') . '</b></a>';
        return $actions;
    }
    public function e_admin_notice__license() {
        $addons = \EAddonsForElementor\Plugin::instance()->get_addons();
        foreach ($addons as $akey => $addon) {
            if (did_action('elementor/loaded')) {
                if (!\EAddonsForElementor\Plugin::instance()->is_addon_valid($addon)) {
                    $msg = '<b>' . $addon['Name'] . __(' license is not active', 'e_addons') . '</b>' . '<br>' . 'Your copy seems to be not activated, please <a href="' . admin_url() . 'admin.php?page=e_addons">activate</a> or <a href="https://e-addons.com/plugins/' . $addon['TextDomain'] . '" target="blank">buy a new license code</a>.';
                    Utils::e_admin_notice($msg, 'error');
                }
            }
        }
    }
    
    public function e_admin_notice__banner() {
        $addons = \EAddonsForElementor\Plugin::instance()->get_addons(true);
        $has_pro = false;
        foreach ($addons as $akey => $addon) {
            if (empty($addon['Free'])) {
                $has_pro = true;
            }
        }
        //$has_pro = false;
        //delete_option('skip_banner_e_addons');        
        if (!$has_pro) {            
            $last_skip_version = get_option('skip_banner_e_addons');    
            if (!empty($addons["e-addons-for-elementor"]["Version"])) {
                $last_version = $addons["e-addons-for-elementor"]["Version"];
                if (!$last_skip_version || version_compare($last_skip_version, $last_version) < 0) {
                    $msg = '<a href="https://e-addons.com/go-pro/" target="_blank"><img src="'. E_ADDONS_URL.'assets/img/banner/go-pro.png" style="max-width:calc(100% + 20px);margin:-10px;margin-bottom:-15px;"></a>';
                    $msg .= '<a id="e-addons-banner-notice-dismiss" title="Remind me later" href="#" style="position:absolute;top:20px;right:20px;background-color:white;border-radius:50%;padding:5px;color:black;text-decoration:none;"><span class="dashicons dashicons-no"></span></a>';
                    $msg .= "<script>(function($) {
                                'use strict';
                                function dismissEAddonsBannerNotification() {
                                    jQuery( '#e-addons-banner-notice-dismiss' ).on( 'click', function( event ) {
                                        event.preventDefault();
                                        jQuery.post( ajaxurl, {
                                            action: 'e_addons_banner_dismiss_notification',
                                            version: '".$last_version."',
                                        });
                                        jQuery(this).closest('.notice').fadeOut();
                                    });
                                }
                                jQuery(dismissEAddonsBannerNotification);
                        })(jQuery);</script>";
                    Utils::e_admin_notice($msg, 'warning', false, false);
                }
            }
        }
    }
    public function e_addons_banner_dismiss_notification() {
        $data = $_POST;
        $time = time();
        if (!empty($data['version'])) {
            update_option('skip_banner_e_addons', $data['version']);
        }
        echo $time;
        die();
    }
    

    public function e_plugin_action_links_settings($links) {
        $links['settings'] = '<a title="Configure settings" href="' . admin_url() . 'admin.php?page=e_addons_settings"><b>' . __('Settings', 'e_addons') . '</b></a>';
        return $links;
    }

    /**
     * @since 1.1
     * @access public
     */
    public function maybe_redirect_to_getting_started() {
        if (!wp_doing_ajax()) {
            if (!get_transient('e_addons_activation_redirect')) {
                return;
            }        
            delete_transient('e_addons_activation_redirect');
            if (is_network_admin() || isset($_GET['activate-multi'])) {
                return;
            }
            wp_safe_redirect(admin_url('admin.php?page=e_addons_getting_started'));
            exit;
        }
    }

    public function add_admin_dash_assets() {
        if (!empty($_GET['page'])) {
            switch ($_GET['page']) {
                case 'e_addons':
                    wp_enqueue_style('e-addons-admin-dash');
                    wp_enqueue_script('e-addons-admin-dash');
                    break;
                case 'e_addons_settings':
                    wp_enqueue_style('e-addons-admin-settings');
                    break;
                //default:
                case 'e_addons_getting_started':
                    wp_enqueue_style('e-addons-admin-welcome');
                case 'e_addons_changelog':
                    wp_enqueue_style('e-addons-admin-changelog');
            }
        }
        wp_enqueue_style('e-addons-admin');
        wp_enqueue_style('e-addons-icons');
    }

    public function e_addons_elementor(Settings $settings) {
        //var_dump(ELEMENTOR_VERSION); die();
        if (version_compare(ELEMENTOR_VERSION, '3.2.4', '<')) {             
            $settings->add_section(Settings::TAB_INTEGRATIONS, 'google_maps', [
                'label' => __('Google Maps', 'elementor-pro'),
                'fields' => [
                    'google_maps_js_api_key' => [
                        'label' => __('Maps JavaScript API Key', 'elementor-pro'),
                        'field_args' => [
                            'type' => 'text',
                            'desc' => sprintf(__('To integrate custom Maps in page you need an <a href="%s" target="_blank">API Key</a>.', 'elementor-pro'), 'https://developers.google.com/maps/documentation/javascript/get-api-key'),
                        ],
                    ],
                ],
            ]);
        } else {
            if (get_option('google_maps_api_key')) {
                delete_option('google_maps_js_api_key');
            }
        }
    }

    public function e_addons_menu() {
        $e_addons_plugins = \EAddonsForElementor\Plugin::instance()->get_addons(true);

        $e_addons_count = count($e_addons_plugins) - 1;
        if ($e_addons_count) {
            $counter = '<span class="update-plugins e-count count-' . $e_addons_count . '"><span class="update-count">' . $e_addons_count . '</span></span>';
        } else {
            //$counter = '<span class="update-plugins e-count count-0"><span class="update-count">0</span></span>';
            $counter = '';
        }
        

        //$sub_page = \Elementor\Settings::PAGE_ID;
        $sub_page = 'e_addons';

        add_menu_page(
                __('e-addons Dashboard', 'e-addons-for-elementor'),
                __('Addons', 'e-addons-for-elementor') . $counter,
                'manage_options',
                $sub_page,
                [
                    $this,
                    'dashboard'
                ],
                'dashicons-admin-generic',
                '58.5'
        );

        //if (count($e_addons_plugins) > 1) {
        add_submenu_page(
                $sub_page,
                __('e-addons Dashboard', 'e-addons-for-elementor'),
                __('Dashboard', 'e-addons-for-elementor'),
                'manage_options',
                'e_addons',
                [$this, 'dashboard']
        );
        //}

        add_submenu_page(
                $sub_page,
                __('e-addons Settings', 'e-addons-for-elementor'),
                __('Settings', 'e-addons-for-elementor'),
                'manage_options',
                'e_addons_settings',
                [$this, 'settings']
        );

        if (count($e_addons_plugins) > 1) {
            add_submenu_page(
                    $sub_page,
                    __('e-addons Version Control', 'e-addons-for-elementor'),
                    __('Version Control', 'e-addons-for-elementor'),
                    'manage_options',
                    'e_addons_version',
                    [$this, 'version']
            );
        }

        add_submenu_page(
                $sub_page,
                __('e-addons Changelog', 'e-addons-for-elementor'),
                __('Changelog', 'e-addons-for-elementor'),
                'manage_options',
                'e_addons_changelog',
                [$this, 'changelog']
        );
        
        add_submenu_page(
                $sub_page,
                __('e-addons Started', 'elementor'),
                __('Getting Started', 'elementor'),
                'manage_options',
                'e_addons_getting_started',
                [$this, 'getting_started']
        );
        /*
        $redirect = apply_filters('e_addons/more', false);
        if ( !$redirect ) {
            add_submenu_page(
                    $sub_page,
                    __('e-addons Add more', 'e-addons-for-elementor'),
                    __('More e-addons', 'e-addons-for-elementor'),
                    'manage_options',
                    'e_addons_more',
                    [$this, 'more_addons']
            );
        }
         */
    }

    public function top_menu() {
        $e_addons_plugins = \EAddonsForElementor\Plugin::instance()->get_addons(true);
        ?>
        <h1 class="e_addons_heading"><i class="eadd-logo-e-addons"></i> <b>Addons</b> for Elementor</h1>

        <span class="e_addons_version">v. <?php echo $e_addons_plugins['e-addons-for-elementor']['Version']; ?></span>

        <div id="e-addons-settings-tabs-wrapper" class="nav-tab-wrapper">

            <a id="e-tab-settings" class="nav-tab<?php echo $_GET['page'] == 'e_addons' ? ' nav-tab-active' : ''; ?>" href="?page=e_addons">
                <span class="elementor-icon eicon-apps"></span> Dashboard
            </a>
            <?php //if (count($e_addons_plugins) > 1) { ?>
                <a id="e-tab-settings" class="nav-tab<?php echo $_GET['page'] == 'e_addons_settings' ? ' nav-tab-active' : ''; ?>" href="?page=e_addons_settings">
                    <span class="elementor-icon eicon-settings"></span> Settings
                </a>
            <?php //} ?>
        <!--<a id="e-tab-integration" class="nav-tab<?php echo $_GET['page'] == 'e_addons_integration' ? ' nav-tab-active' : ''; ?>" href="?page=elementor#tab-integrations">
        <span class="elementor-icon eicon-plus-square-o"></span> Integrations
        </a>-->
            <?php if (count($e_addons_plugins) > 1) { ?>
                <a id="e-tab-version" class="nav-tab<?php echo $_GET['page'] == 'e_addons_version' ? ' nav-tab-active' : ''; ?>" href="?page=e_addons_version">
                    <span class="elementor-icon eicon-history"></span> Version control
                </a>
            <?php } ?>
            <a id="e-tab-changelog" class="nav-tab<?php echo $_GET['page'] == 'e_addons_changelog' ? ' nav-tab-active' : ''; ?>" href="?page=e_addons_changelog">
                <span class="elementor-icon eicon-info-circle-o"></span> Changelog
            </a>
        </div>
        <?php
    }

    public function dashboard() {
        include_once(__DIR__ . '/pages/dash.php');
    }

    public function settings() {
        include_once(__DIR__ . '/pages/settings.php');
    }

    public function version() {
        include_once(__DIR__ . '/pages/version.php');
    }

    public function changelog() {
        include_once(__DIR__ . '/pages/changelog.php');
    }

    public function getting_started() {
        include_once(__DIR__ . '/pages/getting_started.php');
    }
    
    public function more_addons() {        
        $this->dashboard();
    }
    
    public function on_admin_init() {
        if ( !empty( $_GET['page'] ) && 'e_addons_more' === $_GET['page'] ) {            
            wp_redirect( 'https://e-addons.com/?p=2950' );
            die;
        }
    }

}