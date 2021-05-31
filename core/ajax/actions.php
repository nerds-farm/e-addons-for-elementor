<?php

namespace EAddonsForElementor\Core\Ajax;

use EAddonsForElementor\Core\Utils;

/**
 * Ajax Actions
 *
 * @author fra
 */
class Actions {

    const ANY = 'any';
    const MIN_LENGHT = 2;

    public $actions = [];

    /*
      'options',
      'fields', // post, user, term, comment
      'custom_fields',
      'repeaters',
      'values', // post, user, term, comment
      'uploads', // dir, file
      'metas', // post, user, term, comment, attachment
      'posts', // type, meta, post_type__name, public, private, 'abc,efg,!xyz'
      'term_posts',
      'terms', // taxonomy, post_type
      'taxonomies',
      'users', // role, user_roles
      'authors',
      'acf',
      'jet',
     */

    public function __construct() {
        add_action('elementor/ajax/register_actions', [$this, 'elementor_ajax_register_actions']);

        // Ajax close banner notice
        add_action('wp_ajax_close_banner_notice_action', array($this, 'close_banner_notice_action'));
        //add_action('wp_ajax_nopriv_close_banner_notice_action', array($this, 'close_banner_notice_action'));

        $this->register_addons_actions();
    }

    public function close_banner_notice_action() {
        $data = $_POST;
        $time = time();
        if (!empty($data['unique_id'])) {
            $unique_id = sanitize_key($data['unique_id']);
            update_option('e_addons_notice_close_'.$unique_id, $time);
        }
        echo $time;
        die();
    }
    
    public function elementor_ajax_register_actions($ajax_manager) {
        $ajax_manager->register_ajax_action('e_query_control_options', [$this, 'get_control_options']);
        $ajax_manager->register_ajax_action('e_query_control_search', [$this, 'get_control_search']);
    }

    public function register_addons_actions() {
        $addons = \EAddonsForElementor\Plugin::instance()->get_plugins();
        foreach ($addons as $addon) {
            if ($addon['active']) {
                $e_query_actions = glob($addon['path'] . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'controls' . DIRECTORY_SEPARATOR . 'e-query' . DIRECTORY_SEPARATOR . '*.php');
                if (!empty($e_query_actions)) {
                    foreach ($e_query_actions as $action) {
                        $action_class = \EAddonsForElementor\Core\Helper::path_to_class($action);
                        $this->actions[] = new $action_class();
                    }
                }
            }
        }
    }

    /* public static function modal_action() {

      //echo \EAddonsForElementor\Core\Managers\Template::ajax_template();
      //wp_die();

      $post_id = Utils::get_id($_POST['post_href']);
      $template_id = 0;
      if (!empty($_POST['template_id']) && is_numeric($_POST['template_id'])) {
      $template_id = intval($_POST['template_id']);
      }

      $title_seo = get_post_meta($post_id, '_yoast_wpseo_title', true); //get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
      $title = (!empty($title_seo)) ? $title_seo : get_the_title($post_id) . ' - ' . get_bloginfo('name');



      $args = array('post_id' => $post_id, 'ajax' => true);
      $template = \EAddonsForElementor\Core\Managers\Template::e_template($template_id, $args);
      ?>
      <div class="content-p">
      <div class="title-p"><?php echo $title; ?></div>
      </div>
      <?php
      wp_die();
      } */

    /**
     * Calls function to get value titles depending on ajax query type
     *
     * @since  1.0.1
     * @return array
     */
    public function get_control_options($params, $control_options = []) {
        $method = '_get_' . $params['query_type'];
        if (method_exists($this, $method)) {
            $control_options = call_user_func([$this, $method], $params);
        }
        $control_options = apply_filters('e_addons/e_query/options/' . $params['query_type'], $control_options, $params);
        return $control_options;
    }

    public function get_control_search(array $params, $control_options = []) {
        if (empty($params['query_type']) || empty($params['q'])) {
            throw new \Exception('Bad Request');
        }
        $method = 'get_' . $params['query_type'];
        if (method_exists($this, $method)) {
            $control_options = call_user_func([$this, $method], $params);
        }
        $control_options = apply_filters('e_addons/e_query/search/' . $params['query_type'], $control_options, $params);
        return [
            'results' => $control_options,
        ];
    }

    public function _search(array $params, $format = '', $control_options = []) {
        if (empty($params['query_type'])) {
            return false;
        }
        if (!isset($params['q'])) {
            $params['q'] = NULL;
        }
        $method = 'get_' . $params['query_type'];
        if (method_exists($this, $method)) {
            $control_options = call_user_func([$this, $method], $params);
        }
        if ($format) {
            if (!empty($control_options)) {
                foreach ($control_options as $key => $aobj) {
                    $text = $aobj['text'];
                    $data = array();
                    switch($params['query_type']) {
                        case 'term_posts':
                        case 'posts':
                            $post = get_post($aobj['id']);
                            $data = array('post' => $post);
                            break;
                        case 'terms':
                            $term = Utils::get_term($aobj['id']);
                            $data = array('term' => $term);
                            break;
                        case 'users':
                            $user = get_user_by('ID', $aobj['id']);
                            $data = array('user' => $user);
                            break;
                        case 'values':
                        case 'metas':                            
                            $data = array('meta' => $text);                            
                            break;
                    }
                    $text = Utils::get_dynamic_data($format, $data);
                    if (empty($text) || $text == $format) {
                        $text = $aobj['text'];
                    }
                    //$text = '['.$aobj['id'].'] '.$text;
                    $control_options[$key]['text'] = $text;
                }
            }
        }
        $control_options = apply_filters('e_addons/e_query/search/' . $params['query_type'], $control_options, $params);
        return $control_options;
    }

    /*     * *********************************************************************** */

    public function get_options($params, $control_options = []) {
        $options = Utils::get_options($params['q']);
        if (!empty($options)) {
            foreach ($options as $okey => $oname) {
                $control_options[] = [
                    'id' => $okey,
                    'text' => $oname,
                ];
            }
        }
        return $control_options;
    }

    public function _get_options($params, $control_options = []) {
        $uid = (array) $params['id'];
        $options = Utils::get_options($uid);
        if (!empty($options)) {
            foreach ($options as $okey => $oname) {
                $control_options[] = [
                    'id' => $okey,
                    'text' => $oname,
                ];
            }
        }
        return $control_options;
    }
    
    public function get_comments($params, $control_options = []) {
        $args = array(); 
        if (is_numeric($params['q'])) {
            $args['ID'] = intval($params['q']);// args here 
        } else {
            $args['search'] = $params['q'];
        }
        $comments_query = new \WP_Comment_Query( $args ); 
        $comments = $comments_query->comments;
        if ( $comments ) { 
            foreach ( $comments as $comment ) { 
                $control_options[] = [
                    'id' => $comment->comment_ID,
                    'text' => '['.$comment->comment_ID.'] on '.$comment->comment_post_ID.' by '.$comment->comment_author,
                ];
            }
        }
        return $control_options;
    }

    public function _get_comments($params, $control_options = []) {
        $uid = (array) $params['id'];        
        foreach ( $uid as $comment_id ) {
            $comment = get_comment(intval($comment_id));
            if ($comment) {
                $control_options[] = [
                    'id' => $comment->comment_ID,
                    'text' => '['.$comment->comment_ID.'] on '.$comment->comment_post_ID.' by '.$comment->comment_author,
                ];
            }
        }
        return $control_options;
    }

    public function get_fields($params, $control_options = []) {
        if ($params['object_type'] == self::ANY) {
            $object_types = array('post', 'user', 'term', 'comment');
        } else {
            $object_types = array($params['object_type']);
        }
        foreach ($object_types as $object_type) {
            $fields = Utils::get_fields($object_type, $params['q']);
            if (!empty($fields)) {
                foreach ($fields as $field_key => $field_name) {
                    $control_options[] = [
                        'id' => $field_key,
                        'text' => ($params['object_type'] == self::ANY ? '[' . $object_type . '] ' : '') . $field_name,
                    ];
                }
            }
        }
        //$control_options = array_reverse($control_options);
        return $control_options;
    }

    public function _get_fields($params, $control_options = []) {
        $uid = (array) $params['id'];
        if ($params['object_type'] == self::ANY) {
            $object_types = array('post', 'user', 'term');
        } else {
            $object_types = array($params['object_type']);
        }
        foreach ($object_types as $object_type) {
            foreach ($uid as $aid) {
                $fields = Utils::get_fields($object_type, $aid);
                if (!empty($fields)) {

                    foreach ($fields as $field_key => $field_name) {
                        if (in_array($field_key, $uid)) {
                            $control_options[$field_key] = $field_name;
                        }
                    }
                }
            }
        }
        return $control_options;
    }

    public function get_taxonomies($params, $control_options = []) {
        $taxonomies = Utils::get_taxonomies(false, $params['q']);
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $field_key => $field_name) {
                if ($field_key) {
                    $control_options[] = [
                        'id' => $field_key,
                        'text' => $field_name,
                    ];
                }
            }
        }
        return $control_options;
    }

    public function _get_taxonomies($params, $control_options = []) {
        $uid = (array) $params['id'];
        foreach ($uid as $value) {
            $taxonomies = Utils::get_taxonomies(false, null, $value);
            if (!empty($taxonomies)) {
                foreach ($taxonomies as $field_key => $field_name) {
                    if ($field_key) {
                        $control_options[$field_key] = $field_name;
                    }
                }
            }
        }
        return $control_options;
    }

    public function get_uploads($params, $control_options = []) {
        $types = (!empty($params['object_type'])) ? $params['object_type'] : null;

        $upload_dir = wp_upload_dir();
        $uploads_basedir = $upload_dir['basedir'];
        switch ($types) {
            case 'folder':
                $contents = Utils::recursive_glob($uploads_basedir, '/*', GLOB_ONLYDIR);
                break;
            case 'file':
                $contents = Utils::recursive_glob($uploads_basedir, '/*', 'GLOB_ONLYFILE');
                break;
            default:
                $contents = Utils::recursive_glob($uploads_basedir, '/*');
                break;
        }

        if (!empty($contents)) {
            foreach ($contents as $akey => $acontent) {
                $file_path = str_replace($uploads_basedir . '/', '', $acontent);
                if (strlen($params['q']) > self::MIN_LENGHT) {
                    if (strpos($file_path, $params['q']) === false) {
                        continue;
                    }
                }
                $control_options[] = [
                    'id' => $file_path,
                    'text' => $file_path,
                ];
            }
        }
        return $control_options;
    }

    public function _get_uploads($params) {
        return $this->_get_files($params);
    }

    public function get_files($params, $control_options = []) {
        $root_dir = ABSPATH;
        $contents = glob($root_dir . $params['q'] . '*');

        if (!empty($contents)) {
            foreach ($contents as $akey => $acontent) {
                if (is_dir($acontent)) {
                    $acontent .= '/';
                }
                $file_path = str_replace($root_dir, '', $acontent);
                $control_options[] = [
                    'id' => $file_path,
                    'text' => $file_path,
                ];
            }
        }
        return $control_options;
    }

    public function _get_files($params, $control_options = []) {
        $uid = (array) $params['id'];
        foreach ($uid as $aid) {
            $control_options[$aid] = basename($aid);
        }
        return $control_options;
    }

    public function get_metas($params, $control_options = []) {        
        $fields = Utils::get_metas($params['object_type'], $params['q']);
        foreach ($fields as $field_key => $field_name) {
            if ($field_key) {
                $control_options[] = [
                    'id' => $field_key,
                    'text' => $field_name,
                ];
            }
        }
        return $control_options;
    }

    public function _get_metas($params, $control_options = []) {
        $uid = (array) $params['id'];        
        foreach ($uid as $aid) {
            $fields = Utils::get_metas($params['object_type'], $aid);
            foreach ($fields as $field_key => $field_name) {
                if (in_array($field_key, $uid)) {
                    $control_options[$field_key] = $field_name;
                }
            }
        }
        return $control_options;
    }
    
    public function get_values($params, $control_options = []) {
        global $wpdb;
        $table = $wpdb->prefix . $params['object_type'] . 'meta';
        //$query = 'SELECT meta_id, meta_value FROM ' . $table;
        $query = 'SELECT DISTINCT meta_value FROM ' . $table;
        $query .= " WHERE meta_key LIKE '" . $params['meta_key'] . "'";
        if (!empty($params['q'])) {
            $query .= " WHERE meta_value LIKE '%" . $params['q'] . "%'";
        }
        if ($params['object_type'] == 'post') {
            $query .= " AND post_id IN ( SELECT id FROM " . $wpdb->prefix . "posts WHERE post_status LIKE 'publish' )";
        }
        $results = $wpdb->get_results($query);
        if (!empty($results)) {
            /*$values = array();
            foreach ($results as $result) {
                if (!isset($values[$result->meta_value])) {
                    $values[$result->meta_value] = $result->meta_id;
                }
            }
            $flipped = array_flip($values);            
            foreach ($flipped as $id => $text) {
                    $control_options[] = [
                        'id' => $id,
                        'text' => $text,
                    ];
            }*/
            foreach ($results as $result) {
                    $control_options[] = [
                        'id' => $result->meta_value,
                        'text' => $result->meta_value,
                    ];
            }
        }
        return $control_options;
    }

    public function _get_values($params, $control_options = []) {
        $uid = (array) $params['id'];        
        foreach ($uid as $aid) {
            $control_options[$aid] = $aid;
        }
        return $control_options;
    }
    
    
    public function get_types($params, $control_options = []) {        
        $post_types = Utils::get_post_types();
        if (!empty($post_types)) {
            foreach ($post_types as $akey => $apt) {
                if (strlen($params['q']) > self::MIN_LENGHT) {
                    if (strpos($akey, $params['q']) === false && strpos($apt, $params['q']) === false) {
                        continue;
                    }
                }
                $control_options[] = [
                    'id' => $akey,
                    'text' => $apt,
                ];
            }
        }
        return $control_options;
    }

    public function get_posts($params, $control_options = []) {        
        $object_type = (!empty($params['object_type'])) ? $params['object_type'] : self::ANY;
        $post_type_public = null;
        $post_type_excluded = array();
        if (!empty($params['object_type']) && in_array($params['object_type'], array('public', 'private'))) {
            $objecy_type = self::ANY;
            $post_type_public = $params['object_type'] == 'public' ? true : false;
        }
        
        switch ($object_type) {
            case 'type':
                $control_options = $this->get_types($params);
                break;
            case 'meta':
                $fields = Utils::get_metas('post', $params['q']);
                foreach ($fields as $field_key => $field_name) {
                    if ($field_key) {
                        $control_options[] = [
                            'id' => $field_key,
                            'text' => $field_name,
                        ];
                    }
                }
                break;
            default:
                $object_type = Utils::explode($object_type);
                foreach($object_type as $okey => $otype) {
                    if (substr($otype, 0, 1) == '!') {
                        $post_type_excluded[] = substr($otype, 1);
                        unset($object_type[$okey]);
                    }
                }
                if (empty($object_type)) {
                    $object_type = self::ANY;
                }
                $query_params = [
                    'post_type' => $object_type,
                    's' => $params['q'],
                    'posts_per_page' => -1,
                ];
                if (!empty($params['author_id'])) {
                    $query_params['author__in'] = Utils::explode($params['author_id']);
                }
                if (!empty($params['post_parent'])) {
                    $query_params['post_parent'] = $params['post_parent'];
                }  
                if (!empty($params['orderby'])) {
                    $query_params['orderby'] = $params['orderby'];
                }  
                if (!empty($params['order'])) {
                    $query_params['order'] = $params['order'];
                }  
                if (!empty($params['post_parent'])) {
                    $query_params['post_parent'] = $params['post_parent'];
                }  
                if (!empty($params['tax_query'])) {
                    $terms = Utils::explode($params['tax_query']);
                    $term = Utils::get_term(reset($terms));
                    //var_dump($term);
                    if ($term && is_object($term) && get_class($term) == 'WP_Term') {
                        $query_params['tax_query'] = array(
                            array(
                                'taxonomy' => $term->taxonomy,
                                'terms'    => $terms,
                            ),
                        );
                    }
                }
                if (!wp_doing_ajax()) { //$object_type != 'any') {
                    $query_params['post_status'] = 'publish';
                }
                if ('attachment' === $query_params['post_type'] 
                    || (is_array($query_params['post_type']) && in_array('attachment', $query_params['post_type']))) {
                    $query_params['post_status'] = 'inherit';
                }
                if (!empty($params['post_status'])) {
                    $query_params['post_status'] = $params['post_status'];
                }    
                //var_dump($query_params); die();
                if (class_exists('EAddonsForElementor\Overrides\E_Query')) {
                    $query = new \EAddonsForElementor\Overrides\E_Query($query_params);
                } else {
                    $query = new \WP_Query($query_params);
                }
                foreach ($query->posts as $post) {
                    $post_title = $post->post_title;
                    if (empty($params['object_type']) || $object_type == self::ANY) {
                        $post_title = '[' . $post->ID . '] ' . $post_title . ' (' . $post->post_type . ')';
                    }
                    if (!empty($params['object_type']) && $object_type == 'elementor_library') {
                        $etype = get_post_meta($post->ID, '_elementor_template_type', true);
                        $post_title = '[' . $post->ID . '] ' . $post_title . ' (' . $post->post_type . ' > ' . $etype . ')';
                    }
                    
                    if (in_array($post->post_type, $post_type_excluded)) {
                        continue;
                    }

                    if ($post_type_public != null) {
                        $post_type = get_post_type_object($post->post_type);
                        if ($post_type->public != $post_type_public) {
                            continue;
                        }
                    }
                    $control_options[] = [
                        'id' => $post->ID,
                        'text' => $post_title,
                    ];
                }
        }
        return $control_options;
    }

    public function _get_posts($params, $control_options = []) {
        $uid = (array) $params['id'];        
        $is_txt = false;
        if (!empty($uid)) {
            if (is_array($uid)) {
                $first = reset($uid);
            } else {
                $first = $uid;
            }
            $is_txt = !is_numeric($first);
        }
        if ($is_txt) {            
            if (!empty($uid)) {
                // post type
                $post_types = Utils::get_post_types();
                foreach ($uid as $aid) {
                    if (isset($post_types[$aid])) {
                        $control_options[$aid] = $post_types[$aid];
                    }
                }
                // meta
                foreach ($uid as $aid) {
                    if (empty($control_options)) {
                        global $wpdb;
                        $sql = "SELECT post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '" . $aid . "' LIMIT 1";
                        if ($wpdb->get_var($sql)) {
                            $control_options[$aid] = $aid;
                        }
                    }
                }
            }
        } else {
            foreach ($uid as $akey => $aid) {
                $uid[$akey] = intval($aid);
            }
            $args = [
                'post_type' => self::ANY,
                'post_status' => self::ANY,
                'posts_per_page' => -1,
                'ignore_sticky_posts' => true,
                'post__in' => $uid,
            ];            
            if (class_exists('EAddonsForElementor\Overrides\E_Query')) {
                $the_query = new \EAddonsForElementor\Overrides\E_Query($args);
            } else {
                $the_query = new \WP_Query($args);
            }            
            //$the_query = new \WP_Query($args);
            foreach ($the_query->posts as $post) {
                $control_options[$post->ID] = $post->post_title;
            }
        }
        return $control_options;
    }

    public function get_terms($params, $control_options = []) {
        if (empty($params['object_type'])) {
            $taxonomies = get_object_taxonomies('');
        } else {
            $post_type = get_post_type_object($params['object_type']);
            if ($post_type) {
                $taxonomies = get_object_taxonomies($params['object_type']);
            } else {
                $taxonomies = $params['object_type'];
            }
        }
        $query_params = [
            'taxonomy' => $taxonomies,
            'search' => $params['q'],
            'hide_empty' => false,
        ];
        //$query_params = array_merge($query_params, $params);
        if (!empty($params['parent'])) {
           $query_params['parent'] = $params['parent'];
           $term = Utils::get_term($params['parent']);
           if ($term) {
                $query_params['taxonomy'] = $term->taxonomy;
           }
        }
        $terms = get_terms($query_params);
        foreach ($terms as $term) {
            if (is_object($term) && get_class($term) == 'WP_Term') {
                $term_name = $term->name;
                if (empty($params['object_type'])) {
                    $taxonomy = get_taxonomy($term->taxonomy);
                    if ($taxonomy) {
                        $label = (property_exists($taxonomy, 'labels')) ? $taxonomy->labels->singular_name : $taxonomy->label;
                        $term_name = $term_name . ' (' . $label . ')';
                    }
                }
                $control_options[] = [
                    'id' => $term->term_id,
                    'text' => $term_name,
                ];
            }
        }
        return $control_options;
    }

    public function _get_terms($params, $control_options = []) {
        $uid = (array) $params['id'];        
        $term_id = reset($uid);
        $query_params = array('hide_empty' => false);
        if (is_numeric($term_id)) {
            $query_params['include'] = $uid;
        } else {
            $query_params['slug'] = $uid;
        }
        $terms = get_terms($query_params);
        foreach ($terms as $term) {
            $control_options[$term->term_id] = $term->name;
        }
        return $control_options;
    }

    public function get_users($params, $control_options = []) {
        $object_type = (!empty($params['object_type'])) ? $params['object_type'] : false;
        if ($object_type == 'role') {
            $user_roles = Utils::get_user_roles();
            if (!empty($user_roles)) {
                foreach ($user_roles as $akey => $aur) {
                    if (strlen($params['q']) > self::MIN_LENGHT) {
                        if (strpos($akey, $params['q']) === false && strpos($aur, $params['q']) === false) {
                            continue;
                        }
                    }
                    $control_options[] = [
                        'id' => $akey,
                        'text' => $aur,
                    ];
                }
            }
        } else {
            $query_params = [
                'search' => '*' . $params['q'] . '*'
            ];
            if (!empty($params['object_type'])) {
                $query_params['role__in'] = Utils::explode($params['object_type']);
            }
            $users = get_users($query_params);
            foreach ($users as $user) {
                $control_options[] = [
                    'id' => $user->ID,
                    'text' => $user->display_name,
                ];
            }
        }
        return $control_options;
    }

    public function _get_users($params, $control_options = []) {
        $uid = (array) $params['id'];
        $is_role = false;
        if (!empty($uid)) {
            $first = reset($uid);
            $is_role = !is_numeric($first);
        }
        if ($is_role) {
            $roles = Utils::get_user_roles();
            if (!empty($uid)) {
                foreach ($uid as $aid) {
                    if (isset($roles[$aid])) {
                        $control_options[$aid] = $roles[$aid];
                    }
                }
            }
        } else {
            $query_params = [
                'fields' => [
                    'ID',
                    'display_name',
                ],
                'include' => $uid,
            ];
            $user_query = new \WP_User_Query($query_params);
            foreach ($user_query->get_results() as $user) {
                $control_options[$user->ID] = $user->display_name;
            }
        }
        return $control_options;
    }

    public function get_authors($params, $control_options = []) {
        $query_params = [
            'who' => 'authors',
            'has_published_posts' => true,
            'fields' => ['ID', 'display_name'],
            'search' => '*' . $params['q'] . '*',
            'search_columns' => ['user_login', 'user_nicename'],
        ];
        $user_query = new \WP_User_Query($query_params);
        foreach ($user_query->get_results() as $author) {
            $control_options[] = [
                'id' => $author->ID,
                'text' => $author->display_name,
            ];
        }
        return $control_options;
    }

    public function _get_authors($params, $control_options = []) {
        $uid = (array) $params['id'];
        $query_params = [
            'who' => 'authors',
            'has_published_posts' => true,
            'fields' => ['ID', 'display_name'],
            'include' => $uid,
        ];
        $user_query = new \WP_User_Query($query_params);
        foreach ($user_query->get_results() as $author) {
            $control_options[$author->ID] = $author->display_name;
        }
        return $control_options;
    }

    public function get_term_posts($params, $control_options = []) {
        $term = Utils::get_term($params['object_type']);
        $query_params = [
            'post_type' => self::ANY,
            'tax_query' => array(
                array(
                    'taxonomy' => $term->taxonomy,
                    'terms' => $term->term_id,
                ),
            ),
            's' => $params['q'],
            'posts_per_page' => -1,
        ];
        $query = new \WP_Query($query_params);
        foreach ($query->posts as $post) {
            $post_title = $post->post_title;
            $post_title = '[' . $post->ID . '] ' . $post_title . ' (' . $post->post_type . ')';
            if ($post->taxonomy == 'elementor_library') {
                $etype = get_post_meta($post->ID, '_elementor_template_type', true);
                $post_title = '[' . $post->ID . '] ' . $post->post_title . ' (' . $post->post_type . ' > ' . $etype . ')';
            }
            $control_options[] = [
                'id' => $post->ID,
                'text' => $post_title,
            ];
        }
        return $control_options;
    }

    public function _get_term_posts($params) {        
        return $this->_get_posts($params);
    }
    
    public function get_author_posts($params, $control_options = []) {
        $author_id = intval($params['object_type']);
        $query_params = [
            'post_type' => self::ANY,
            'author_id' => $author_id,
            's' => $params['q'],
            'posts_per_page' => -1,
        ];
        $query = new \WP_Query($query_params);
        foreach ($query->posts as $post) {
            $post_title = $post->post_title;
            $post_title = '[' . $post->ID . '] ' . $post_title . ' (' . $post->post_type . ')';
            if ($post->taxonomy == 'elementor_library') {
                $etype = get_post_meta($post->ID, '_elementor_template_type', true);
                $post_title = '[' . $post->ID . '] ' . $post->post_title . ' (' . $post->post_type . ' > ' . $etype . ')';
            }
            $control_options[] = [
                'id' => $post->ID,
                'text' => $post_title,
            ];
        }
        return $control_options;
    }

    public function _get_author_posts($params) {        
        return $this->_get_posts($params);
    }
    
    
    public function get_repeaters($params, $control_options = []) { 
        $params['object_type'] = 'repeater';
        if (defined('ACF_PRO')) {
            foreach(self::get_acf($params) as $acf) {
                $control_options[] = $acf;
            }
        }
        if (class_exists( 'Jet_Engine' )) {
            foreach(self::get_jet($params) as $jet) {
                $control_options[] = $jet;
            }
        }
        return $control_options;
    }
    
    public function _get_repeaters($params, $control_options = []) { 
        //$params['object_type'] = 'repeater';
        if (defined('ACF_PRO')) {
            $control_options += self::_get_acf($params);
        }
        if (class_exists( 'Jet_Engine' )) {
            $control_options += self::_get_jet($params);
        }
        return $control_options;
    }
    
    public function get_custom_fields($params, $control_options = []) { 
        if (defined('ACF_PRO')) {
            foreach(self::get_acf($params) as $acf) {
                $control_options[] = $acf;
            }
        }
        if (class_exists( 'Jet_Engine' )) {
            foreach(self::get_jet($params) as $jet) {
                $control_options[] = $jet;
            }
        }
        return $control_options;
    }
    
    public function _get_custom_fields($params, $control_options = []) { 
        if (defined('ACF_PRO')) {
            $control_options += self::_get_acf($params);
        }
        if (class_exists( 'Jet_Engine' )) {
            $control_options += self::_get_jet($params);
        }
        return $control_options;
    }
    
    public function get_acf($params, $control_options = []) {        
        $types = (!empty($params['object_type'])) ? $params['object_type'] : array();
        $acfs = \EAddonsForElementor\Core\Utils\Acf::get_acf_fields($types);
        if (!empty($acfs)) {
            foreach ($acfs as $akey => $acf) {
                if (strlen($params['q']) > Actions::MIN_LENGHT) {
                    if (strpos($akey, $params['q']) === false && strpos($acf, $params['q']) === false) {
                        continue;
                    }
                }
                $control_options[] = [
                    'id' => $akey,
                    'text' => $acf,
                ];
            }
        }
        return $control_options;
    }

    public function _get_acf($params, $control_options = []) {
        $uid = (array) $params['id'];
        foreach ($uid as $aid) {
            $acf = acf_get_field_post($aid);
            if ($acf) {
                $control_options[$aid] = $acf->post_title;
            }
        }
        return $control_options;
    }
    
    public function get_jet($params, $control_options = []) {        
        $types = (!empty($params['object_type'])) ? $params['object_type'] : array();
        $jets = \EAddonsForElementor\Core\Utils\Jet::get_jet_fields($types);
        if (!empty($jets)) {
            foreach ($jets as $akey => $ajet) {
                if (strlen($params['q']) > Actions::MIN_LENGHT) {
                    if (strpos($akey, $params['q']) === false && strpos($ajet, $params['q']) === false) {
                        continue;
                    }
                }
                $control_options[] = [
                    'id' => $akey,
                    'text' => $ajet,
                ];
            }
        }
        return $control_options;
    }

    public function _get_jet($params, $control_options = []) {
        $uid = (array) $params['id'];
        foreach ($uid as $aid) {
            $title = \EAddonsForElementor\Core\Utils\Jet::get_jet_field($aid);
            if (!empty($title)) {
                if (is_array($title) && !empty($title['title'])) {
                    $title = $title['title'];
                }
                $control_options[$aid] = $title;
            }
        }
        return $control_options;
    }

}
