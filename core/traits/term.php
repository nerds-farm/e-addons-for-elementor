<?php

namespace EAddonsForElementor\Core\Traits;

/**
 * Description of Term
 *
 * @author fra
 */
trait Term {
    
    public static function get_taxonomies($post_type = '', $filter = '', $exclude = true) {
        $custom_taxonomies = get_taxonomies(array(), 'objects');
        $taxonomies = [];
        if (!$post_type) {
            $taxonomies['category'] = 'Categories Post (category)';
            $taxonomies['post_tag'] = 'Tags Post (post_tag)';
        }
        if ($custom_taxonomies) {
            foreach ($custom_taxonomies as $taxonomy) {
                if ($exclude && in_array($taxonomy->name, array('elementor_library_type','elementor_library_category', 'elementor_font_type', 'nav_menu', 'link_category'))) {
                    continue;
                }
                if (!$post_type || in_array($post_type, $taxonomy->object_type)) {
                    $taxonomies[$taxonomy->name] = $taxonomy->label . ' (' . $taxonomy->name . ')';
                }
            }
        }

        if (!empty($filter)) {
            $tmp = array();
            foreach ($taxonomies as $tkey => $atax) {
                if (stripos($tkey, $filter) !== false || stripos($atax, $filter) !== false) {
                    $tmp[$tkey] = $atax;
                }
            }
            $taxonomies = $tmp;
        }

        return $taxonomies;
    }

    public static function get_taxonomy_terms($taxonomy = null, $search = '', $info = true) {
        $terms = [];        
        $taxonomies = ($taxonomy) ? array($taxonomy => $taxonomy) : self::get_taxonomies();
        if (!empty($taxonomies)) {
            $args = array('hide_empty' => false);
            if ($search) {
                $args['name__like'] = $search;
            }
            foreach ($taxonomies as $tkey => $atax) {
                $args['taxonomy'] = $tkey;
                $db_terms = get_terms($args);
                if (!empty($db_terms) && !is_wp_error($db_terms)) {
                    foreach ($db_terms as $aterm) {
                        $term_name = $aterm->name;
                        if ($info) {
                            $term_name = $atax . ' > ' . $term_name . ' (' . $aterm->slug . ')';
                        }
                        $terms[$aterm->term_id] = $term_name;
                    }
                }
            }
        }
        return $terms;
    }

    public static function get_term_posts($term_id, $post_type = 'any') {
        $term = get_term_by('id', $term_id);
        if ($term) {
            return get_posts(array(
                'post_type' => $post_type,
                'numberposts' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => $term->taxonomy,
                        'field' => 'id',
                        'terms' => $term_id,
                        'include_children' => false
                    )
                )
            ));
        }
        return false;
    }

    public static function get_term_fields($filter = false, $info = true) {
        $term = get_term_by('id', 1, 'category');
        return self::get_fields($term, $filter, $info);
    }

    public static function get_term($term_id = false, $taxonomy = 'category') {
        $term = false;
        if (is_object($term_id)) {
            if (get_class($term_id) == 'WP_Term') {
                return $term_id;
            } else {
                return false;
            }
        }
        if (is_numeric($term_id)) {
            $term_id = intval($term_id);
            $term = get_term($term_id);
        }
        if (is_string($term_id)) {
            $term = get_term_by('slug', $term_id, $taxonomy);
            if (!$term) {
                $term = get_term_by('name', $term_id, $taxonomy);
            }
        }
        if (!$term_id && get_the_ID()) {
            $post_type = get_post_type();                
            if ($post_type && $post_type != 'post') {
                $taxonomies = get_post_taxonomies();
                if (!empty($taxonomies)) {
                    if (!in_array($taxonomy, $taxonomies)) {
                        $taxonomy = reset($taxonomies);
                    }
                }
            }
            $terms = get_the_terms(get_the_ID(), $taxonomy);
            if (!empty($terms)) {
                $term = reset($terms);
                return $term;
            }
        }
        return $term;
    }
    
    public static function get_term_id($taxonomy = 'category') {
        $term = self::get_term(false, $taxonomy);
        if ($term) {
            return $term->term_id;
        }
    }

    public static function get_term_taxonomy($term_id) {
        $term = self::get_term($term_id);
        if ($term) {
            return $term->taxonomy;
        }
        return false;
    }

    public static function get_term_field($field = 'name', $term = null, $single = null) {      
        $value = null;
        if (is_numeric($field) && intval($field)) {
            $tmp = $term;
            $term = $field;
            $field = $tmp;
        }
        $term = self::get_term($term);
        if ($term) {
            if (in_array($field, array('permalink', 'get_permalink', 'get_term_link', 'term_link'))) {
                $value = get_term_link($term);
            }
            if ($value == null) {
                $value = self::get_wp_object_field($term, $field, $single);
            }        
        }
        return self::adjust_data($value, $single);
    }
    
    public static function get_term_url($id = null, $taxonomy = '') {
        if (!$taxonomy) {
            $taxonomy = self::get_term_taxonomy($id);
        }
        return get_term_link($id, $taxonomy);
    }
}
