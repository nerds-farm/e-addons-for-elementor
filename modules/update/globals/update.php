<?php

namespace EAddonsForElementor\Modules\Update\Globals;

use EAddonsForElementor\Base\Base_Global;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Update extenstion
 *
 * @since 1.0.1
 */
class Update extends Base_Global {

    public $updaters = [];
    public static $remote = false;
    
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label() {
        return __('WP Managed e-addons Updates', 'e-addons');
    }

    public function __construct() {
        parent::__construct();

        $addons = \EAddonsForElementor\Plugin::instance()->get_addons(true);
        foreach ($addons as $addon) {
            //if ($addon["PluginURI"] == 'https://e-addons.com' && (empty($addon['Channel']) || $addon['Channel'] == 'e-addons')) {
            $this->updaters[$addon['TextDomain']] = new \EAddonsForElementor\Modules\Update\Edd\Edd($addon);
            //}
        }

        if (is_admin() && !empty($_GET['page']) && in_array($_GET['page'], array('e_addons'))) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('e-addons-admin-dash-ajax', $this->get_module_url() . 'assets/js/e-addons-admin-dash-ajax.js');
        }

        add_action('e_addons/dash/more', [$this, 'more_addons']);

        add_filter('e_addons/more', function($redirect) {
            return true;
        });
        add_filter('e_addons/addons/remote', [$this, '_remote_addons'], 10, 3);
    }

    public function get_icon() {
        return 'eadd-e-addoons-updates';
    }

    public function get_pid() {
        return 6764;
    }

    public function more_addons($not_installed) {
        ?>
        <br><br><hr><br><br>        
        <h2 class="e_addons-title"><span class="e_addons_ic elementor-icon eicon-file-download"></span> Add more e-addons</h2>     
        <div class="my_e_addons">
            <?php
            if (!empty($not_installed)) {
                foreach ($not_installed as $akey => $addon) {
                    ?>
                    <div class="my_e_addon my_e_addons-add" id="my_e_addons__<?php echo $akey; ?>">
                        <div class="my_eaddon_header">
                            <?php
                            if (\EAddonsForElementor\Plugin::instance()->is_free($akey)) {
                                $install_url = substr(str_replace('plugins/', 'edd/download.php?addon=', $addon['url']), 0, -1);
                                ?>
                                <a style="background-color: <?php echo $addon['color']; ?>" class="my_e_addon_install e_addon_free e_addons-button" href="<?php echo $install_url; ?>" target="_blank" title="<?php _e('Click to install now this addons'); ?>"><span class="dashicons dashicons-download"></span> <span class="btn-txt">INSTALL FREE <span class="eadd-logo-e-addons"></span> ADDON</span></a>    
                            <?php } else {
                                $license = get_option('e_addons_' . $akey . '_license_key');
                                if ($license) {
                                    $addon['license_key'] = $license;
                                    $install_url = substr(str_replace('plugins/', 'edd/download.php?license='.$license.'&addon=', $addon['url']), 0, -1);
                                    ?>
                                    <a style="background-color: <?php echo $addon['color']; ?>" class="my_e_addon_install e_addon_pro e_addons-button" href="<?php echo $install_url; ?>" target="_blank" title="<?php _e('Click to install now this addons'); ?>"><span class="dashicons dashicons-download"></span> <span class="btn-txt">INSTALL PRO <span class="eadd-logo-e-addons"></span> ADDON</span></a>
                                <?php } else { ?>
                                <a style="background-color: <?php echo $addon['color']; ?>" class="my_e_addon_buy e_addons-button" href="<?php echo $addon['url']; ?>" target="_blank" title="<?php _e('Click to go to shop'); ?>"><span class="btn-txt"><span class="dashicons dashicons-plus"></span> PRO <span class="eadd-logo-e-addons"></span> ADDON</span></a>    
                            <?php }
                            }?>
                        </div>
                        <div class="my_eaddon_body">
                            <div class="my_eaddon_desc">                            
                                <h3 class="my_e_addon_title"><?php echo $addon['title']; ?></h3>                            
                                <figure class="my_eaddon_figure">
                                    <img class="my_e_addon_thumb" height="auto" width="120" src="<?php echo $addon['thumb']; ?>">
                                </figure>
                                <div class="my_eaddon_desc">
                                    <p><?php echo $addon['excerpt']; ?></p>
                                    <?php if (\EAddonsForElementor\Plugin::instance()->is_free($akey) || !empty($addon['license_key'])) { ?>
                                        <a class="e_addons-button e_addons-button-default" target="_blank" href="<?php echo $addon['url']; ?>?ref=dash"><span class="dashicons dashicons-info-outline"></span> Get more info</a>
                                    <?php } else { ?>
                                        <a class="e_addons-button e_addons-button-default" target="_blank" href="<?php echo $addon['url']; ?>?ref=dash"><span class="dashicons dashicons-cart"></span> Buy now</a>
                                    <?php } ?>    
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>            

            <div class="my_e_addon my_e_addons-cache<?php if (empty($not_installed)) {?> my_e_addons_foreveralone<?php } ?>" id="my_e_addons__update-cache">
                <div class="my_eaddon_header">
                    <a style="background-color: #666" class="e_addons-button my_e_addon_cache" href="?page=e_addons&update" title="Click to update e-addons cache"><span class="dashicons dashicons-cloud"></span> Check for new e-addons</span></a>    
                </div>
                <div class="my_eaddon_body">
                    <div class="my_eaddon_desc">                            
                        <h3 class="my_e_addon_title">Update your local cache</h3>
                        <div class="my_eaddon_desc">
                            <p>Discover if we released some new features or some new e-addons updates!</p>
                            <a class="e_addons-button e_addons-button-default" href="?page=e_addons&update"><span class="dashicons dashicons-update-alt"></span> Refresh now!</a>                                        
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="my_eaddon_cta my_e_addon_update-actions">
            <a href="https://e-addons.com/?p=2950" class="e_addons-button e_addons-button-primary" target="_blank"><span class="dashicons dashicons-editor-help"></span> <?php _e('Learn how-to install more e-addons', 'e-addons-for-elementor'); ?></a>            
        </div>
        <?php
    }

    public function _remote_addons($all_addons, $e_addons_plugin, $core) {

        // LOCAL CACHE
        $e_addons_json = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'e-addons.json';

        // REMOTE
        $all_addons_url = 'https://e-addons.com/edd/products.php'; // downloads

        $remote = !empty($_GET['page']) && $_GET['page'] == 'e_addons' && isset($_GET['update']);
        if ($remote && !self::$remote) {
            if (defined('E_ADDONS_DEBUG') && E_ADDONS_DEBUG) {
                $all_addons_url .= '?debug=' . E_ADDONS_DEBUG;
            }
            $all_addons_get = wp_remote_get($all_addons_url);
            if (!is_wp_error($all_addons_get) && !empty($all_addons_get['body']) && wp_remote_retrieve_response_code($all_addons_get) == 200) {
                $all_addons_get_body = $all_addons_get['body'];
                file_put_contents($e_addons_json, $all_addons_get_body); // update local cache
                Utils::e_admin_notice(__('e-addons plugins cache updated', 'e-addons-for-elementor'), 'success');
                self::$remote = true;
                $all_addons = json_decode($all_addons_get_body, true);
                if (!$core) {
                    unset($all_addons['e-addons-for-elementor']);
                }
                return $all_addons;
            }
        }

        if (file_exists($e_addons_json)) {
            $filemtime = @filemtime($e_addons_json);
            $filemtime_plugin = @filemtime($e_addons_plugin);
            if ($filemtime && $filemtime > $filemtime_plugin) {
                $e_addons_json_content = file_get_contents($e_addons_json);
                $all_addons = json_decode($e_addons_json_content, true);
                if (!$core) {
                    unset($all_addons['e-addons-for-elementor']);
                }
                return $all_addons;
            }
        }

        return $all_addons;
    }

}