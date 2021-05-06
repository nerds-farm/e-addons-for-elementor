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
trait Infinite_Scroll {

    protected function add_infinite_scroll_section() {
        // ------------------------------------------------------------------ [SECTION INFINITE SCROLL]
        $this->start_controls_section(
                'section_infinitescroll', [
            'label' => '<i class="eaddicon eicon-navigation-horizontal" aria-hidden="true"></i> ' . __('Infinite Scroll', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                '_skin' => ['', 'grid', 'filters', 'timeline', 'list', 'table'],
                'infiniteScroll_enable' => 'yes',
                'query_type' => ['automatic_mode', 'get_cpt', 'get_tax', 'get_users_and_roles', 'get_attachments']
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_trigger', [
            'label' => __('Trigger', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => 'button',
            'frontend_available' => true,
            'options' => [
                'button' => __('On Click Button', 'e-addons'),
                'scroll' => __('On Scroll Page', 'e-addons'),
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_label_button', [
            'label' => __('Label Button', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => __('View more', 'e-addons'),
            'condition' => [
                'infiniteScroll_trigger' => 'button',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_enable_status', [
            'label' => __('Enable Status', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before',
                ]
        );

        $this->add_control(
                'infiniteScroll_show_preview', [
            'label' => __('Show Status PREVIEW in Editor Mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'condition' => [
                'infiniteScroll_enable_status' => 'yes',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_loading_type', [
            'label' => __('Loading Type', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'options' => [
                'ellips' => [
                    'title' => __('Ellips', 'e-addons'),
                    'icon' => 'fa fa-ellipsis-h',
                ],
                'text' => [
                    'title' => __('Label Text', 'e-addons'),
                    'icon' => 'fa fa-font',
                ]
            ],
            'default' => 'ellips',
            'separator' => 'before',
            'condition' => [
                'infiniteScroll_enable_status' => 'yes',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_label_loading', [
            'label' => __('Label Loading', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Loading...', 'e-addons'),
            'condition' => [
                'infiniteScroll_enable_status' => 'yes',
                'infiniteScroll_loading_type' => 'text',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_label_last', [
            'label' => __('Label Last', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => __('End of content', 'e-addons'),
            'condition' => [
                'infiniteScroll_enable_status' => 'yes',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_label_error', [
            'label' => __('Label Error', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => __('No more articles to load', 'e-addons'),
            'condition' => [
                'infiniteScroll_enable_status' => 'yes',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_enable_history', [
            'label' => __('Enable History', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'frontend_available' => true,
                ]
        );

        $this->end_controls_section();
    }

    protected function render_infinite_scroll() {
        // Infinite scroll pagination -----------------------------------------------
        // @p ..infiniteScroll è abilitato e anche se i post generati sono maggiori dei post visualizzati..
        if ($this->get_settings_for_display('infiniteScroll_enable')) {
            $query = $this->get_query();
            $querytype = $this->get_querytype();
            $settings = $this->get_settings_for_display();

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
                    <nav class="e-add-infiniteScroll" role="navigation">
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
    public static function infinite_scroll_pagination($settings) {
        
    }

}
