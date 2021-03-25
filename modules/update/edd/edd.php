<?php

namespace EAddonsForElementor\Modules\Update\Edd;

use EAddonsForElementor\Core\Utils;

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * For further details please visit http://docs.easydigitaldownloads.com/article/383-automatic-upgrades-for-wordpress-plugins
 */
class Edd {
    
    const SHOP_URL = 'https://e-addons.com';

    public $addon;
    public $updater;

    public function __construct($addon) {
        if (!empty($addon)) {
            $this->addon = $addon;
            add_action('admin_init', [$this, 'addons_updater'], 0);
        }
    }

    public function addons_updater() {
        // retrieve our license key from the DB
        $license_key = trim(get_option('e_addons_' . $this->addon['TextDomain'] . '_license_key'));
        // https://e-addons.com/?edd_action=get_version&item_name=e-addons-for-elementor
        // https://e-addons.com/?edd_action=get_version&item_name=e-addons-dev&license=XXXX-XXXX-XXXX-XXXX
        //
        // setup the updater
        $this->updater = new \EAddonsForElementor\Modules\Update\Edd\Updater($this->addon['PluginURI'], $this->addon['file'],
                array(
            'version' => $this->addon['Version'], // current version number
            'license' => $license_key, // license key (used get_option above to retrieve from DB)
            'item_name' => $this->addon['TextDomain'], // ID of the product
            'author' => $this->addon['Author'], // author of this plugin
            'beta' => get_option('e_addons_beta'),
                )
        );
    }

    public function get_license_key() {
        return get_option('e_addons_' . $this->addon['TextDomain'] . '_license_key');
    }

    public function get_shop_url() {
        if (!empty($this->addon['PluginURI'])) {
            return $this->addon['PluginURI'];
        }
        return self::SHOP_URL;
    }

    public function do_actions($action = '') {

        if (!$action) {
            if (!empty($_GET['action'])) {
                $action = $_GET['action'];
            }
        }

        switch ($action) {

            case 'license_update':
                if (!empty($_REQUEST[$this->addon['TextDomain']]) || !empty($_REQUEST['all-access-pass'])) {
                    $license = empty($_REQUEST['all-access-pass']) ? $_REQUEST[$this->addon['TextDomain']] : $_REQUEST['all-access-pass'];
                    $license = sanitize_text_field($license);
                    $this->activate_license($license);
                }
                break;

            case 'deactivate_license':
                if (!empty($_REQUEST['e_addon']) && $_REQUEST['e_addon'] == $this->addon['TextDomain']) {
                    $this->deactivate_license();
                }
                break;

            case 'check_license':
                if (!empty($_REQUEST['e_addon']) && $_REQUEST['e_addon'] == $this->addon['TextDomain']) {
                    $check = $this->get_license();
                    $this->save_license($check);
                    $msg = '<b>' . strtoupper($this->addon['Name']) . '</b><ul>';
                    foreach ((array) $check as $lkey => $lvalue) {
                        $msg .= '<li><b>' . ucwords(str_replace('_', ' ', $lkey)) . ':</b> ' . $lvalue . '</li>';
                    }
                    $msg .= '</ul>';
                    Utils::e_admin_notice($msg, 'info');
                }
                break;
        }
    }

    /*     * **********************************
     * this illustrates how to activate
     * a license key
     * *********************************** */

    public function activate_license($license = '') {
// listen for our activate button to be clicked
        update_option('e_addons_' . $this->addon['TextDomain'] . '_license_key', $license);
        \EAddonsForElementor\Plugin::instance()->addons_manager[$this->addon['TextDomain']]['license'] = $license;

// data to send in our API request
        $api_params = array(
            'edd_action' => 'activate_license',
            'license' => $license,
            'item_name' => urlencode($this->addon['TextDomain']), // the name of our product in EDD
            'url' => home_url()
        );
        $message = false;
// Call the custom API.
        $response = wp_remote_post($this->get_shop_url(), array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

// make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

            if (is_wp_error($response)) {
                $message = $response->get_error_message();
            } else {
                var_dump($response);
                $message = __('An error occurred, please try again.');
            }
            return false;
        } else {

            $license_data = json_decode(wp_remote_retrieve_body($response));
            // $license_data->license will be either "valid" or "invalid"
            //var_dump($license_data);
            //if ($license_data->item_name != $this->addon['TextDomain']) {
            $this->save_license($license_data);
            /*
              ["payment_id"]=> int(123)
              ["customer_name"]=> string(9) "XXX ZZZ"
              ["customer_email"]=> string(21) "xxx@yyy.it"
              ["license_limit"]=> int(1000)
              ["site_count"]=> int(0)
              ["activations_left"]=> int(1000)
              ["price_id"]=> string(1) "6"
             */
            if (false === $license_data->success) {

                switch ($license_data->error) {

                    case 'expired' :

                        $message = sprintf(
                                __('Your license key expired on %s.'),
                                date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                        );
                        break;

                    case 'disabled' :
                    case 'revoked' :

                        $message = __('Your license key has been disabled.');
                        break;

                    case 'missing' :

                        $message = __('Invalid license.');
                        break;

                    case 'invalid' :
                    case 'site_inactive' :

                        $message = __('Your license is not active for this URL.');
                        break;

                    case 'item_name_mismatch' :

                        $message = sprintf(__('This appears to be an invalid license key for %s.'), $this->addon['TextDomain']);
                        break;

                    case 'no_activations_left':

                        $message = __('Your license key has reached its activation limit.');
                        break;

                    default :
//var_dump($license_data);
                        $message = __('An error occurred:');
                        $message .= ' &gt;&gt; ' . $license_data->error;
                        break;
                }
                
                return false;
            }
            /* } else {
              $message = sprintf(__('This appears to be an invalid license key for %s. The license is valid for: %s'), $this->addon['TextDomain'], $license_data->item_name);
              } */
        }

// Check if anything passed on a message constituting a failure
        if (empty($message)) {
            $message = $this->addon['Name'] . ': ' . __('Your license key is valid and addon is active.');
            \EAddonsForElementor\Core\Utils::e_admin_notice($message, 'success');
        } else {
            $message = $this->addon['Name'] . ': ' . $message;
            \EAddonsForElementor\Core\Utils::e_admin_notice($message, 'warning');
        }
        return true;
    }

    /*     * ********************************************
     * Illustrates how to deactivate a license key.
     * This will decrease the site count
     * ********************************************* */

    public function deactivate_license() {

// listen for our activate button to be clicked
        if (!empty($this->addon['license'])) {
// run a quick security check
//if (!check_admin_referer('edd_sample_nonce', 'edd_sample_nonce')) return; // get out if we didn't click the Activate button
// data to send in our API request
            $api_params = array(
                'edd_action' => 'deactivate_license',
                'license' => $this->addon['license'],
                'item_name' => urlencode($this->addon['TextDomain']), // the name of our product in EDD
                'url' => home_url()
            );

// Call the custom API.
            $response = wp_remote_post($this->get_shop_url(), array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

// make sure the response came back okay
            if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

                if (is_wp_error($response)) {
                    $message = $response->get_error_message();
                } else {
                    $message = __('An error occurred, please try again.');
                }
                if (!empty($message)) {
                    $message = $this->addon['Name'] . ': ' . $message;
                    \EAddonsForElementor\Core\Utils::e_admin_notice($message, 'warning');
                }
                return false;
            }
// decode the license data
            $license_data = json_decode(wp_remote_retrieve_body($response));
// $license_data->license will be either "deactivated" or "failed"
            $this->save_license($license_data);

            delete_option('e_addons_' . $this->addon['TextDomain'] . '_license_key');
            unset(\EAddonsForElementor\Plugin::instance()->addons_manager[$this->addon['TextDomain']]['license']);

            $message = $this->addon['Name'] . ': ' . __('You succefully deleted the addon license key.');
            \EAddonsForElementor\Core\Utils::e_admin_notice($message, 'success');

            return true;
        }
        return false;
    }

    /*     * **********************************
     * this illustrates how to check if
     * a license key is still valid
     * the updater does this for you,
     * so this is only needed if you
     * want to do something custom
     * *********************************** */

    public function get_license() {

        $license = trim(get_option('e_addons_' . $this->addon['TextDomain'] . '_license_key'));

        $api_params = array(
            'edd_action' => 'check_license',
            'license' => $license,
            'item_name' => urlencode($this->addon['TextDomain']),
            'url' => home_url()
        );

// Call the custom API.
        $response = wp_remote_post($this->addon['PluginURI'], array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

        if (is_wp_error($response))
            return false;

        $license_data = json_decode(wp_remote_retrieve_body($response));

        return $license_data;
    }

    public function save_license($license_data) {
        update_option('e_addons_' . $license_data->item_name . '_license_status', $license_data->license);
        if (!empty($license_data->expires)) {
            update_option('e_addons_' . $license_data->item_name . '_license_expires', $license_data->expires);
        }
        if (!empty(\EAddonsForElementor\Plugin::instance()->addons_manager[$license_data->item_name])) {
            \EAddonsForElementor\Plugin::instance()->addons_manager[$license_data->item_name]['license_status'] = $license_data->license;
            if (!empty($license_data->expires)) {
                \EAddonsForElementor\Plugin::instance()->addons_manager[$license_data->item_name]['license_expires'] = $license_data->expires;
            }
        }
    }

    /*     * **********************************
     * this illustrates how to check if
     * a license key is still valid
     * the updater does this for you,
     * so this is only needed if you
     * want to do something custom
     * *********************************** */

    public function check_license() {
        $license_data = self::get_license();
        return ($license_data) ? $license_data->license == 'valid' : false;
    }

}
