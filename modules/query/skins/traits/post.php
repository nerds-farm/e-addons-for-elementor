<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

use EAddonsForElementor\Core\Utils;

/**
 * Description of Post
 *
 * @author fra
 */
trait Post {

    protected function render_item_author($settings) {
        // Settings ------------------------------
        $avatar_image_size = $settings['author_image_size'];
        $use_link = $this->get_item_link($settings);
        $blanklink  = $settings['blanklink_enable'];

        $author_user_key = array();
        if (count($settings['author_user_key']))
            $author_user_key = $settings['author_user_key'];
        // ---------------------------------------
        if ($settings['author_image'])
            array_push($author_user_key, 'avatar');
        if ($settings['author_displayname'])
            array_push($author_user_key, 'display_name');
        if ($settings['author_bio'])
            array_push($author_user_key, 'description');
        // ---------------------------------------
        $author = [];

        $avatar_args['size'] = $avatar_image_size;

        $user_id = get_the_author_meta('ID');
        $author['avatar'] = get_avatar_url($user_id, $avatar_args);
        $author['posts_url'] = get_author_posts_url($user_id);

        //<a href="' . $author['posts_url'] . '">' . '
        if (!empty($author_user_key)) {
            echo '<div class="e-add-post-author">';
            ?>
            <div class="e-add-author-image">
            <?php
            foreach ($author_user_key as $akey => $author_value) {

                if ($author_value == 'avatar') {
                    ?>
                        <div class="e-add-author-avatar">
                            <img class="e-add-img" src="<?php echo $author['avatar']; ?>" alt="<?php echo get_the_author_meta('display_name'); ?>" />
                        </div>
                    <?php
                    }
                }
                ?>
            </div>
            <div class="e-add-author-text">
                <?php
                foreach ($author_user_key as $akey => $author_value) {
                    if ($author_value != 'avatar') {
                        echo '<div class="e-add-author-' . $author_value . '">' . get_the_author_meta($author_value) . '</div>';
                    }
                }
                ?>
            </div>
            <?php
            echo '</div>';
        }
    }

    protected function render_item_content($settings, $excerpt = false) {
        // Settings ------------------------------
        $textcontent_limit = $settings['textcontent_limit'];
        //
        $querytype = $this->parent->get_querytype();
        //
        $use_link = $settings['use_link']; //@p questo qui non mi serve
        // ---------------------------------------
        echo '<div class="e-add-post-content">';
        // Content
        switch ($querytype) {
            case 'attachment':
                $content = $this->current_data->post_content;
                if($content){
                    if ($textcontent_limit) {
                        echo substr(wp_strip_all_tags($content), 0, $textcontent_limit) . ' ...'; //
                    } else {
                        echo $content;
                    }
                }
                break;
            case 'post':
                //$content_type = $settings['content_type'];
                //
                // Excerpt
                if ($excerpt) {
                    $post = get_post();
                    $post_excerpt = $post->post_excerpt;
                    if ($textcontent_limit) {
                        $post_excerpt = $this->limit_text($post_excerpt, $textcontent_limit);
                    }
                    echo $post_excerpt; //$this->limit_excerpt( $settings['textcontent_limit'] ); //
        
                    /*
                      // Da valutare se fare così...
                      add_filter( 'excerpt_more', [ $this, 'filter_excerpt_more' ], 20 );
                      add_filter( 'excerpt_length', [ $this, 'filter_excerpt_length' ], 20 );
        
                      ?>
        
                      <?php the_excerpt(); ?>
        
                      <?php
        
                      remove_filter( 'excerpt_length', [ $this, 'filter_excerpt_length' ], 20 );
                      remove_filter( 'excerpt_more', [ $this, 'filter_excerpt_more' ], 20 );
                     */
                } else {
                    // CONTENT
                    if ($textcontent_limit) {
                        echo $this->limit_content($textcontent_limit);
                    } else {
                        echo wpautop(get_the_content());
                    }
                }
                break;
        }
        echo '</div>';
    }

    protected function render_item_posttype($settings) {
        $posttype_label = $settings['posttype_label'];
        $type = get_post_type();
        //
        switch ($posttype_label) {
            case 'plural' :
                $posttype = get_post_type_object($type)->labels->name;
                break;
            case 'singular' :
            default:
                $posttype = get_post_type_object($type)->labels->singular_name;
                break;
        }
        if ( !empty($posttype) ){
            //@p label before
            echo $this->render_label_before_item($settings,'Type: ');
            echo '<div class="e-add-post-ptype">';
                echo $posttype;
            echo '</div>';           
            echo $this->render_label_after_item($settings);
        }
    }

    protected function render_item_termstaxonomy($settings) {
        // Settings ------------------------------
        $taxonomy_filter = $settings['taxonomy_filter'];
        $separator_chart = $settings['separator_chart'];
        $only_parent_terms = $settings['only_parent_terms'];
        $block_enable = $settings['block_enable']; //style
        $icon_enable = $settings['icon_enable'];
        //
        $use_link = $settings['use_link'];
        // ---------------------------------------

        $term_list = [];


        $taxonomy = get_post_taxonomies($this->current_id);

        echo '<div class="e-add-post-terms">';
        // ------- Ciclo le taxonomy in automatico
        foreach ($taxonomy as $tax) {

            // @p se $taxonomy_filter è valorizzato filtro solo le taxonomy scelte
            if ( !empty($taxonomy_filter) ) {
                if (!in_array($tax, $taxonomy_filter)) {
                    continue;
                }
            }
            // ...da migliorarre...
            if ($tax != 'post_format') {

                $term_list = Utils::get_post_terms($this->current_id, $tax);
                if ($term_list && is_array($term_list) && count($term_list) > 0) {
                    
                    //if($cont == 1){
                        // @p La label
                        echo $this->render_label_before_item($settings, get_taxonomy($tax)->labels->name.': ');

                        if ($icon_enable) {
                            // @p l'icona
                            $icon = '';
                            if (is_taxonomy_hierarchical($tax)) {
                                $icon = '<i class="e-add-icon e-add-query-icon far fa-folder-open" aria-hidden="true"></i> ';
                            } else {
                                $icon = '<i class="e-add-icon e-add-query-icon far fa-tags" aria-hidden="true"></i> ';
                            }
                            echo $icon;
                        }
                    //}

                    echo '<ul class="e-add-terms-list e-add-taxonomy-' . $tax . '">';

                    // ------- Ciclo i termini
                    $cont = 1;
                    $divider = '';
                    foreach ($term_list as $term) {

                        if (!empty($only_parent_terms)) {
                            if ($only_parent_terms == 'yes') {
                                if ($term->parent)
                                    continue;
                            }
                            if ($only_parent_terms == 'children') {
                                if (!$term->parent)
                                    continue;
                            }
                        }
                        
                        //@p il link del termine
                        $term_url = trailingslashit(get_term_link($term));

                        $linkOpen = '';
                        $linkClose = '';

                        if ($use_link) {
                            if ($use_link != 'yes') {
                                $term_url = $this->get_item_link($settings);
                            }
                            $linkOpen = '<a class="e-add-link" href="' . $term_url . '"'.( !empty($settings['blanklink_enable']) ? ' target="_blank"' : '').'>';
                            $linkClose = '</a>';
                        }
                        //@p il divisore in caso di inline
                        if ($cont > 1 && !$block_enable) {
                            $divider = '<span class="e-add-separator">' . $separator_chart . '</span>';
                        }
                        //@ stampo il termine
                        echo '<li class="e-add-term-item">';
                        echo $divider . '<span class="e-add-term e-add-term-' . $term->term_id . '" data-e-add-order="' . $term->term_order . '">' . $linkOpen . $term->name . $linkClose . '</span>';
                        echo '</li>';
                        //
                        $cont++;
                    } //end foreach terms
                    echo '</ul>';
                    echo $this->render_label_after_item($settings);
                    
                } //end if termslist
            } //end exclusion
        } //end foreach taxonomy	

        echo '</div>';
    }

}
