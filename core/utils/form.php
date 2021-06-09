<?php

namespace EAddonsForElementor\Core\Utils;

use EAddonsForElementor\Core\Utils;

/**
 * Description of Form Utils
 *
 */
class Form {

    public static function get_form_data($record, $raw = false, $extra = false, $settings = array()) {
        // Get sumitetd Form data
        $raw_fields = $record->get('fields');

        // Normalize the Form Data
        $fields = [];
        foreach ($raw_fields as $id => $field) {
            if ($raw) {
                $fields[$id] = $field['raw_value'];
            } else {
                $fields[$id] = $field['value'];
            }
        }
        
        //var_dump($_POST);
        if (!empty($_POST['form_fields'])) {
            foreach ($_POST['form_fields'] as $id => $field) {
                if (!isset($fields[$id])) {
                    $fields[$id] = $field;
                }
            }
        }
        
        foreach ($fields as $id => $field) {
            $tmp = explode('__value', $id); // media field
            if (count($tmp) == 2) {
                $oid = reset($tmp);
                if (empty($fields[$oid])) {
                    $fields[$oid] = $fields[$id];
                    unset($fields[$id]);
                }
            }
        }

        if ($extra) {
            $extra_fields = self::get_form_extra_data($record, $fields, $settings);
            foreach ($extra_fields as $key => $value) {
                $fields[$key] = $value;
            }
        }

        global $e_form;
        if (!empty($e_form) && is_array($e_form)) {
            foreach ($fields as $key => $value) {
                $e_form[$key] = $value;
            }
        } else {
            $e_form = $fields; // for form twig
        }
        // set them globally in _POST var
        foreach ($fields as $key => $value) {
            $_POST[$key] = $value;
        }

        $post_id = !empty($_POST['queried_id']) ? absint($_POST['queried_id']) : absint($_POST['post_id']);
        if ($post_id) {
            // force post for Dynamic Tags and Widgets
            global $post, $wp_query;
            $post = get_post($post_id);
            $wp_query->queried_object = $post;
            $wp_query->queried_object_id = $post_id;
        }

        return $fields;
    }

    public static function get_form_extra_data($record, $fields = null, $settings = null) {

        $user_id = get_current_user_id();
        $queried_id = $post_id = get_queried_object_id();

        if (is_object($record)) {
            $form_name = $record->get_form_settings('form_name');
        } else {
            $form_name = !empty($settings['form_name']) ? $settings['form_name'] : '';
        }

        // get current page
        $document = get_queried_object();
        if ($document && get_class($document) == 'WP_Post') {
            $post_id = $document->ID;
        }
        if (isset($_POST['post_id'])) {
            $post_id = absint($_POST['post_id']);
            $document = get_post($post_id);
        }

        $referrer = isset($_POST['referrer']) ? esc_url($_POST['referrer']) : $_SERVER['HTTP_REFERER'];
        if ($referrer) {
            $queried_id_tmp = url_to_postid($referrer);
            if ($queried_id_tmp) {
                $post = get_post($queried_id_tmp);
                if ($post) {
                    $queried_id = $queried_id_tmp;
                }
            }
        }
        if (isset($_POST['queried_id'])) {
            $queried_id = absint($_POST['queried_id']);
            $post = get_post($queried_id);
        }

        return [
            'queried_id' => $queried_id,
            'post_id' => $post_id,
            'user_id' => $user_id,
            'ip_address' => \ElementorPro\Core\Utils::get_client_ip(),
            'referrer' => $referrer,
            'form_name' => $form_name,
            'form_id' => sanitize_title($_POST['form_id']),
        ];
    }

    public static function do_setting_shortcodes($setting, $fields = array(), $urlencode = false) {
        // Shortcode can be `[field id="fds21fd"]` or `[field title="Email" id="fds21fd"]`, multiple shortcodes are allowed
        if (!empty($fields)) {
            if (strpos($setting, '[field id=') !== false) {
                foreach ($fields as $fkey => $fvalue) {
                    if (!is_object($fvalue)) {
                        $fvalue = Utils::to_string($fvalue);
                        if ($urlencode) {
                            $fvalue = urlencode($fvalue);
                        }
                        if (!is_object($fvalue)) {
                            $setting = str_replace('[field id=' . $fkey . ']', $fvalue, $setting);
                            $setting = str_replace("[field id='" . $fkey . "']", $fvalue, $setting);
                            $setting = str_replace('[field id="' . $fkey . '"]', $fvalue, $setting);
                        }
                    }
                }
            }
        }

        $setting = preg_replace_callback('/(\[field[^]]*id="(\w+)"[^]]*\])/', function($matches) use ($urlencode, $fields) {
            $value = '';
            if (isset($fields[$matches[2]])) {
                $value = $fields[$matches[2]];
            }
            if ($urlencode) {
                $value = urlencode($value);
            }
            return $value;
        }, $setting);

        return $setting;
    }

    public static function options_array($string = '', $val = 'pro') {
        $arr = explode(PHP_EOL, $string);
        foreach ($arr as $akey => $astring) {
            $pieces = explode('|', $astring, 2);
            if (count($pieces) > 1) {
                if ($val == 'pro') {
                    $arr[$akey] = array('text' => reset($pieces), 'value' => end($pieces));
                }
                if ($val == 'acf') {
                    $arr[$akey] = array('text' => end($pieces), 'value' => reset($pieces));
                }
                if ($val == 'flatpickr') {
                    $arr[$akey] = array('from' => reset($pieces), 'to' => end($pieces));
                }
            }
        }
        return $arr;
    }

    public static function form_field_value($arr = array(), $default = '') {
        $str = '';
        if (empty($arr)) {
            return $str;
        }
        if (is_string($arr))
            $arr = Utils::explode($arr);
        if (is_object($arr))
            $arr = (array) $arr;
        if (!empty($arr) && is_array($arr)) {
            $i = 0;
            foreach ($arr as $key => $value) {
                $str_tmp = '';

                // object
                if (is_object($value) && get_class($value) == 'WP_Post') {
                    $str_tmp .= $value->ID;
                }
                if (is_object($value) && get_class($value) == 'WP_User') {
                    $str_tmp .= $value->ID;
                }
                if (is_object($value) && get_class($value) == 'WP_Term') {
                    $str_tmp .= $value->term_id;
                }

                // array
                if (is_array($value) && isset($value['post_title'])) {
                    $str_tmp .= $value['ID'];
                }
                if (is_array($value) && isset($value['display_name'])) {
                    $str_tmp .= $value['ID'];
                }
                if (is_array($value) && isset($value['term_id'])) {
                    $str_tmp .= $value['term_id'];
                }

                // INT or String
                if (!$str_tmp) {
                    $str_tmp = $value;
                }

                $str .= $str_tmp;
                if ($i < count($arr) - 1) {
                    $str .= ',';
                }
                $i++;
            }
        }
        if ($str == '') {
            $str = $default;
        }
        return $str;
    }

    // convert an array to a options list compatible with Elementor PRO Form
    public static function array_options($arr = array(), $val = 'keys') {
        $str = '';
        if (empty($arr)) {
            return false;
        }
        if (is_string($arr))
            $arr = Utils::str_to_array($arr);
        if (is_object($arr))
            $arr = (array) $arr;
        if (!empty($arr) && is_array($arr)) {
            if ($val && !in_array($val, array('keys', 'post', 'term', 'user'))) {
                $str = $val . '|' . PHP_EOL;
            }
            $i = 0;
            foreach ($arr as $key => $value) {
                $str_tmp = '';

                // object
                if (is_object($value) && get_class($value) == 'WP_Post') {
                    $str_tmp .= $value->post_title . '|' . $value->ID;
                }
                if (is_object($value) && get_class($value) == 'WP_User') {
                    $str_tmp .= $value->display_name . '|' . $value->ID;
                }
                if (is_object($value) && get_class($value) == 'WP_Term') {
                    $str_tmp .= $value->name . '|' . $value->term_id;
                }

                // array
                if (is_array($value) && isset($value['post_title'])) {
                    $str_tmp .= $value['post_title'] . '|' . $value['ID'];
                }
                if (is_array($value) && isset($value['display_name'])) {
                    $str_tmp .= $value['display_name'] . '|' . $value['ID'];
                }
                if (is_array($value) && isset($value['name'])) {
                    $str_tmp .= $value['name'] . '|' . $value['term_id'];
                }

                // INT
                if (is_numeric($value)) {
                    $value = intval($value);
                    if ($val == 'post') {
                        $str_tmp = get_the_title($value) . '|' . $value;
                    }
                    if ($val == 'user') {
                        $tmp_user = get_user_by('ID', $value);
                        if ($tmp_user) {
                            $str_tmp = $tmp_user->display_name . '|' . $value;
                        }
                    }
                    if ($val == 'term') {
                        $tmp_term = Utils::get_term_by('id', $value);
                        if ($tmp_term) {
                            $str_tmp = $tmp_term->name . '|' . $value;
                        }
                    }
                }

                if (!$str_tmp) {
                    if ($val == 'keys' || !is_numeric($key)) {
                        $str_tmp .= $value . '|' . $key;
                    } else {
                        $str_tmp .= $value;
                    }
                }

                $str .= $str_tmp;
                if ($i < count($arr) - 1) {
                    $str .= PHP_EOL;
                }
                $i++;
            }
        }
        //var_dump($str);
        return $str;
    }

    public static function get_field($custom_id, $settings = array()) {
        if (empty($settings)) {
            $form_id = self::get_form_id();
            if ($form_id) {
                $settings = Utils::get_settings_by_element_id($form_id);
            }
        }
        if (!empty($settings['form_fields'])) {
            foreach ($settings['form_fields'] as $afield) {
                if ($afield['custom_id'] == $custom_id) {
                    return $afield;
                }
            }
        }
        return false;
    }

    public static function get_field_type($custom_id, $settings = array()) {
        $field = self::get_field($custom_id, $settings);
        if ($field && !empty($field['field_type'])) {
            return $field['field_type'];
        }
        return false;
    }

    public static function tablefy($html = '') {
        $table_replaces = array(
            'table' => '.elementor-container',
            'tr' => '.elementor-row',
            'td' => '.elementor-column',
        );
        $dom = new \PHPHtmlParser\Dom;
        $dom->loadStr($html);
        foreach ($dom->find('.elementor-container') as $tag) {
            $changeTagTable = function() {
                $this->name = 'table';
            };
            $changeTagTable->call($tag->tag);
        }
        foreach ($dom->find('.elementor-row') as $tag) {
            $changeTagTr = function() {
                $this->name = 'tr';
            };
            $changeTagTr->call($tag->tag);
        }
        foreach ($dom->find('.elementor-column') as $tag) {
            $changeTagTd = function() {
                $this->name = 'td';
            };
            $changeTagTd->call($tag->tag);
        }
        $html_table = (string) $dom;
        return $html_table;
    }

    public static function get_post_css($p_id = false, $theme = false) {
        $css = '';
        $upload = wp_upload_dir();
        $elementor_styles = array(
            'elementor-custom-frontend' => ELEMENTOR_ASSETS_PATH . 'css/custom-frontend.css',
            'elementor-frontend' => ELEMENTOR_ASSETS_PATH . 'css/frontend.min.css',
            'elementor-common' => ELEMENTOR_ASSETS_PATH . 'css/common.min.css',
            'elementor-global' => $upload['basedir'] . '/elementor/css/global.css',
        );
        if ($theme) {
            $elementor_styles['theme-style'] = STYLESHEETPATH . '/style.css';
            if (is_child_theme()) {
                $elementor_styles['theme-style-child'] = TEMPLATEPATH . '/style.css';
                $elementor_styles['theme-assets-style'] = TEMPLATEPATH . '/assets/css/style.css';
            }
        }
        if ($p_id) {
            $elementor_styles['elementor-post-' . $p_id . '-css'] = $upload['basedir'] . '/elementor/css/post-' . $p_id . '.css';
        }
        if (Utils::is_plugin_active('elementor-pro')) {
            $elementor_styles['elementor-pro-frontend'] = ELEMENTOR_PRO_ASSETS_PATH . 'css/frontend.min.css';
        }
        foreach ($elementor_styles as $style) {
            if (file_exists($style)) {
                $css .= file_get_contents($style);
            }
        }
        return $css;
    }

    public static function get_elementor_elements($type = '') {
        global $wpdb;
        $sql_query = "SELECT * FROM " . $wpdb->prefix . "postmeta
		WHERE meta_key LIKE '_elementor_data'
		AND meta_value LIKE '%\"widgetType\":\"" . $type . "\"%'
            AND post_id IN (
            SELECT id FROM " . $wpdb->prefix . "posts
            WHERE post_status LIKE 'publish'
          )";

        $results = $wpdb->get_results($sql_query);
        if (!count($results)) {
            return false;
        }
        $elements = array();
        foreach ($results as $result) {
            $p_id = $result->post_id;
            $e_data = $result->meta_value;
            $elements_tmp = self::get_elements_from_data($e_data, 'form');
            if (!empty($elements_tmp)) {
                foreach ($elements_tmp as $key => $value) {
                    $elements[$p_id][$key] = $value;
                }
            }
        }

        return $elements;
    }

    public static function get_elements_from_data($e_data, $type = '') {
        $elements = array();
        if (is_string($e_data)) {
            $e_data = json_decode($e_data);
        }
        if (!empty($e_data)) {
            foreach ($e_data as $element) {
                if ($type && $element->widgetType == $type) {
                    $elements[$element->id] = $element->settings;
                }
                if (!empty($element->elements)) {
                    $elements_tmp = self::get_elements_from_data($element->elements, $type);
                    if (!empty($elements_tmp)) {
                        foreach ($elements_tmp as $key => $value) {
                            $elements[$key] = $value;
                        }
                    }
                }
            }
        }
        return $elements;
    }

    /**
     * @param string      $email_content
     * @param Form_Record $record
     *
     * @return string
     */
    public static function replace_content_shortcodes($email_content = '', $record = array(), $line_break = '<br>') {

        $all_fields_shortcode = '[all-fields]';
        $text = self::get_shortcode_value($all_fields_shortcode, $email_content, $record, $line_break);
        $email_content = str_replace($all_fields_shortcode, $text, $email_content);

        $all_valued_fields_shortcode = '[all-fields|!empty]';
        $text = self::get_shortcode_value($all_valued_fields_shortcode, $email_content, $record, $line_break, false);
        $email_content = str_replace($all_valued_fields_shortcode, $text, $email_content);

        if ($email_content) {
            global $e_form;
            $pdf_form = '[form:pdf]';
            if (strpos($email_content, $pdf_form) !== false) {
                $value = '';
                if (!empty($e_form['pdf']['url'])) {
                    $value = $e_form['pdf']['url'];
                }
                $email_content = str_replace($pdf_form, $value, $email_content);
            }
            $pdf_form = '[form:pdf:';
            if (strpos($email_content, $pdf_form) !== false) {
                $tmp = explode($pdf_form, $email_content);
                foreach ($tmp as $key => $pdf) {
                    if ($key) {
                        list($field, $tmp) = explode(']', $pdf, 2);
                        $value = '';
                        if (!empty($e_form['pdf'][$field])) {
                            $value = $e_form['pdf'][$field];
                        }
                        $email_content = str_replace($pdf_form . $field . ']', $value, $email_content);
                    }
                }
            }
        }

        return $email_content;
    }

    public static function get_shortcode_value($shortcode = '[all-fields]', $email_content = '', $record = array(), $line_break = '<br>', $show_empty = true) {
        $text = '';
        if (false !== strpos($email_content, $shortcode)) {
            $fields = $record;
            if (is_object($record)) {
                $fields = $record->get('fields');
            }
            foreach ($fields as $fkey => $field) {
                $formatted = '';
                if (is_string($field) || empty($field['type'])) {
                    $formatted = $fkey . ': ' . Utils::to_string($field);
                } else {
                    if (!empty($field['title'])) {
                        $formatted = sprintf('%s: %s', $field['title'], $field['value']);
                    } elseif (!empty($field['value'])) {
                        $formatted = sprintf('%s', $field['value']);
                    }
                    if (( 'textarea' === $field['type'] ) && ( '<br>' === $line_break )) {
                        $formatted = str_replace(["\r\n", "\n", "\r"], '<br />', $formatted);
                    }
                    if (!$show_empty && empty($field['value']))
                        continue;
                }
                $text .= $formatted . $line_break;
            }
        }
        return $text;
    }

    public static function get_plain_txt($e_message_content_txt, $line_break = PHP_EOL) {
        $e_message_content_txt = str_replace('</p>', '</p><br /><br />', $e_message_content_txt);
        $e_message_content_txt = str_replace('<br />', $line_break, $e_message_content_txt);
        $e_message_content_txt = str_replace('<br/>', $line_break, $e_message_content_txt);
        $e_message_content_txt = str_replace('<br>', $line_break, $e_message_content_txt);
        $e_message_content_txt = str_replace('\n', $line_break, $e_message_content_txt);
        $e_message_content_txt = strip_tags($e_message_content_txt);
        return $e_message_content_txt;
    }

    public static function get_attachments($fields, $settings, $amail = array(), $e_form_email_content = '', $url = false) {
        $attachments = array();

        if ($e_form_email_content) {
            $pdf_attachment = '<!--[e_form_pdf:attachment]-->';
            $pdf_form = '[form:pdf]';
            if (strpos($e_form_email_content, $pdf_attachment) !== false || strpos($e_form_email_content, $pdf_form) !== false) {
                global $e_form;
                if (!empty($e_form['pdf'])) {
                    if ($url) {
                        if (!empty($e_form['pdf']['url'])) {
                            $attachments[] = $e_form['pdf']['url'];
                        }
                    } else {
                        if (!empty($e_form['pdf']['path'])) {
                            $attachments[] = $e_form['pdf']['path'];
                        }
                    }
                }
                $e_form_email_content = str_replace($pdf_attachment, '', $e_form_email_content);
                $e_form_email_content = str_replace($pdf_form, '', $e_form_email_content);
            }
        }

        if (!empty($fields) && is_array($fields)) {
            foreach ($fields as $akey => $adatas) {
                $afield = self::get_field($akey, $settings);
                if ($afield) {
                    if (in_array($afield['field_type'], array('upload', 'media', 'signature'))) {
                        $files = Utils::explode($adatas);
                        if (!empty($files)) {
                            foreach ($files as $adata) {
                                if (filter_var($adata, FILTER_VALIDATE_URL)) {
                                    $file_path = Utils::url_to_path($adata);
                                    if (is_file($file_path)) {
                                        if ($url) {
                                            if (!in_array($adata, $attachments)) {
                                                $attachments[] = $adata;
                                            }
                                        } else {
                                            if (!in_array($file_path, $attachments)) {
                                                $attachments[] = $file_path;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!empty($amail) && !empty($amail['e_form_attachments_file'])) {
            $media_ids = Utils::explode($amail['e_form_attachments_file']);
            if (!empty($media_ids)) {
                foreach ($media_ids as $attachment_id) {
                    $file_path = get_attached_file($attachment_id);
                    if (!in_array($file_path, $attachments)) {
                        $attachments[] = $file_path;
                    }
                }
            }
        }

        return $attachments;
    }

    public static function delete_attachments($fields, $settings) {
        $attachments = self::get_attachments($fields, $settings);
        if (!empty($attachments) && is_array($attachments)) {
            foreach ($attachments as $filename) {
                unlink($filename);
            }
        }
    }

    public static function is_multiple($afield) {
        return $afield['field_type'] == 'checkbox' || ($afield['field_type'] == 'select' && $afield['allow_multiple']) || ($afield['field_type'] == 'upload' && $afield['allow_multiple_upload']);
    }

    public static function save_upload_media($fields, $settings, $obj_id = 0) {
        if (!empty($fields) && is_array($fields)) {
            foreach ($fields as $akey => $adatas) {
                $afield = self::get_field($akey, $settings);
                if ($afield) {
                    if (in_array($afield['field_type'], array('upload', 'media', 'signature'))) {
                        $files = Utils::explode($adatas);
                        if (!empty($files)) {
                            foreach ($files as $adata) {
                                if (filter_var($adata, FILTER_VALIDATE_URL)) {
                                    //$adata = str_replace(get_bloginfo('url'), WP, $value);
                                    $filename = Utils::url_to_path($adata);
                                    if (is_file($filename)) {                                        
                                        $attach_id = Utils::url_to_postid($adata); // check if exists
                                        if (!$attach_id) {
                                            // Check the type of file. We'll use this as the 'post_mime_type'.
                                            $filetype = wp_check_filetype(basename($filename), null);
                                            $fileinfo = pathinfo($filename);
                                            // Prepare an array of post data for the attachment.
                                            $attachment = array(
                                                'guid' => $adata,
                                                'post_mime_type' => $filetype['type'],
                                                'post_status' => 'inherit',
                                                'post_title' => $fileinfo['filename'],
                                                    //'post_content' => '',
                                            );
                                            if ($obj_id) {
                                                $attachment['post_parent'] = $obj_id;
                                            }
                                            // Insert the attachment.
                                            $attach_id = wp_insert_attachment($attachment, $filename, $obj_id);
                                            // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                            // Generate the metadata for the attachment, and update the database record.
                                            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                                            wp_update_attachment_metadata($attach_id, $attach_data);
                                        }
                                        if ($afield['allow_multiple_upload']) {
                                            if (is_array($fields[$akey])) {
                                                $fields[$akey][] = $attach_id;
                                            } else {
                                                $fields[$akey] = array($attach_id);
                                            }
                                        } else {
                                            $fields[$akey] = $attach_id;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $fields;
    }

    public static function save_extra($obj_id, $type, $settings, $fields) {

        if ($settings['e_form_save_' . ($type ? $type . '_' : '') . 'file']) {
            $fields = Form::save_upload_media($fields, $settings, $obj_id);
        }

        if (!empty($fields) && is_array($fields)) {
            if (!empty($settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas']) && is_array($settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas'])) {
                $settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas'] = array_filter($settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas']); // remove the "No field" empty value
            }
            foreach ($fields as $akey => $adata) {
                if (!empty($settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas']) && !in_array($akey, $settings['e_form_save_' . ($type ? $type . '_' : '') . 'metas']))
                    continue;
                /* if ($settings['e_form_save_anonymous'] && ($akey == 'ip_address' || $akey == 'referrer' || $akey == 'user_id'))
                  continue; */
                if ($settings['e_form_save_' . ($type ? $type . '_' : '') . 'array']) {
                    $afield = self::get_field($akey, $settings);
                    if ($afield) {
                        if (Form::is_multiple($afield)) {
                            $adata = Utils::explode($adata);
                        }
                    }
                }
                if ($type == 'option') {
                    $exist_opt = false;
                    if ($settings['e_form_save_' . ($type ? $type . '_' : '') . 'override'] == 'add') {
                        $exist_opt = get_option($akey);
                    }
                    if ($settings['e_form_save_' . ($type ? $type . '_' : '') . 'override'] == 'update' || !$exist_opt) {
                        update_option($akey, $adata);
                    }
                } else {
                    update_metadata($type, $obj_id, $akey, $adata);
                }
            }
        }
    }
    
    static public function get_form_id() {
        if (!empty($_REQUEST['form_id'])) {
            return sanitize_key($_REQUEST['form_id']);
        }
        return false;
    }

}
