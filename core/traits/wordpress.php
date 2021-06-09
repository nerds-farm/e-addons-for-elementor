<?php

namespace EAddonsForElementor\Core\Traits;

use EAddonsForElementor\Core\Utils;

/**
 * @author francesco
 */
trait Wordpress {
    
    public static $admin_notices_fired = false;
    public static $notices = [];

    public static function e_admin_notice($msg = '', $level = 'warning', $dismissible = true, $wrapper = true) {
        $msg = Utils::to_string($msg);
        ob_start();
        ?>
        <div class="e-add-notiice <?php echo $level . ' notice-' . $level; ?> notice<?php echo $dismissible ? ' is-dismissible' : ''; ?>">
            <?php if ($wrapper) { ?>
            <i class="eadd-logo-e-addons"></i>
            <p>
                <strong>e-addons:</strong> 
                <?php _e($msg, 'e-addons-for-elementor'); ?>
            </p>
            <?php } else { _e($msg, 'e-addons-for-elementor'); } ?>
        </div>
        <?php
        $notice = ob_get_clean();
        if (self::$admin_notices_fired) {
            echo $notice;
        } else {
            self::$notices[] = $notice;
            //add_action('admin_notices', '\EAddonsForElementor\Core\Utils::e_admin_notices');
        }
        
    }
    
    public static function e_admin_notices() {
        foreach(self::$notices as $notice) {
            echo $notice;
        }
        self::$admin_notices_fired = true;
    }

    public static function e_admin_banner_notice($msg = '', $unique_id = '') {
        $notice_id = 'e_addons_notice_close_' . $unique_id;
        $maybe_show = !get_option($notice_id);
        $msg = Utils::to_string($msg);
        if ($maybe_show && $msg) {
            ?>
            <div class="e-add-notiice notice-success notice is-dismissible" id="<?php echo $notice_id; ?>">
                <?php echo ($msg); ?>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            </div>
            <script>
                jQuery(function () {
                    jQuery("#<?php echo $notice_id; ?> .notice-dismiss").on('click', function (e) {
                        e.preventDefault();
                        jQuery.ajax({
                            type: "post",
                            dataType: "json",
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            data: {action: 'close_banner_notice_action', unique_id: '<?php echo $unique_id; ?>'},
                            success: function (response) {
                                if (response.type == "success") {
                                    //
                                }
                            }
                        });
                        jQuery('#<?php echo $notice_id; ?>').fadeOut();
                    });
                });
            </script>
            <?php
        }
    }

    /*     * *********************************************************************** */

    public static function get_options($filter = '') {
        global $wpdb;
        $options = array();
        $query = 'SELECT option_name FROM ' . $wpdb->prefix . 'options';
        if ($filter) {
            if (is_array($filter)) {
                $query .= " WHERE option_name IN ('" . implode("','", $filter) . "')";
            } else {
                $query .= " WHERE option_name LIKE '%" . $filter . "%'";
            }
        }
        $results = $wpdb->get_results($query);
        if (!empty($results)) {
            foreach ($results as $key => $aopt) {
                $options[$aopt->option_name] = $aopt->option_name;
            }
            ksort($options);
        }
        return $options;
    }

    public static function get_object_fields($obj, $filter = false) {
        $obj_vars = array();
        $object_vars = get_object_vars($obj);
        if (isset($object_vars['data'])) { // WP_User
            unset($object_vars['data']);
            $object_vars = array_merge($object_vars, get_object_vars($obj->data));
        }
        if (!empty($filter) && is_string($filter)) {
            foreach ($object_vars as $key => $var) {
                if (is_array($var)) {
                    $var = $key;
                }
                if (stripos($var, $filter) === false && stripos($key, $filter) === false) {
                    continue;
                }
                $obj_vars[$key] = $var;
            }
        } else {
            $obj_vars = $object_vars;
        }
        $fields = array_keys($obj_vars);
        return $fields;
    }

    public static function get_fields($obj, $meta = false, $info = true) {
        $fields = array();
        if (is_string($obj)) {
            switch ($obj) {
                case 'post':
                    $obj = get_post();
                    break;
                case 'user':
                    $obj = get_user_by('ID', get_current_user_id());
                    break;
                case 'term':
                    $obj = get_term_by('id', 1, 'category');
                    break;
                case 'comment':
                    $obj = get_comment(1);
                    break;
            }
        }
        if ($obj && is_object($obj)) {
            $obj_class = get_class($obj);
            list($wp, $type) = explode('_', $obj_class, 2);
            $type = strtolower($type);

            $obj_fields = self::get_object_fields($obj, $meta);
            if (!empty($obj_fields)) {
                foreach ($obj_fields as $field) {
                    $name = str_replace($type . '_', '', $field);
                    $name = str_replace('_', ' ', $name);
                    $name = ucwords($name);
                    if ($info) {
                        $name .= ' (' . $field . ')';
                    }
                    $fields[$field] = $name;
                }
            }

            if ($meta) {
                $metas = self::get_metas($type, $meta, $info);
                foreach ($metas as $mkey => $ameta) {
                    $fields[$mkey] = $ameta;
                }
            }
        }
        return $fields;
    }

    public static function get_field($obj, $field, $single = true) {
        return self::get_wp_object_field($obj, $field, $single);
    }
    
    public static function get_wp_object_field($obj, $field, $single = true) {
        $value = $type = null;

        if ($value === null) {
            if (property_exists($obj, $field)) {
                $value = $obj->{$field};
            }
        }
        if ($value === null) {
            $class = strtolower(get_class($obj));
            $tmp = explode('_', $class);
            if (count($tmp) == 2) {
                list($wp, $type) = $tmp;
            } else {
                $type = 'user';
            }
            if (property_exists($obj, $type . '_' . $field)) {
                $value = $obj->{$type . '_' . $field};
            }
        }
        if ($value === null && $type) {
            $obj_id = self::get_id($obj);
            if (metadata_exists($type, $obj_id, $field)) {
                $value = get_metadata($type, $obj_id, $field, $single);
            }
        }

        if ($value === null) {
            if (get_class($obj) == 'WP_User' && property_exists($obj, 'data')) {
                $obj = $obj->data;
                return self::get_wp_object_field($obj, $field, $single);
            }
        }

        return $value;
    }

    public static function get_metas($obj = 'post', $like = '', $info = true) {
        global $wpdb;
        $metas = array();

        $table_join = '';
        if ($obj == 'attachment') {
            $table_join .= " AND post_id IN ( SELECT id FROM " . $wpdb->prefix . "posts WHERE post_type LIKE '" . $obj . "' )";
            $obj = 'post';
        }

        if ($obj == 'post') {
            // REGISTERED in FUNCTION
            $post_types = self::get_post_types();
            foreach ($post_types as $tkey => $tvalue) {
                $registered_meta_keys = get_registered_meta_keys($tkey);
                if (!empty($registered_meta_keys)) {
                    foreach ($registered_meta_keys as $mkey => $meta) {
                        if (!empty($like) && is_string($like)) {
                            if (stripos($mkey, $like) === false) {
                                continue;
                            }
                        }
                        $field_name = $mkey;
                        if ($info) {
                            $field_name .= ' [' . $meta['type'] . ']';
                        }
                        $metas[$mkey] = $field_name;
                    }
                }
            }
        }

        // FROM DB        
        $table = $wpdb->prefix . $obj . 'meta';
        if ($obj == 'user') {
            if (defined('CUSTOM_USER_META_TABLE')) {
                $table = CUSTOM_USER_META_TABLE;
            }
        }
        $query = 'SELECT DISTINCT meta_key FROM ' . $table;
        if (!empty($like) && is_string($like)) {
            $query .= " WHERE meta_key LIKE '%" . $like . "%'";
        }
        $query .= $table_join;
        //var_dump($query); die();
        $results = $wpdb->get_results($query);
        if (!empty($results)) {
            $db_metas = array();
            foreach ($results as $ares) {
                $db_metas[$ares->meta_key] = $ares->meta_key;
            }
            ksort($db_metas);
            $manual_metas = $db_metas;
            foreach ($manual_metas as $ameta) {
                if (substr($ameta, 0, 1) == '_') {
                    $tmp = substr($ameta, 1);
                    if (in_array($tmp, $manual_metas)) {
                        continue;
                    }
                }
                if (substr($ameta, 0, 8) == '_oembed_') {
                    continue;
                }
                if (!isset($metas[$ameta])) {
                    $metas[$ameta] = $ameta;
                }
            }
        }
        return $metas;
    }

    public static function is_meta($meta = false, $obj = 'post', $extend = false) {
        switch ($obj) {
            case 'comment':
                $fields = array('comment_ID', 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_author_IP', 'comment_date', 'comment_date_gmt', 'comment_content', 'comment_karma', 'comment_approved', 'comment_agent', 'comment_type', 'comment_parent', 'user_id');
                break;
            case 'user':
                $fields = array('ID', 'user_login', 'user_pass', 'user_nicename', 'user_email', 'user_url', 'user_registered', 'user_activation_key', 'user_status', 'display_name');
                break;
            case 'term':
                $fields = array('term_id', 'name', 'slug', 'term_group', 'term_order');
                if ($extend) {
                    array_push($fields, 'parent', 'description', 'taxonomy', 'count');
                }
                break;
            case 'post':
            default:
                $fields = array('ID', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt', 'post_status', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_content_filtered', 'post_parent', 'guid', 'menu_order', 'post_type', 'post_mime_type', 'comment_count');
        }
        if (in_array($meta, $fields)) {
            return false;
        }
        return true;
    }

    public static function get_queried_object_type() {
        $queried_object = get_queried_object();
        if (is_object($queried_object)) {
            switch (get_class($queried_object)) {
                case 'WP_Term':
                    return 'term';
                case 'WP_User':
                    return 'user';
                case 'WP_Comment':
                    return 'comment';
                case 'WP_Post':
                default:
                    return 'post';
            }
        }
        return false;
    }

    public static function get_link($obj = null) {
        return self::get_permalink($obj);
    }

    public static function get_permalink($obj = null, $type = 'post') {
        if (is_numeric($obj) || empty($obj)) {
            switch ($type) {
                case 'term':
                    return self::get_term_url($obj);
                case 'user':
                    return self::get_user_url($obj);
                case 'comment':
                    return self::get_comment_url($obj);
                case 'post':
                default:
                    return get_permalink($obj);
            }
        }
        if (is_string($obj)) {
            return $obj;
        }
        if (is_array($obj)) {
            if (!empty($obj['term_id'])) {
                return self::get_term_url($obj['term_id']);
            }
            if (!empty($obj['comment_ID'])) {
                return self::get_comment_url($obj['comment_ID']);
            }
            if (!empty($obj['ID'])) {
                if (!empty($obj['user_login'])) {
                    return self::get_user_url($obj['ID']);
                }
                return self::get_post_url($obj['ID']);
            }
        }
        if (is_object($obj)) {
            switch (get_class($obj)) {
                case 'WP_Post':
                    return self::get_post_url($obj->ID);
                case 'WP_Term':
                    return self::get_term_url($obj->term_id);
                case 'WP_User':
                    return self::get_user_url($obj->ID);
                case 'WP_Comment':
                    return self::get_comment_url($obj->comment_ID);
            }
        }
        return false;
    }

    public static function get_id($obj = null) {
        if (empty($obj)) {
            return get_the_ID();
        }
        if (filter_var($obj, FILTER_VALIDATE_URL)) {
            return url_to_postid($obj);
        }
        if (is_numeric($obj)) {
            return intval($obj);
        }
        if (is_string($obj)) {
            return intval($obj);
        }
        if (is_array($obj)) {
            if (!empty($obj['term_id'])) {
                return $obj['term_id'];
            }
            if (!empty($obj['comment_ID'])) {
                return $obj['comment_ID'];
            }
            if (!empty($obj['ID'])) {
                return $obj['ID'];
            }
        }
        if (is_object($obj)) {
            switch (get_class($obj)) {
                case 'WP_Post':
                    return $obj->ID;
                case 'WP_Term':
                    return $obj->term_id;
                case 'WP_User':
                    return $obj->ID;
                case 'WP_Comment':
                    return $obj->comment_ID;
            }
        }
        return false;
    }

    public static function get_image($meta = null) {
        $id = '';
        $url = '';
        if (!empty($meta)) {
            if (is_numeric($meta)) {
                $id = intval($meta);
                $url = wp_get_attachment_url($id);
            }
            if (is_string($meta)) {
                if (filter_var($meta, FILTER_VALIDATE_URL)) {
                    $id = attachment_url_to_postid($meta);
                    if ($id) {
                        $url = $meta;
                    }
                }
            }
            if (is_array($meta)) {
                if (isset($meta['url'])) {
                    $url = $meta['url'];
                }
                if (isset($meta['src'])) {
                    $url = $meta['src'];
                }
                if (isset($meta['guid'])) {
                    $url = $meta['guid'];
                }
                if (isset($meta['ID'])) {
                    $id = intval($meta['ID']);
                    $url = wp_get_attachment_url($id);
                }
            }
        }
        if ($url) {
            return array('id' => $id, 'url' => $url);
        }
        return false;
    }

}
