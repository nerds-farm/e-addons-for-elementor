<?php

namespace EAddonsForElementor\Core\Traits;

use EAddonsForElementor\Core\Utils;

/**
 * @author francesco
 */
trait Pagination {
    
    
    public static function get_current_page_num() {
        return max(1, get_query_var('paged'), get_query_var('page'));
    }
    
    //
    public static function get_linkpage($i) {
        if (!is_singular() || is_front_page()) {
            return get_pagenum_link($i);
        }

        // Based on wp-includes/post-template.php:957 `_wp_link_page`.
        global $wp_rewrite;
        $id_page = get_the_ID();
        $post = get_post();
        $query_args = [];
        $url = get_permalink($id_page);

        if ($i > 1) {
            if ('' === get_option('permalink_structure') || in_array($post->post_status, ['draft', 'pending'])) {
                $url = add_query_arg('page', $i, $url);
            } elseif (get_option('show_on_front') === 'page' && (int) get_option('page_on_front') === $post->ID) {
                $url = trailingslashit($url) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
            } else {
                $url = trailingslashit($url) . user_trailingslashit($i, 'single_paged');
            }
        }

        if (is_preview()) {
            if (( 'draft' !== $post->post_status ) && isset($_GET['preview_id'], $_GET['preview_nonce'])) {
                $query_args['preview_id'] = wp_unslash($_GET['preview_id']);
                $query_args['preview_nonce'] = wp_unslash($_GET['preview_nonce']);
            }

            $url = get_preview_post_link($post, $query_args, $url);
        }
        
        if (!wp_doing_ajax() && !empty($_GET)) {
            foreach($_GET as $gkey => $gvalue) {
                $url = add_query_arg($gkey, $gvalue, $url);
            }
        }

        return $url;
    }
    
    static public function fix_ajax_pagination($content, $element, $fields = array()) {
        
        $pagination = false;
        if ($element) {
            switch ($element->get_name()) {
                case 'posts':
                case 'archive-posts':
                    //case 'wc-archive-products':
                    $pagination = $element->get_settings_for_display('pagination_type');
                    break;
                case 'e-query-posts':
                    $pagination = $element->get_settings_for_display('pagination_enable') || $element->get_settings_for_display('infiniteScroll_enable');
                    break;
            }
        }
        
        $nav_start = '<nav ';
        if ($pagination || strpos($content, $nav_start) !== false) { //'role="navigation"'
            //wp_json_encode($fields);
            $params = '';
            $form_id = \EAddonsForElementor\Core\Utils\Form::get_form_id();
            if ($form_id) {                
                $fields['form_id'] = $form_id;
                if (empty($fields['post_id']) && !empty($_POST['post_id'])) {
                    $fields['post_id'] = $_POST['post_id'];
                }
                if (empty($fields['queried_id']) && !empty($_POST['queried_id'])) {
                    $fields['queried_id'] = $_POST['queried_id'];
                }
                foreach ($fields as $fkey => $field) {
                    if ($field) {
                        if ($params) {
                            $params .= '&';
                        } else {
                            $params .= '?';
                        }
                        $field = Utils::to_string($field);
                        $params .= $fkey . '=' . urlencode($field);
                    }
                }
            }
            $current_url = Utils::get_current_url();
            $base_url = Utils::get_current_url(true);

            if (wp_doing_ajax()) {
                $current_url = admin_url('admin-ajax.php');                    
                if (empty($_POST['url'])) {
                    if (!empty($_POST['queried_id'])) {
                        $base_url = get_permalink($_POST['queried_id']);                    
                    }
                } else {
                    $base_url = esc_url_raw($_POST['url']);
                    //var_dump(Utils::get_current_page_num());
                    if (Utils::get_current_page_num() > 1) {
                        $base_url = remove_query_arg('page', $base_url);
                        $base_url = remove_query_arg('paged', $base_url);
                        
                        $tmpp = explode('?', $base_url);
                        $tmp = explode('/page/', $base_url);
                        if (count($tmp) > 1) {
                            $base_url = reset($tmp).'/';
                            if (count($tmpp) > 1) {
                                $base_url .= '?'.end($tmpp);
                            }
                        }
                    }
                }
                
                $content = str_replace($current_url . '/', $base_url, $content);
                $content = str_replace($current_url, $base_url, $content);
            }
            //var_dump($_POST);
            //var_dump('Current: '.$current_url);
            //var_dump('Base: '.$base_url);
            //die();

                
            $tmp = explode($nav_start, $content);
            //
            if (count($tmp) == 2) {
                $pre = reset($tmp);
                $nav = end($tmp);
                $base_href = 'href="' . $base_url;
                $current_href = 'href="' . $current_url;
                $tmp = explode($base_href, $nav); 
                $quote = '"';
                //var_dump($nav);
                if (count($tmp) == 1) {
                    $quote = "'";
                    $base_href = "href='" . $base_url;
                    $tmp = explode($base_href, $nav); 
                }
                if (count($tmp) > 1) {
                    $contentmp = '';
                    foreach ($tmp as $key => $href) {
                        if ($key) {
                            list($get, $other) = explode($quote, $href, 2);                                
                            if (strpos($get, 'form_id=') === false) {
                                if (strpos($get, '?') === false) {
                                    $contentmp .= $base_href . $get . $params . $quote . $other;
                                } else {                                        
                                    $contentmp .= $base_href . $get . '&' . ltrim($params, '?') . $quote . $other;
                                }
                            } else {
                                $contentmp .= $base_href . $href;
                            }
                        } else {
                            $contentmp = $href;
                        }
                    }
                    $content = $pre . $nav_start . $contentmp;
                }
            }
        }
        return $content;
    }

}
