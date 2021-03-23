<?php

namespace EAddonsForElementor\Base;

use Elementor\Element_Base;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Base_Skin extends \Elementor\Skin_Base {

    use \EAddonsForElementor\Base\Traits\Base;

    /**
     * Skin base constructor.
     *
     * Initializing the skin base class by setting parent widget and registering
     * controls actions.
     *
     * @since 1.0.0
     * @access public
     * @param Widget_Base $parent
     */
    public function __construct($parent = []) {
        if (!empty($parent)) {
            parent::__construct($parent);
        }
    }

    public function get_id() {
        return 'e-skin';
    }

    public function _enqueue_scripts() {
        $scripts = $this->get_script_depends();
        if (!empty($scripts)) {
            wp_enqueue_script('elementor-frontend');
            foreach ($scripts as $script) {
                wp_enqueue_script($script);
            }
        }
    }

    public function _enqueue_styles() {
        $styles = $this->get_style_depends();
        if (!empty($styles)) {
            foreach ($styles as $style) {
                wp_enqueue_style($style);
            }
        }
    }

    public function _print_styles() {
        $styles = $this->get_style_depends();
        if (!empty($styles)) {
            foreach ($styles as $style) {
                wp_print_styles(array($style));
            }
        }
    }

    public function _print_scripts() {
        $scripts = $this->get_script_depends();
        if (!empty($scripts)) {
            foreach ($scripts as $script) {
                wp_print_scripts(array($script));
            }
        }
    }

    public function preview_enqueue() {
        // @p Se mi trovo in editor li sparo tutti
        $this->_enqueue_styles();
        $this->_enqueue_scripts();
    }

    public function enqueue() {
        // @p Se mi trovo in frontend uso solo quelli che servono in base allo skin
        if (!empty($this->parent->get_settings('_skin')) && $this->get_id() == $this->parent->get_settings('_skin')) {
            $this->_enqueue_styles();
            $this->_enqueue_scripts();
        }
    }

    public function print_assets() {
        $this->_print_styles();
        $this->_print_scripts();
    }

    public function _register_controls_actions() {
        add_action('elementor/element/posts/section_layout/before_section_end', [$this, 'register_controls']);
        add_action('elementor/element/posts/section_query/after_section_end', [$this, 'register_style_sections']);
    }

    public function render() {

    }

    /* ELEMENTOR PRO © - PORTFOLIO - Protected function */

    protected function render_thumbnail() {
        $settings = $this->parent->get_settings();

        $settings['skin_thumbnail_size'] = [
            'id' => get_post_thumbnail_id(),
        ];

        $thumbnail_html = Group_Control_Image_Size::get_attachment_image_html($settings, 'skin_thumbnail_size');
        ?>
        <div class="elementor-portfolio-item__img elementor-post__thumbnail">
            <?php echo $thumbnail_html; ?>
        </div>
        <?php
    }

    protected function render_title() {
        if (!$this->parent->get_settings('show_title')) {
            return;
        }

        $tag = $this->parent->get_settings('title_tag');
        ?>
        <<?php echo $tag; ?> class="elementor-portfolio-item__title">
        <?php the_title(); ?>
        </<?php echo $tag; ?>>
        <?php
    }

    protected function get_posts_tags() {
        $taxonomy = $this->parent->get_settings('taxonomy');

        foreach ($this->parent->get_query()->posts as $post) {
            if (!$taxonomy) {
                $post->tags = [];

                continue;
            }

            $tags = wp_get_post_terms($post->ID, $taxonomy);

            $tags_slugs = [];

            foreach ($tags as $tag) {
                $tags_slugs[$tag->term_id] = $tag;
            }

            $post->tags = $tags_slugs;
        }
    }

    protected function render_loop_header() {
        if ($this->parent->get_settings('show_filter_bar')) {
            $this->render_filter_menu();
        }
        ?>
        <div class="elementor-portfolio elementor-grid elementor-posts-container">
            <?php
        }

        protected function render_filter_menu() {
            $taxonomy = $this->parent->get_settings('taxonomy');

            if (!$taxonomy) {
                return;
            }

            $terms = [];

            foreach ($this->parent->get_query()->posts as $post) {
                $terms += $post->tags;
            }

            if (empty($terms)) {
                return;
            }

            usort($terms, function ($a, $b) {
                return strcmp($a->name, $b->name);
            });
            ?>
            <ul class="elementor-portfolio__filters">
                <li class="elementor-portfolio__filter elementor-active" data-filter="__all"><?php echo __('All', 'elementor-pro'); ?></li>
                <?php foreach ($terms as $term) { ?>
                    <li class="elementor-portfolio__filter" data-filter="<?php echo esc_attr($term->term_id); ?>"><?php echo $term->name; ?></li>
                <?php } ?>
            </ul>
            <?php
        }

        protected function render_post_header($post_id = 0) {
            global $post;

            if (!$post) {
                $post = get_post($post_id);
            }

            $tags_classes = array();
            if ($post->tags) {
                $tags_classes = array_map(function ($tag) {
                    return 'elementor-filter-' . $tag->term_id;
                }, $post->tags);
            }

            $widget_name = $this->parent->get_name();

            $classes = [
                'elementor-' . $widget_name . '-item',
                'elementor-post',
                implode(' ', $tags_classes),
            ];
            ?>
            <article <?php post_class($classes); ?>>
                <?php
            }

            protected function render_post_footer() {
                ?>
            </article>
            <?php
        }

        protected function render_overlay_header($link = false) {
            ?>
            <a <?php if ($link) { ?> style="position: absolute; top: 0;height: 100%;z-index: 10;"<?php } ?> class="elementor-post__thumbnail__link" href="<?php echo get_permalink(); ?>">
                <div class="elementor-portfolio-item__overlay">
                    <?php
                }

                protected function render_overlay_footer() {
                    ?>
                </div></a>
            <?php
        }

        protected function render_loop_footer() {
            ?>
        </div>
        <?php
    }

}
