<?php

use EAddonsForElementor\Core\Utils;

// check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

\EAddonsForElementor\Plugin::instance()->assets_manager->enqueue_icons();

$modules_manager = \EAddonsForElementor\Plugin::instance()->modules_manager;
$folders = array('widgets', 'extensions', 'tags', 'skins', 'globals', 'actions', 'shortcodes', 'tweaks', 'fields', 'controls', 'controls/groups');
$e_addons_plugins = \EAddonsForElementor\Plugin::instance()->get_addons(true);
?>
<div class="wrap nav-menus-php">

    <?php $this->top_menu(); ?>

    <h1 class="e_addons-title"><span class="e_addons_ic elementor-icon eicon-settings"></span> e-addons Settings</h1>

    <?php
    if (isset($_REQUEST['action'])) {
        if (isset($_POST['action']) && $_POST['action'] == 'save') {
        $value = array();
        foreach ($e_addons_plugins as $e_plugin) {
            if ($e_plugin['active']) {
                $modules = $modules_manager->find_modules($e_plugin['path']);
                foreach ($modules as $amod) {
                    foreach ($folders as $folder) {
                        $expr = $e_plugin['path'] . '/modules/' . $amod . '/' . $folder . '/*.php';
                        $files = glob($expr);
                        $files = apply_filters('e_addons/' . $e_plugin['TextDomain'] . '/modules/' . $amod . '/' . $folder, $files);

                        if ($amod == 'disable' && $folder == 'widgets') {
                            global $wp_widget_factory;
                            $black_list = apply_filters('elementor/widgets/black_list', []);
                            foreach ($wp_widget_factory->widgets as $widget_class => $widget_obj) {
                                if (in_array($widget_class, $black_list)) {
                                    continue;
                                }
                                $elementor_widget_class = 'Elementor\Widget_WordPress';
                                $element = new $elementor_widget_class([], ['widget_name' => $widget_class,]);
                                $files[] = $folder . DIRECTORY_SEPARATOR . $element->get_name() . '.php';
                            }
                        }

                        foreach ($files as $afile) {
                            $file_name = pathinfo($afile, PATHINFO_FILENAME);
                            if ($file_name == 'wordress')
                                continue;

                            if ($amod != 'disable' || $folder != 'widgets') {
                                $class = \EAddonsForElementor\Core\Helper::path_to_class($afile);
                                $element = new $class();
                                if (method_exists($element, 'show_in_settings')) {
                                    if (!$element->show_in_settings()) {
                                        continue;
                                    }
                                }
                            }

                            if (!isset($_POST['settings'][$folder][$amod][$file_name])) {
                                //var_dump($_POST['settings']['extensions']); die();
                                // is disabled
                                $value[$folder][$amod][] = $file_name;
                            }
                        }
                    }
                }
            }
        }
        update_option('e_addons_disabled', $value);
        Utils::e_admin_notice(__('Your preferences has been stored succefully!'), 'success');
        }
        if (isset($_GET['action']) && $_GET['action'] == 'reset') {
            delete_option('e_addons_disabled');
            Utils::e_admin_notice(__('Your preferences has been reset to default!'), 'success');
        }
    }
    $e_addons_disabled = get_option('e_addons_disabled', array());
    $e_docs = 'https://e-addons.com/?p=3412';
    ?>
    <form id="nav-menu-meta" class="nav-menu-meta" action="" method="POST">
        <input type="hidden" name="page" value="e_addons_settings">
        <input type="hidden" name="action" value="save">

        <div id="nav-menus-frame" class="wp-clearfix">
            <div id="menu-settings-column" class="metabox-holder">

                <!--<h2>e-addons Plugins</h2>-->
                <div id="side-sortables" class="e-accordion-container">
                    <ul class="outer-border e-nav-tab-wrapper">
                        <?php
                        foreach ($e_addons_plugins as $ekey => $e_plugin) {
                            if (!$e_plugin['active']) {
                                continue;
                            }
                            ?>
                            <li id="e_addon_plugin_module_<?php echo $e_plugin['TextDomain']; ?>_li" class="e-control-section e-accordion-section e_addon_plugin">
                                <a href="#e_addon_plugin_module_<?php echo $e_plugin['TextDomain']; ?>"  class="nav-tab<?php echo (!$ekey) ? ' nav-tab-active' : ''; ?>">
                                    <?php echo $e_plugin['Name']; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                    <a id="e_get_more" href="https://e-addons.com" target="_blank">Get more<?php echo count($e_addons_plugins) == 1 ? ' info' : ''; ?>!</a>
                </div>
            </div>

            <div id="menu-management-liquid">
                <div id="menu-management">
                    <div class="menu-edit">

                        <div id="nav-menu-header">
                            <div class="my_e-addons_testatina major-publishing-actions wp-clearfix">
                                <label><b>Available Modules</b> - Enable/disable addon features</label>
                                <div class="publishing-action">
                                    <input type="submit" name="save_menu" id="save_menu_header" class="button button-primary button-large menu-save" value="Save current Settings">
                                </div><!-- END .publishing-action -->
                            </div>
                        </div>

                        <div id="post-body">
                            <div id="post-body-content" class="wp-clearfix">

                                <ul>
                                    <?php
                                    foreach ($e_addons_plugins as $e_plugin) {
                                        if (!$e_plugin['active']) {
                                            continue;
                                        }
                                        ?>
                                        <li id="e_addon_plugin_module_<?php echo $e_plugin['TextDomain']; ?>_settings" class="e_addon_plugin e_addon_plugin_area">

                                            <?php if ($e_plugin['TextDomain'] == 'e-addons-for-elementor') { ?>
                                                <!--<h1 id="e_addon_plugin_module_<?php echo $e_plugin['TextDomain']; ?>_name" class="e_addons-title"><?php echo $e_plugin['Name']; //'<i class="eadd-logo-e-addons"></i> addons for Elementor'; ?></h1>-->
                                            <?php } else { ?>
                                                <h2 id="e_addon_plugin_module_<?php echo $e_plugin['TextDomain']; ?>_name"><?php echo $e_plugin['Name']; ?></h2>
                                            <?php } ?>
                                            <ul class="e_addon_plugin_modules">
                                                <?php
                                                $modules = $modules_manager->find_modules($e_plugin['path']);      
                                                if ($key = array_search('disable', $modules)) {
                                                    unset($modules[$key]);
                                                    $modules[] = 'disable';
                                                }
                                                foreach ($modules as $amod) {
                                                    $unique_mod = $e_plugin['TextDomain'] . '_' . $amod;            
                                                    ?>
                                                    <li class="e_addon_plugin_module" id="e_addon_plugin_module_<?php echo $unique_mod; ?>">
                                                        <h3 class="e-settings-module-title">
                                                            <?php echo Utils::slug_to_camel($amod, ' '); ?>
                                                            <input class="e-settings-toggle" type="checkbox" checked="">
                                                        </h3>
                                                        <ul class="e-settings-module-features">
                                                            <?php
                                                            $has_features = false;
                                                            foreach ($folders as $folder) {
                                                                
                                                                $title = str_replace(DIRECTORY_SEPARATOR, ' ', $folder);
                                                                $title = ucwords($title);
                                                                $title = ($folder == 'tags') ? 'Dynamic Tags' : $title;
                                                                $title = ($folder == 'globals') ? 'Global' : $title;
                                                                
                                                                $has_features_folder = false;
                                                                $expr = $e_plugin['path'] . '/modules/' . $amod . '/' . $folder . '/*.php';
                                                                $files = glob($expr);
                                                                $files = apply_filters('e_addons/' . $e_plugin['TextDomain'] . '/modules/' . $amod . '/' . $folder, $files);
                                                                if (!empty($files)) {
                                                                    ?>
                                                                    <li id="e_addon_plugin_module_<?php echo $e_plugin['TextDomain'] . '_' . $amod . '_' . $folder; ?>">
                                                                        <h4 class="e-settings-title"><?php echo $title; ?></h4>
                                                                        <ul>
                                                                            <?php
                                                                            foreach ($files as $afile) {
                                                                                $file_name = pathinfo($afile, PATHINFO_FILENAME);
                                                                                if ($file_name == 'wordpress') {
                                                                                    global $wp_widget_factory;
                                                                                    // Allow themes/plugins to filter out their widgets.
                                                                                    $black_list = [];

                                                                                    /**
                                                                                     * Elementor widgets black list.
                                                                                     *
                                                                                     * Filters the widgets black list that won't be displayed in the panel.
                                                                                     *
                                                                                     * @since 1.0.0
                                                                                     *
                                                                                     * @param array $black_list A black list of widgets. Default is an empty array.
                                                                                     */
                                                                                    $black_list = apply_filters('elementor/widgets/black_list', $black_list);
                                                                                    foreach ($wp_widget_factory->widgets as $widget_class => $widget_obj) {
                                                                                        if (in_array($widget_class, $black_list)) {
                                                                                            continue;
                                                                                        }
                                                                                        $elementor_widget_class = 'Elementor\Widget_WordPress';
                                                                                        $element = new $elementor_widget_class([], ['widget_name' => $widget_class,]);
                                                                                        $file_label = (substr($widget_class, 0, 10) == 'WP_Widget_') ? substr($widget_class, 10) : $widget_class;
                                                                                        $unique_feature = $unique_mod . '_' . $folder . '_' . $file_name . '_' . $widget_class;
                                                                                        $checked = (!empty($e_addons_disabled[$folder][$amod]) && in_array($element->get_name(), $e_addons_disabled[$folder][$amod])) ? '' : ' checked';

                                                                                        $docs = $e_docs;
                                                                                        ?>
                                                                                        <li class="e-setting">
                                                                                            <input class="e-setting-input-checkbox" id="<?php echo $unique_feature; ?>" type="checkbox" name="settings[<?php echo $folder; ?>][<?php echo $amod; ?>][<?php echo $element->get_name(); ?>]"<?php echo $checked; ?>>
                                                                                            <label class="e-setting-label" for="<?php echo $unique_feature; ?>"><i class="e-setting-icon <?php echo $element->get_icon(); ?>"></i><span class="e-settings-label"><?php echo $file_label; ?></span></label>
                                                                                            <a class="e-setting-info" href="<?php echo $docs; ?>" target="_blank"><i class="eicon-help-o"></i></a>
                                                                                        </li>
                                                                                        <?php
                                                                                    }
                                                                                } else {
                                                                                    $unique_feature = $unique_mod . '_' . $folder . '_' . $file_name;

                                                                                    $checked = (!empty($e_addons_disabled[$folder][$amod]) && in_array($file_name, $e_addons_disabled[$folder][$amod])) ? '' : ' checked';
                                                                                    $file_label = Utils::slug_to_camel($file_name, ' ');

                                                                                    $class = \EAddonsForElementor\Core\Helper::path_to_class($afile);
                                                                                    //echo ' (' . $class . ')';

                                                                                    $element = new $class();

                                                                                    if (method_exists($element, 'get_name')) {
                                                                                        $file_label = $element->get_name();
                                                                                    }
                                                                                    if (method_exists($element, 'get_label')) {
                                                                                        $file_label = $element->get_label();
                                                                                    } else if (method_exists($element, 'get_title')) {
                                                                                        $file_label = $element->get_title();
                                                                                    }

                                                                                    if (method_exists($element, 'show_in_settings')) {
                                                                                        if (!$element->show_in_settings()) {
                                                                                            continue;
                                                                                        }
                                                                                    }

                                                                                    // maybe only for Widget Common?
                                                                                    if (method_exists($element, 'show_in_panel')) {
                                                                                        if (!$element->show_in_panel()) {
                                                                                            ?>
                                                                                            <input type="hidden" name="settings[<?php echo $folder; ?>][<?php echo $amod; ?>][<?php echo $file_name; ?>]" value="1">
                                                                                            <?php
                                                                                            continue;
                                                                                        }
                                                                                    }

                                                                                    $docs = '#';
                                                                                    if (method_exists($element, 'get_help_url')) {
                                                                                        $docs = $element->get_help_url();
                                                                                    }
                                                                                    if (method_exists($element, 'get_docs')) {
                                                                                        $docs = $element->get_docs();
                                                                                    }
                                                                                    $class_namespace = explode('\\', $class);
                                                                                    if (reset($class_namespace) == 'Elementor') {
                                                                                        $docs = $e_docs;
                                                                                    }
                                                                                    ?>
                                                                                    <li class="e-setting">
                                                                                        <input class="e-setting-input-checkbox" id="<?php echo $unique_feature; ?>" type="checkbox" name="settings[<?php echo $folder; ?>][<?php echo $amod; ?>][<?php echo $file_name; ?>]"<?php echo $checked; ?>>
                                                                                        <label class="e-setting-label" for="<?php echo $unique_feature; ?>"><i class="e-setting-icon <?php echo $element->get_icon(); ?>"></i><span class="e-settings-label"><?php echo $file_label; ?></span></label>
                                                                                        <a class="e-setting-info" href="<?php echo $docs; ?>" target="_blank"><i class="eicon-help-o"></i></a>
                                                                                    </li>
                                                                                    <?php
                                                                                    $has_features_folder = true;
                                                                                    //echo ' (' . $unique_feature . ')';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </ul>
                                                                    </li>
                                                                    <?php
                                                                    $has_features = $has_features || $has_features_folder;
                                                                }
                                                                if (!$has_features_folder) {
                                                                    echo '<style>#e_addon_plugin_module_' . $e_plugin['TextDomain'] . '_' . $amod . '_' . $folder . ' {display:none;}</style>';
                                                                }
                                                            }
                                                            if (!$has_features) {
                                                                echo '<style>#e_addon_plugin_module_' . $e_plugin['TextDomain'] . '_' . $amod . '{display:none;}</style>';
                                                            }
                                                            ?>
                                                        </ul>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>

                            </div><!-- /#post-body-content -->
                        </div><!-- /#post-body -->

                        <div id="nav-menu-footer">
                            <div class="my_e-addons_testatina major-publishing-actions wp-clearfix">
                                <div class="publishing-action">
                                    <input type="submit" name="save_menu" id="save_menu_footer" class="button button-primary button-large menu-save" value="Save current Settings">
                                </div><!-- END .publishing-action -->
                            </div>
                        </div>

                    </div><!-- /.menu-edit -->
                </div><!-- /#menu-management -->
            </div>
        </div>

    </form>
    
    <?php
    $disabled = get_option('e_addons_disabled');
    if (!empty($disabled)) {
        //var_dump($disabled);
    ?>
    <a class="button button-primary button-large" style="background-color: #a00;border-color: white;" href="?page=<?php echo $_GET['page']; ?>&action=reset">        
        <i class="eicon-editor-close"></i>
        <?php _e('Reset to defaults', 'e-addons-for-elementor'); ?>
    </a>
    <?php } ?>
</div>

<script>
    jQuery(document).ready(function () {
        jQuery('#side-sortables .e_addon_plugin:first-child a').addClass('nav-tab-active');
        jQuery('#post-body .e_addon_plugin').not(':first-child').hide();
        jQuery('#side-sortables a:not(#e_get_more)').on('click', function () {
            jQuery('#side-sortables a.nav-tab-active').removeClass('nav-tab-active');
            jQuery(this).addClass('nav-tab-active');
            jQuery('#post-body li.e_addon_plugin').hide();
            jQuery(jQuery(this).attr('href') + '_settings').show();
        });

        if (location.hash) {
            jQuery(location.hash + '_li a').trigger('click');
        }
        
        jQuery('.e-settings-toggle').on('click', function () {
            let module = jQuery(this).closest('.e_addon_plugin_module');
            let onoff = jQuery(this).prop("checked");
            //console.log(onoff);            
            module.find('.e-settings-module-features .e-setting-input-checkbox').each(function(){
                jQuery(this).prop("checked", onoff).trigger("change");
            });
            //return false;
        });
    });
    
    
</script>
        <?php
        //wp_print_styles(array('e-addons-admin-settings'));