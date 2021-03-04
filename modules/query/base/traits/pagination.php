<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

/**
 * Description of infinite-scroll
 *
 * @author fra
 */
trait Pagination {

    public function paginations_enable_controls() {
        // +********************* Pagination ()
        $this->add_control(
                'pagination_enable', [
            'label' => '<i class="eaddicon eicon-post-navigation" aria-hidden="true"></i> ' . __('Pagination', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                //@p il massimo è che la paginazione funzioni con tutti gli skins...
                '_skin' => ['', 'grid', 'carousel', 'filters', 'justifiedgrid', 'gridtofullscreen3d', 'table'],
                'infiniteScroll_enable' => '',
                'query_type' => ['automatic_mode', 'get_cpt', 'get_tax', 'get_users_and_roles', 'get_attachments']
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_enable', [
            'label' => '<i class="eaddicon eicon-navigation-horizontal" aria-hidden="true"></i> ' . __('Infinite Scroll', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'frontend_available' => true,
            'condition' => [
                '_skin' => ['', 'grid', 'filters', 'gridtofullscreen3d', 'table'],
                'pagination_enable!' => ''
            ],
                ]
        );
    }

    protected function add_pagination_section() {
        // ------------------------------------------------------------------ [SECTION PAGINATION]
        $this->start_controls_section(
                'section_pagination', [
            'label' => '<i class="eaddicon eicon-post-navigation" aria-hidden="true"></i> ' . __('Pagination', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'pagination_enable' => 'yes',
                'infiniteScroll_enable' => ''
            ],
                ]
        );
        $this->add_control(
                'pagination_show_numbers', [
            'label' => __('Show Numbers', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
                ]
        );
        $this->add_control(
                'pagination_range', [
            'label' => __('Range of numbers', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '4',
            'condition' => [
                'pagination_show_numbers!' => '',
            ]
                ]
        );
        // Prev/Next
        $this->add_control(
                'pagination_show_prevnext', [
            'label' => __('Show Prev/Next', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'pagination_icon_prevnext',
                [
                    'label' => __('Icon', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fa fa-arrow-right',
                        'library' => 'fa-solid',
                    ],
                    'skin' => 'inline',
                    'label_block' => false,
                    'recommended' => [
                        'fa-solid' => [
                            'arrow-right',
                            'chevron-right',
                            'angle-right',
                            'chevron-circle-right',
                            'angle-double-right',
                            'caret-right',
                            'caret-square-right',
                            'hand-point-right',
                            'arrow-circle-right',
                            'arrow-alt-circle-right',
                            'long-arrow-alt-right'
                        ],
                        'fa-regular' => [
                            'caret-square-right',
                            'hand-point-right',
                            'arrow-alt-circle-right',
                        ],
                    ],
                    'fa4compatibility' => 'icon',
                    'condition' => [
                        'pagination_show_prevnext' => 'yes',
                    ],
                ]
        );
        $this->add_control(
                'pagination_prev_label', [
            'label' => __('Previous Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Previous', 'e-addons'),
            'condition' => [
                'pagination_show_prevnext' => 'yes',
            ],
                ]
        );
        $this->add_control(
                'pagination_next_label', [
            'label' => __('Next Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Next', 'e-addons'),
            'condition' => [
                'pagination_show_prevnext' => 'yes',
            ],
                ]
        );
        // first/last
        $this->add_control(
                'pagination_show_firstlast', [
            'label' => __('Show First/Last', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before'
                ]
        );
        $this->add_control(
                'pagination_icon_firstlast',
                [
                    'label' => __('Icon', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fa fa-arrow-right',
                        'library' => 'fa-solid',
                    ],
                    'skin' => 'inline',
                    'label_block' => false,
                    'recommended' => [
                        'fa-solid' => [
                            'arrow-right',
                            'chevron-right',
                            'angle-right',
                            'chevron-circle-right',
                            'angle-double-right',
                            'caret-right',
                            'caret-square-right',
                            'hand-point-right',
                            'arrow-circle-right',
                            'arrow-alt-circle-right',
                            'long-arrow-alt-right'
                        ],
                        'fa-regular' => [
                            'caret-square-right',
                            'hand-point-right',
                            'arrow-alt-circle-right',
                        ],
                    ],
                    'fa4compatibility' => 'icon',
                    'condition' => [
                        'pagination_show_firstlast!' => '',
                    ],
                ]
        );
        $this->add_control(
                'pagination_first_label', [
            'label' => __('First Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => __('First', 'e-addons'),
            'condition' => [
                'pagination_show_firstlast!' => '',
            ],
                ]
        );
        $this->add_control(
                'pagination_last_label', [
            'label' => __('Last Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Last', 'e-addons'),
            'condition' => [
                'pagination_show_firstlast!' => '',
            ],
                ]
        );
        $this->add_control(
                'pagination_show_progression', [
            'label' => __('Show Progression', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before'
                ]
        );
        $this->end_controls_section();
    }

    /// questo è il valore di paged
    public function get_current_page() {
        if ('' === $this->get_settings_for_display('pagination_enable')) {
            return 1;
        }

        return max(1, get_query_var('paged'), get_query_var('page'));
    }

    ///
    public function get_next_pagination() {
        //global $paged;
        $paged = $this->get_current_page(); //max(1, get_query_var('paged'), get_query_var('page'));

        if (empty($paged))
            $paged = 1;

        $link_next = self::get_linkpage($paged + 1);

        return $link_next;
    }

    ///
    public function render_pagination() {
        //@p qui renderizzo la paginazione se abilitata
        // ....
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        //$this->get_instance_value('pagination_enable');
        //var_dump($settings);
        // Numeric pagination -----------------------------------------------
        if (!empty($settings['pagination_enable'])) {
            $query = $this->get_query();
            $querytype = $this->get_querytype();

            switch ($querytype) {
                case 'attachment':
                case 'post':
                    $page_limit = $query->max_num_pages;
                    break;
                case 'user':
                    $no = $settings['users_per_page'];
                    $total_user = $query->total_users;
                    $page_limit = $no ? ceil($total_user / $no) : 1;
                    break;
                case 'term':
                    $no = $settings['terms_per_page'];

                    $args = $query->query_vars;
                    if (!empty($args['number'])) {
                        unset($args['number']);
                        $term_query_totals = new \WP_Term_Query($args);
                        $total_term = count($term_query_totals->get_terms());
                    } else {
                        $total_term = count($query->get_terms());
                    }
                    $page_limit = $no ? ceil($total_term / $no) : 1;
                    break;
            }
            $this->numeric_query_pagination($page_limit, $settings);
        }

        // Infinite scroll pagination -----------------------------------------------
        // @p ..infiniteScroll è abilitato e anche se i post generati sono maggiori dei post visualizzati..
        if (!empty($settings['infiniteScroll_enable'])) {
            $query = $this->get_query();
            $querytype = $this->get_querytype();


            switch ($querytype) {
                case 'post':
                    $page_limit = $query->max_num_pages;
                    //
                    $page_length = $query->post_count;
                    $per_page = $settings['posts_per_page'];
                    break;
                case 'user':
                    $no = $settings['users_per_page'];
                    $total_user = $query->total_users;
                    $page_limit = ceil($total_user / $no);
                    //
                    $page_length = $query->total_users;
                    $per_page = $settings['users_per_page'];
                    break;
                case 'term':
                    //var_dump(count($query));
                    /* $no = $settings['terms_per_page'];
                      $total_term = $query->count; */
                    $page_limit = count($query->get_terms()); //ceil($total_term/$no);
                    //
                    $page_length = count($query->get_terms());
                    $per_page = $settings['terms_per_page'];
                    break;
            }

            if (( $page_length >= $per_page && $per_page >= 0) ||
                    \Elementor\Plugin::$instance->editor->is_edit_mode()
            ) {
                // previewmode è una versione utile mentre mi trovo in editor per vedere la situazione degli status
                $preview_mode = '';
                if (\Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['infiniteScroll_show_preview']) {
                    $preview_mode = ' visible';
                }
                //  @p show status
                if ($settings['infiniteScroll_enable_status']) {
                    ?>
                    <nav class="e-add-infiniteScroll">
                        <div class="e-add-page-load-status e-add-page-load-status-<?php echo $this->get_id() . $preview_mode; ?>">
                            <?php
                            if ($settings['infiniteScroll_loading_type'] == 'text') {
                                ?>
                                <div class="infinite-scroll-request status-text"><?php echo __($settings['infiniteScroll_label_loading'], 'e-addons' . '_texts'); ?></div>
                                <?php
                            } else if ($settings['infiniteScroll_loading_type'] == 'ellips') {
                                ?>
                                <div class="loader-ellips infinite-scroll-request">
                                    <span class="loader-ellips__dot"></span>
                                    <span class="loader-ellips__dot"></span>
                                    <span class="loader-ellips__dot"></span>
                                    <span class="loader-ellips__dot"></span>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="infinite-scroll-last status-text"><?php echo __($settings['infiniteScroll_label_last'], 'e-addons' . '_texts'); ?></div>
                            <div class="infinite-scroll-error status-text"><?php echo __($settings['infiniteScroll_label_error'], 'e-addons' . '_texts'); ?></div>

                            <div class="e-add-infinite-scroll-paginator" role="navigation">
                                <a class="e-add-infinite-scroll-paginator__next e-add-infinite-scroll-paginator__next-<?php echo $this->get_id(); ?>" href="<?php echo $this->get_next_pagination(); ?>"><?php //echo __('Next', 'e-addons');    ?></a>
                            </div>
                        </div>
                    </nav>
                    <?php
                } // ens show status
                // Infinite scroll Button ...
                if ($settings['infiniteScroll_trigger'] == 'button') {
                    ?>
                    <div class="e-add-infiniteScroll">
                        <button class="e-add-view-more-button e-add-view-more-button-<?php echo $this->get_id(); ?>"><?php echo __($settings['infiniteScroll_label_button'], 'e-addons' . '_texts'); ?></button>
                    </div>
                    <?php
                }
            } // end infinitescroll eneble
        }
        // --------------------------------------------------------------------
    }

    ///
    public function numeric_query_pagination($pages, $settings) {
        $icon_first = '';
        $icon_last = '';
        if (!empty($settings['pagination_icon_prevnext']['value'])) {
            if ($settings['pagination_icon_prevnext']['value']) {
                $icon_prevnext = str_replace('right', '', $settings['pagination_icon_prevnext']['value']);
                $icon_prev = '<i class="' . $icon_prevnext . 'left"></i> ';
                $icon_next = '<i class="' . $icon_prevnext . 'right"></i> ';
            }
        }
        if (!empty($settings['pagination_icon_firstlast']['value'])) {
            if ($settings['pagination_icon_firstlast']['value']) {
                $icon_firstlast = str_replace('right', '', $settings['pagination_icon_firstlast']['value']);
                $icon_first = '<i class="' . $icon_firstlast . 'left"></i> ';
                $icon_last = '<i class="' . $icon_firstlast . 'right"></i> ';
            }
        }
        $range = (int) $settings['pagination_range'] - 1; //la quantità di numeri visualizzati alla volta
        //@p in questo passaggio ho dei dubbi ..vedo il risultato..
        $showitems = ($range)/* - 1 */;
        //$showitems = ($range * 2)/* - 1*/;

        $paged = max(1, get_query_var('page'), get_query_var('page'));

        if (empty($paged))
            $paged = 1;


        if ($pages == '') {
            global $wp_query;

            $pages = $wp_query->max_num_pages;

            if (!$pages) {
                $pages = 1;
            }
        }

        if (1 != $pages) {
            echo '<nav class="elementor-pagination e-add-pagination" role="navigation" aria-label="Pagination">';

            //Progression
            if ($settings['pagination_show_progression'])
                echo '<span class="progression">' . $paged . ' / ' . $pages . '</span>';

            /* echo "<span>paged: ".$paged."</span>";
              echo "<span>range: ".$range."</span>";
              echo "<span>showitems: ".$showitems."</span>";
              echo "<span>pages: ".$pages."</span>"; */

            //First
            if ($settings['pagination_show_firstlast'])
                if ($paged > 2 && $paged > $range + 1 && $showitems < $pages)
                    echo '<a href="' . self::get_linkpage(1) . '" class="pagefirst">' . $icon_first . ' ' . __($settings['pagination_first_label'], 'e-addons' . '_texts') . '</a>';

            //Prev
            if ($settings['pagination_show_prevnext'])
                if ($paged > 1 && $showitems < $pages)
                    echo '<a href="' . self::get_linkpage($paged - 1) . '" class="pageprev">' . $icon_prev . ' ' . __($settings['pagination_prev_label'], 'e-addons' . '_texts') . '</a>';

            //Numbers
            if ($settings['pagination_show_numbers'])
                for ($i = 1; $i <= $pages; $i++) {
                    if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                        echo ($paged == $i) ? "<span class=\"current\">" . $i . "</span>" : "<a href='" . self::get_linkpage($i) . "' class=\"inactive\">" . $i . "</a>";
                    }
                }

            //Next
            if ($settings['pagination_show_prevnext'])
                if ($paged < $pages && $showitems < $pages)
                    echo '<a href="' . self::get_linkpage($paged + 1) . '" class="pagenext">' . __($settings['pagination_next_label'], 'e-addons' . '_texts') . $icon_next . '</a>';

            //Last
            if ($settings['pagination_show_firstlast'])
                if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages)
                    echo '<a href="' . self::get_linkpage($pages) . '" class="pagelast">' . __($settings['pagination_last_label'], 'e-addons' . '_texts') . $icon_last . '</a>';

            echo '</nav>';
        }
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

        return $url;
    }

}
