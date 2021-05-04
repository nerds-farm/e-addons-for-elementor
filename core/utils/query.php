<?php
namespace EAddonsForElementor\Core\Utils;

/**
 * Description of query
 *
 * @author fra
 */
class Query {
    
    //@p questo metodo restituisce l'id in base al tipo in corso
    public static function is_id_of() {
        $id_of_content = '';
        //
        // dipende da dove mi trovo utilizzo il tipo diverso: (posst,term,user)
        if ( is_single() || is_page() ) {
            //echo 'sono in single';
            $id_of_content = get_the_ID();
        }
        if( is_post_type_archive() ){
            //echo 'sono in archivio del type';
            $id_of_content = get_queried_object()->term_id;
        }
        if( is_archive() ){
            //echo 'sono in archivio';
            $id_of_content = get_queried_object()->term_id;
        }
        if( is_front_page() ){
            //echo 'sono in home';
            $id_of_content = get_the_ID();
        }
        if ( is_tax() || is_category() || is_tag() ) {
            //echo 'sono in termine';
            $id_of_content = get_queried_object()->term_id;
        }
        if( is_author() ){
            //echo 'sono in autore';
            $id_of_content = get_the_author_meta('ID');
        }
        if( is_search() ){
            //echo 'sono in ricerca';
            $id_of_content = get_the_ID();
        }
        if( is_404() ){
            //echo 'sono in 404';
            $id_of_content = get_the_ID();
        }

        if (!$id_of_content) {
            $id_of_content = get_queried_object_id();
        }
        return $id_of_content;
    }
    //@p questo metodo restituisce il tipo di query in corso
    public static function is_type_of() {
        $type_of_content = '';
        //
        // dipende da dove mi trovo utilizzo il tipo diverso: (posst,term,user .. attachment)
        if ( is_single() || is_page() ) {
            //echo 'sono in single';
            $type_of_content = 'post';
        }
        if( is_post_type_archive() ){
            //echo 'sono in archivio del type';
            $type_of_content = 'term';
        }
        if( is_archive() ){
            //echo 'sono in archivio';
            $type_of_content = 'term';
        }
        if( is_front_page() ){
            //echo 'sono in home';
            $type_of_content = 'post';
        }
        if ( is_tax() || is_category() || is_tag() ) {
            //echo 'sono in termine';
            $type_of_content = 'term';
        }
        if( is_author() ){
            //echo 'sono in autore';
            $type_of_content = 'user';
        }
        if( is_search() ){
            //echo 'sono in ricerca';
            $type_of_content = 'post';
        }
        if( is_404() ){
            //echo 'sono in 404';
            $type_of_content = 'post';
        }
                
        if (!$type_of_content) {
            $qo = get_queried_object();
            if ($qo) {
                $class = strtolower(get_class($qo));
                $tmp = explode('_', $class, 2);
                $type_of_content = end($tmp);
            }
        }
        return $type_of_content;
    }
    public static function get_available_image_sizes_options() {
        $imagesizes = array( );
        $available_sizes = get_intermediate_image_sizes();
        $imagesizes['full'] = 'Full';
        if ($available_sizes) {
            
            foreach ($available_sizes as $sizeskey => $sizesval) {
                $imagesizes[$sizesval] = $sizesval;
            }
        }
        return $imagesizes;
    }
    public static function get_available_mime_types_options() {
        $mimetypes = array( );
        $available_mime = get_available_post_mime_types();
        if ($available_mime) {
            
            foreach ($available_mime as $mimekey => $mimeval) {
                $mimetypes[$mimeval] = $mimeval;
            }
        }
        return $mimetypes;
    }
    public static function get_post_orderby_options() {
        $orderby = array(
            'ID' => 'Post Id',
            'author' => 'Post Author',
            'title' => 'Title',
            'date' => 'Date',
            'modified' => 'Last Modified Date',
            'parent' => 'Parent Id',
            'rand' => 'Random',
            'comment_count' => 'Comment Count',
            'menu_order' => 'Menu Order',
            'meta_value' => 'Meta Value',
            'meta_value_num' => 'Meta Value NUM',
            'meta_value_date' => 'Meta Value DATE',
        );

        return $orderby;
    }
    public static function get_term_orderby_options() {
        $orderby = array(
            'id' => 'id',
            'term_id' => 'Term Id',
            'name' => 'Name',
            'slug' => 'Slug',
            'term_group' => 'Term Group',
            'description' => 'Description',
            'parent' => 'Parent',
            'term_order' => 'Term Order',
            'count' => 'Count',
            'include' => 'Include params',
            'meta_value' => 'Meta Value',
            'meta_value_num' => 'Meta Value NUM',
            'meta_value_date' => 'Meta Value DATE',
            'rand' => 'Random',
        );

        return $orderby;
    }
    public static function get_user_orderby_options() {
        $orderby = array(
            'ID' => 'Post Id',
            'display_name' => 'Display Name',
            'name' => 'Name',
            'include' => 'Include ID',
            'user_login' => 'User Login',
            'user_nicename' => 'Nicename',
            'url' => 'URL',
            'user_registered' => 'Registered date',
            'post_count' => 'Post count',
            'meta_value_num' => 'Meta Value NUM',
            'meta_value_date' => 'Meta Value DATE',
            'rand' => 'Random',
        );

        return $orderby;
    }
	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'e-addons' ),
			'sm' => __( 'Small', 'e-addons' ),
			'md' => __( 'Medium', 'e-addons' ),
			'lg' => __( 'Large', 'e-addons' ),
			'xl' => __( 'Extra Large', 'e-addons' ),
		];
    }
    public static function get_meta_comparetype() {
        return array(
            'NUMERIC' => 'NUMERIC',
            'BINARY' => 'BINARY',
            'CHAR' => 'CHAR',
            'DATE' => 'DATE',
            'DATETIME' => 'DATETIME',
            'DECIMAL' => 'DECIMAL',
            'SIGNED' => 'SIGNED',
            'TIME' => 'TIME',
            'UNSIGNED' => 'UNSIGNED'
        );
    }
    public static function get_meta_compare() {
        // meta_compare (string) - Operator to test the 'meta_value'. Possible values are '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP' or 'RLIKE'. Default value is '='.
        return array(
            "=" => "=",
            ">" => "&gt;",
            ">=" => "&gt;=",
            "<" => "&lt;",
            "<=" => "&lt;=",
            "!=" => "!=",
            "LIKE" => "LIKE",
            "RLIKE" => "RLIKE",
            /*
              "E" => "=",
              "GT" => "&gt;",
              "GTE" => "&gt;=",
              "LT" => "&lt;",
              "LTE" => "&lt;=",
              "NE" => "!=",
              "LIKE_WILD" => "LIKE %...%",
             */
            "NOT LIKE" => "NOT LIKE",
            "IN" => "IN (...)",
            "NOT IN" => "NOT IN (...)",
            "BETWEEN" => "BETWEEN",
            "NOT BETWEEN" => "NOT BETWEEN",
            "EXISTS" => "EXISTS",
            "NOT EXISTS" => "NOT EXISTS",
            "REGEXP" => "REGEXP",
            "NOT REGEXP" => "NOT REGEXP",
        );
    }
    
    public static function get_anim_timingFunctions() {
        $tf_p = [
            '' => __('Initial', 'e-addons'),
            'linear' => __('Linear', 'e-addons'),
            'ease' => __('Ease', 'e-addons'),
            'ease-in' => __('Ease In', 'e-addons'),
            'ease-out' => __('Ease Out', 'e-addons'),
            'ease-in-out' => __('Ease In Out', 'e-addons'),
            'cubic-bezier(0.755, 0.05, 0.855, 0.06)' => __('easeInQuint', 'e-addons'),
            'cubic-bezier(0.23, 1, 0.32, 1)' => __('easeOutQuint', 'e-addons'),
            'cubic-bezier(0.86, 0, 0.07, 1)' => __('easeInOutQuint', 'e-addons'),
            'cubic-bezier(0.6, 0.04, 0.98, 0.335)' => __('easeInCirc', 'e-addons'),
            'cubic-bezier(0.075, 0.82, 0.165, 1)' => __('easeOutCirc', 'e-addons'),
            'cubic-bezier(0.785, 0.135, 0.15, 0.86)' => __('easeInOutCirc', 'e-addons'),
            'cubic-bezier(0.95, 0.05, 0.795, 0.035)' => __('easeInExpo', 'e-addons'),
            'cubic-bezier(0.19, 1, 0.22, 1)' => __('easeOutExpo', 'e-addons'),
            'cubic-bezier(1, 0, 0, 1)' => __('easeInOutExpo', 'e-addons'),
            'cubic-bezier(0.6, -0.28, 0.735, 0.045)' => __('easeInBack', 'e-addons'),
            'cubic-bezier(0.175, 0.885, 0.32, 1.275)' => __('easeOutBack', 'e-addons'),
            'cubic-bezier(0.68, -0.55, 0.265, 1.55)' => __('easeInOutBack', 'e-addons'),
        ];
        return $tf_p;
    }
    public static function get_anim_in() {
        $anim = [
            [
                'label' => 'Fading',
                'options' => [
                    'fadeIn' => 'Fade In',
                    'fadeInDown' => 'Fade In Down',
                    'fadeInLeft' => 'Fade In Left',
                    'fadeInRight' => 'Fade In Right',
                    'fadeInUp' => 'Fade In Up',
                ],
            ],
            [
                'label' => 'Zooming',
                'options' => [
                    'zoomIn' => 'Zoom In',
                    'zoomInDown' => 'Zoom In Down',
                    'zoomInLeft' => 'Zoom In Left',
                    'zoomInRight' => 'Zoom In Right',
                    'zoomInUp' => 'Zoom In Up',
                ],
            ],
            [
                'label' => 'Bouncing',
                'options' => [
                    'bounceIn' => 'Bounce In',
                    'bounceInDown' => 'Bounce In Down',
                    'bounceInLeft' => 'Bounce In Left',
                    'bounceInRight' => 'Bounce In Right',
                    'bounceInUp' => 'Bounce In Up',
                ],
            ],
            [
                'label' => 'Sliding',
                'options' => [
                    'slideInDown' => 'Slide In Down',
                    'slideInLeft' => 'Slide In Left',
                    'slideInRight' => 'Slide In Right',
                    'slideInUp' => 'Slide In Up',
                ],
            ],
            [
                'label' => 'Rotating',
                'options' => [
                    'rotateIn' => 'Rotate In',
                    'rotateInDownLeft' => 'Rotate In Down Left',
                    'rotateInDownRight' => 'Rotate In Down Right',
                    'rotateInUpLeft' => 'Rotate In Up Left',
                    'rotateInUpRight' => 'Rotate In Up Right',
                ],
            ],
            [
                'label' => 'Attention Seekers',
                'options' => [
                    'bounce' => 'Bounce',
                    'flash' => 'Flash',
                    'pulse' => 'Pulse',
                    'rubberBand' => 'Rubber Band',
                    'shake' => 'Shake',
                    'headShake' => 'Head Shake',
                    'swing' => 'Swing',
                    'tada' => 'Tada',
                    'wobble' => 'Wobble',
                    'jello' => 'Jello',
                ],
            ],
            [
                'label' => 'Light Speed',
                'options' => [
                    'lightSpeedIn' => 'Light Speed In',
                ],
            ],
            [
                'label' => 'Specials',
                'options' => [
                    'rollIn' => 'Roll In',
                ],
            ]
        ];
        return $anim;
    }
    public static function get_anim_out() {
        $anim = [
            [
                'label' => 'Fading',
                'options' => [
                    'fadeOut' => 'Fade Out',
                    'fadeOutDown' => 'Fade Out Down',
                    'fadeOutLeft' => 'Fade Out Left',
                    'fadeOutRight' => 'Fade Out Right',
                    'fadeOutUp' => 'Fade Out Up',
                ],
            ],
            [
                'label' => 'Zooming',
                'options' => [
                    'zoomOut' => 'Zoom Out',
                    'zoomOutDown' => 'Zoom Out Down',
                    'zoomOutLeft' => 'Zoom Out Left',
                    'zoomOutRight' => 'Zoom Out Right',
                    'zoomOutUp' => 'Zoom Out Up',
                ],
            ],
            [
                'label' => 'Bouncing',
                'options' => [
                    'bounceOut' => 'Bounce Out',
                    'bounceOutDown' => 'Bounce Out Down',
                    'bounceOutLeft' => 'Bounce Out Left',
                    'bounceOutRight' => 'Bounce Out Right',
                    'bounceOutUp' => 'Bounce Out Up',
                ],
            ],
            [
                'label' => 'Sliding',
                'options' => [
                    'slideOutDown' => 'Slide Out Down',
                    'slideOutLeft' => 'Slide Out Left',
                    'slideOutRight' => 'Slide Out Right',
                    'slideOutUp' => 'Slide Out Up',
                ],
            ],
            [
                'label' => 'Rotating',
                'options' => [
                    'rotateOut' => 'Rotate Out',
                    'rotateOutDownLeft' => 'Rotate Out Down Left',
                    'rotateOutDownRight' => 'Rotate Out Down Right',
                    'rotateOutUpLeft' => 'Rotate Out Up Left',
                    'rotateOutUpRight' => 'Rotate Out Up Right',
                ],
            ],
            [
                'label' => 'Attention Seekers',
                'options' => [
                    'bounce' => 'Bounce',
                    'flash' => 'Flash',
                    'pulse' => 'Pulse',
                    'rubberBand' => 'Rubber Band',
                    'shake' => 'Shake',
                    'headShake' => 'Head Shake',
                    'swing' => 'Swing',
                    'tada' => 'Tada',
                    'wobble' => 'Wobble',
                    'jello' => 'Jello',
                ],
            ],
            [
                'label' => 'Light Speed',
                'options' => [
                    'lightSpeedOut' => 'Light Speed Out',
                ],
            ],
            [
                'label' => 'Specials',
                'options' => [
                    'rollOut' => 'Roll Out',
                ],
            ]
        ];
        return $anim;
    }
    public static function get_gsap_ease() {
        $tf_p = [
            'easeNone' => __('None', 'e-addons'),
            'easeIn' => __('In', 'e-addons'),
            'easeOut' => __('Out', 'e-addons'),
            'easeInOut' => __('InOut', 'e-addons'),
        ];
        return $tf_p;
    }

    public static function get_gsap_timingFunctions() {
        $tf_p = [
            'Power0' => __('Linear', 'e-addons'),
            'Power1' => __('Power1', 'e-addons'),
            'Power2' => __('Power2', 'e-addons'),
            'Power3' => __('Power3', 'e-addons'),
            'Power4' => __('Power4', 'e-addons'),
            'SlowMo' => __(' SlowMo', 'e-addons'),
            'Back' => __('Back', 'e-addons'),
            'Elastic' => __('Elastic', 'e-addons'),
            'Bounce' => __('Bounce', 'e-addons'),
            'Circ' => __('Circ', 'e-addons'),
            'Expo' => __('Expo', 'e-addons'),
            'Sine' => __('Sine', 'e-addons'),
        ];
        return $tf_p;
    }
}
