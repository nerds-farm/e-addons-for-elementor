<?php

namespace EAddonsForElementor\Core\Traits;

/**
 * @author francesco
 */
trait Elementor {

    public static $documents = [];

    public static function get_current_post_id() {
        if (isset(\Elementor\Plugin::instance()->documents)) {
            return \Elementor\Plugin::instance()->documents->get_current()->get_main_id();
        }
        return get_the_ID();
    }

    public static function get_post_id_from_data($data) {
        if (is_array($data)) {
            if (!empty($data['id'])) {
                return $data['id'];
            }
            $sub = reset($data);
            return self::get_post_id_from_data($sub);
        }
        return false;
    }

    public static function get_post_id_by_data($data, $p_id = 0) {
        $e_id = self::get_post_id_from_data($data);
        if ($e_id) {
            if (is_singular() && !$p_id) {
                $p_id = get_the_id();
            }
            return self::get_template_by_element_id($e_id, $p_id);
        }
        return false;
    }

    public static function get_template_by_element_id($e_id, $p_id = 0) {
        $t_id = false;
        if (empty(self::$documents[$e_id])) {
            // search element settings elsewhere (maybe in a template)
            global $wpdb;
            $table = $wpdb->prefix . 'postmeta';
            $q = "SELECT post_id FROM " . $table . " WHERE meta_key LIKE '_elementor_data' AND meta_value LIKE '%\"id\":\"" . $e_id . "\",%'";
            if ($p_id) {
                $q .= ' AND post_id = ' . $p_id;
            } else {
                $q .= " AND post_id IN ( SELECT id FROM " . $wpdb->prefix . "posts WHERE post_status LIKE 'publish' )";
            }
            $results = $wpdb->get_results($q);
            if (!empty($results)) {
                $result = reset($results);
                $p_id = reset($result);
            } else {
                $p_id = -1;
            }
            self::$documents[$e_id] = $t_id = $p_id;
        } else {
            $t_id = self::$documents[$e_id];
        }
        if ($p_id && !$t_id) {
            $t_id = self::get_template_by_element_id($e_id);
        }
        return $t_id;
    }
    
    public static function get_widget_type_by_id($e_id, $p_id = null) {
        $p_id = self::get_element_post_id($e_id, $p_id);
        if (intval($p_id) > 0) {
            $document = \Elementor\Plugin::$instance->documents->get($p_id);
            if ($document) {
                $e_raw = self::get_element_from_data($document->get_elements_data(), $e_id);
                if (!empty($e_raw['widgetType'])) {
                    return $e_raw['widgetType'];
                }
            }
        }
        return false;
    }
    
    public static function get_element_type_by_id($e_id, $p_id = null) {
        $p_id = self::get_element_post_id($e_id, $p_id);
        if (intval($p_id) > 0) {
            $document = \Elementor\Plugin::$instance->documents->get($p_id);
            if ($document) {
                $e_raw = self::get_element_from_data($document->get_elements_data(), $e_id);
                if (!empty($e_raw['elType'])) {
                    return $e_raw['elType'];
                }
            }
        }
        return false;
    }
    
    public static function get_element_post_id($e_id, $p_id = 0) {        
        if (!$p_id && $e_id) {
            $p_id = self::get_template_by_element_id($e_id);
        }
        if (!$p_id && !empty($_REQUEST['post_id'])) {
            $p_id = absint($_REQUEST['post_id']);
        }
        if (!$p_id && !empty($_REQUEST['post'])) {
            $p_id = absint($_REQUEST['post']);
        }        
        if (!$p_id) {                                
            $p_id = get_the_ID();
        }
        return $p_id;
    }

    public static function get_element_instance_by_id($e_id, $p_id = null) {        
        $p_id = self::get_element_post_id($e_id, $p_id);
        if (intval($p_id) > 0) {
            $document = \Elementor\Plugin::$instance->documents->get($p_id);
            if ($document) {
                $e_raw = self::get_element_from_data($document->get_elements_data(), $e_id);
                //var_dump($e_raw); die();
                if ($e_raw) {
                    $element = \Elementor\Plugin::$instance->elements_manager->create_element_instance($e_raw);
                    return $element;
                } else {
                    $t_id = self::get_template_by_element_id($e_id);
                    if ($t_id && $t_id > 0 && $t_id != $p_id) {
                        return self::get_element_instance_by_id($e_id, $t_id);
                    }
                }
            }
        }
        return false;
    }

    public static function get_current_element() {
        return apply_filters('e_addons/current_element', false);
    }

    public static function get_settings_by_element_id($e_id = null, $p_id = null) {
        $element = self::get_element_instance_by_id($e_id, $p_id);
        if ($element) {
            return $element->get_settings_for_display();
        }
        return false;
    }

    public static function get_element_from_data($elements, $e_id) {
        foreach ($elements as $element) {
            if ($e_id === $element['id']) {
                return $element;
            }
            if (!empty($element['elements'])) {
                $element = self::get_element_from_data($element['elements'], $e_id);
                if ($element) {
                    return $element;
                }
            }
        }
        // $element = \ElementorPro\Modules\Forms\Module::find_element_recursive($elements->get_elements_data(), $e_id);
        return false;
    }

    public static function is_preview($editor_mode = false) {
        return !empty($_GET['elementor-preview']) 
        || (!empty($_GET['post']) && !empty($_GET['action']) && $_GET['action'] == 'elementor') 
        || (wp_doing_ajax() && !empty($_POST['action']) && $_POST['action'] == 'elementor_ajax') 
        || ($editor_mode && \Elementor\Plugin::$instance->editor->is_edit_mode());
    }

    public static function get_template_from_html($content = '') {
        $tmp = explode('class="elementor elementor-', $content, 2);
        if (count($tmp) > 1) {
            $tmp = str_replace('"', ' ', end($tmp));
            list($id, $more) = explode(' ', $tmp, 2);
            return intval($id);
        }
        return false;
    }

    public static function get_elementor_capability() {
        if (is_user_logged_in()) {
            if (is_super_admin()) {
                return true;
            }
            if (is_singular()) {
                if (\Elementor\User::is_current_user_can_edit_post_type(get_post_type())) {
                    return true;
                }
            } else {
                return \Elementor\User::is_current_user_can_edit_post_type('elementor_library');
            }
        }
        return false;
    }

    public static function get_icon($icon) {
        ob_start();
        \Elementor\Icons_Manager::render_icon($icon);
        return ob_get_clean();
    }

    /**
     * Generate trigger URL based on the popup ID and the trigger type.
     *
     * @param integer $p_id
     * @param string $action
     * @return string
     */
    public function get_popup_url($id, $action = 'open') {
        $url = '';
        // Generate the URL based on its action using the native Elementor's function.
        switch ($action) {
            case 'close':
            case 'close-forever':
                $url = \Elementor\Plugin::instance()->frontend->create_action_hash(
                        'popup:close',
                        array(
                            'do_not_show_again' => 'close-forever' === $action ? 'yes' : '',
                        )
                );
                break;
            case 'open':
            case 'toggle':
            default:
                $url = \Elementor\Plugin::instance()->frontend->create_action_hash(
                        'popup:open',
                        array(
                            'id' => strval($id),
                            'toggle' => 'toggle' === $action,
                        )
                );
                break;
        }
        $url = str_replace('%23', '#', $url);
        return $url;
    }

    public static function get_dynamic_tags_categories() {
        //new \Elementor\Modules\DynamicTags\Module();
        $reflection = new \ReflectionClass('\Elementor\Modules\DynamicTags\Module');
        $categories = $reflection->getConstants();
        //var_dump(array_values($categories)); die();
        return array_values($categories);
    }

    public static function add_help_control($base, $element = false) {
        if (!$element) {
            $element = $base;
        }
        $element->add_control(
                'e_' . $base->get_name() . '_help', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $base->get_docs() . '" target="_blank">' . __('Need Help', 'elementor') . ' <i class="eicon-help-o"></i></a></div>',
            'separator' => 'before',
                ]
        );
    }
    
    public static function get_placeholder_image_src() {
        return \Elementor\Utils::get_placeholder_image_src();
    }
    
    public static function get_current_template($post_id = false) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        if (\Elementor\Plugin::instance()->db->is_built_with_elementor($post_id)) {
            return $post_id;
        } else {
            if (\EAddonsForElementor\Core\Utils::is_plugin_active('elementor-pro')) {                
                $locations = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->get_locations();
                if (!empty($locations['single'])) {
                    $documents_for_location = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location('single');
                    if (!empty($documents_for_location)) {
                        foreach ($documents_for_location as $document_id => $document) {
                            return $document_id;
                        }
                    }
                }                
                $document = \Elementor\Plugin::instance()->documents->get($post_id);
                if ($document) {
                    return $document->get_main_id();
                }
            }
        }
        return false;
    }
    
    /**
	 * Add render attributes.
	 *
	 * Used to add attributes to the current element wrapper HTML tag.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	public static function add_render_attributes($element) {
		$id = $element->get_id();

		$settings = $element->get_settings_for_display();
		$frontend_settings = $element->get_frontend_settings();
		$controls = $element->get_controls();

		$element->add_render_attribute( '_wrapper', [
			'class' => [
				'elementor-element',
				'elementor-element-' . $id,
			],
			'data-id' => $id,
			'data-element_type' => $element->get_type(),
		] );

		$class_settings = [];

		foreach ( $settings as $setting_key => $setting ) {
			if ( isset( $controls[ $setting_key ]['prefix_class'] ) ) {
				$class_settings[ $setting_key ] = $setting;
			}
		}

		foreach ( $class_settings as $setting_key => $setting ) {
			if ( empty( $setting ) && '0' !== $setting ) {
				continue;
			}

			$element->add_render_attribute( '_wrapper', 'class', $controls[ $setting_key ]['prefix_class'] . $setting );
		}

		$_animation = ! empty( $settings['_animation'] );
		$animation = ! empty( $settings['animation'] );
		$has_animation = $_animation && 'none' !== $settings['_animation'] || $animation && 'none' !== $settings['animation'];

		if ( $has_animation ) {
			$is_static_render_mode = Plugin::$instance->frontend->is_static_render_mode();

			if ( ! $is_static_render_mode ) {
				// Hide the element until the animation begins
				$element->add_render_attribute( '_wrapper', 'class', 'elementor-invisible' );
			}
		}

		if ( ! empty( $settings['_element_id'] ) ) {
			$element->add_render_attribute( '_wrapper', 'id', trim( $settings['_element_id'] ) );
		}

		if ( $frontend_settings ) {
			$element->add_render_attribute( '_wrapper', 'data-settings', wp_json_encode( $frontend_settings ) );
		}

		/**
		 * After element attribute rendered.
		 *
		 * Fires after the attributes of the element HTML tag are rendered.
		 *
		 * @since 2.3.0
		 *
		 * @param Element_Base $this The element.
		 */
		do_action( 'elementor/element/after_add_attributes', $element );
	}

}
