<?php

/**
 * Element Description: ORC Carousel
 */

/**
 * This will load the appropriate jQuery scripts used for the ORC carousel functions
 * 
 * NOTE: This requires that jquery and owl-carousel have previously been registered
 *       Since this is a WPBakery plugin it is assumed that owl-carousel has been
 *       already registered.  
 */
function orc_carousel_scripts() {
    wp_enqueue_script('orc-carousel', plugin_dir_url(__FILE__) . '../js/orc.carousels.js', array('jquery', 'owl-carousel'), '1.0.0.1', true);
}

add_action('wp_enqueue_scripts', 'orc_carousel_scripts');

// Element Class 
class orcCarousel extends WPBakeryShortCode {

    // Element Initialization
    function __construct() {
        add_action('init', array($this, 'orc_carousel_mapping'));
        add_shortcode('orc_carousel', array($this, 'orc_carousel_render'));
    }

    // Element Mapping of parameters
    public function orc_carousel_mapping() {

        $posttypes = array('Pick a post type' => '', 'ORC Tours' => 'tours', 'Programs' => 'program-types', 'ORC Staff Members' => 'orc_staff_member', 'Testimonials' => 'testimonial', 'Video\'s' => 'videos', 'Vision Make History' => 'make-history', 'Vision Community' => 'community');
        $colsarray = array('7' => 7, '6' => 6, '5' => 5, '4' => 4, '3' => 3, '2' => 2, '1' => 1);
        $colstabletarray = array('5' => 5, '4' => 4, '3' => 3, '2' => 2, '1' => 1);
        $colsmobilearray = array('5' => 5, '4' => 4, '3' => 3, '2' => 2, '1' => 1);

        // Stop all if VC is not enabled
        if (!defined('WPB_VC_VERSION')) {
            return;
        }

        // Map the block with vc_map()
        vc_map(
                array(
                    'name' => __('ORC Carousel', 'text-domain'), // Human friendly name for the element
                    'base' => 'orc_carousel', // MUST be shortcode from above
                    'description' => __('Orchard Recovery Center Custom Carousel', 'text-domain'), // Human friendly description for the element
                    'category' => __('ORC Custom Elements', 'text-domain'), // Category for Visual Composer
//				'icon' => get_template_directory_uri().'/assets/img/vc-icon.png',		// Icon to display  ***** TODO
                    'params' => array(// Parameters for the element

                        array(// First parameter
                            'type' => 'dropdown', // Text filed
//						'holder' => 'small',																// HTML tag where Visual Composer will store attribuite value in Visual Composer edit mode
//						'class' => 'posttype-class',													// Class name that will be added to the "holder" HTML tag. For backent CSS
                            'heading' => __('Post Type', 'text-domain'), // Human friendly title for parameter, visible on shortcode's edit screen
                            'param_name' => 'posttype', // Name of the field that the data will be saved under
                            'value' => $posttypes, // Default value
                            'description' => __('Select the post type for this carousel', 'text-domain'), // Human friendly description of the parameter
                            'admin_label' => true, // Show/Hide value of param in Visual Composer editor
                            'weight' => 0, // Parameters with greater wight will be rendered first
                            'group' => 'Post Info'             // Group/Tab dividers
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => __('Link Image to Posts?', 'text-domain'),
                            'param_name' => 'linkimage',
                            'value' => __('false', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Post Info'
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => __('Christmas testimonial?', 'text-domain'),
                            'param_name' => 'christmas',
                            'value' => __('false', 'text-domain'),
                            'description' => __('Only works on Testimonials?', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Post Info',
                            'dependency' => array(
                                'element' => 'posttype',
                                'value' => array('testimonial'),
                            ),
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => __('Loop carousel?', 'text-domain'),
                            'param_name' => 'loopcarousel',
                            'value' => __('false', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Options'
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => __('Show navigation arrows?', 'text-domain'),
                            'param_name' => 'prevnext',
                            'value' => __('false', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Options'
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => __('Show navigation dots?', 'text-domain'),
                            'param_name' => 'dots',
                            'value' => __('false', 'text-domain'),
                            'admin_label' => true,
                            'weight' => 0,
                            'group' => 'Options'
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => __('Autoplay carousel?', 'text-domain'),
                            'param_name' => 'autoplay',
                            'value' => __('false', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Options'
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => __('Autoplay speed', 'text-domain'),
                            'param_name' => 'speed',
                            'value' => __('7000', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Options',
                            'dependency' => array(
                                'element' => 'autoplay',
                                'value' => array('true'),
                            ),
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => __('Pause on hover?', 'text-domain'),
                            'param_name' => 'pauseonhover',
                            'value' => __('false', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Options',
                            'dependency' => array(
                                'element' => 'autoplay',
                                'value' => array('true'),
                            ),
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => __('Additional Image Class', 'text-domain'),
                            'param_name' => 'additionalclass',
                            'value' => '',
                            'description' => __('Any additional class applied to images?', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Format'
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => __('Margin', 'text-domain'),
                            'param_name' => 'margin',
                            'value' => __('30', 'text-domain'),
                            'description' => __('Margin between elements in carousel?', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Format'
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => __('Image Width', 'text-domain'),
                            'param_name' => 'imgwidth',
                            'value' => __('200', 'text-domain'),
                            'description' => __('Width of the image?', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Format'
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => __('Image Height', 'text-domain'),
                            'param_name' => 'imgheight',
                            'value' => __('200', 'text-domain'),
                            'description' => __('Height of the image?', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Format'
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => __('Round image?', 'text-domain'),
                            'param_name' => 'roundimg',
                            'value' => __('false', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Format'
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => __('Columns Desktop', 'text-domain'),
                            'param_name' => 'colsdesktop',
                            'value' => $colsarray,
                            'description' => __('Number of columns on a desktop', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Format',
                            'std' => 5,
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => __('Columns Tablet', 'text-domain'),
                            'param_name' => 'colstablet',
                            'value' => $colstabletarray,
                            'description' => __('Number of columns on a tablet', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Format',
                            'std' => 3,
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => __('Columns Mobile', 'text-domain'),
                            'param_name' => 'colsmobile',
                            'value' => $colsmobilearray,
                            'description' => __('Number of columns on a mobile', 'text-domain'),
                            'admin_label' => false,
                            'weight' => 0,
                            'group' => 'Format',
                            'std' => 1,
                        ),
                        array(
                            'type' => 'css_editor',
                            'heading' => __('CSS box', 'js_composer'),
                            'param_name' => 'css',
                            'group' => __('Design Options', 'js_composer'),
                        ),
                    )
                )
        );
    }

    // Element HTML
    public function orc_carousel_render($atts) {
        // Params extraction

        extract(shortcode_atts(
                        array(
                            'posttype' => '',
                            'linkimage' => 'false',
                            'prevnext' => 'false',
                            'dots' => 'false',
                            'autoplay' => 'false',
                            'speed' => 7000,
                            'pauseonhover' => 'false',
                            'loopcarousel' => 'false',
                            'christmas' => 'false',
							'additionalclass' => '',
                            'margin' => 30,
                            'imgwidth' => 200,
                            'imgheight' => 200,
                            'roundimg' => 'false',
                            'colsdesktop' => 5,
                            'colstablet' => 3,
                            'colsmobile' => 1,
                            'css' => ''
                        ),
                        $atts
        ));

        $homepage = is_front_page();
        $linkimage = ((0 === strcmp('true', $linkimage)) ? true : false);
        $prevnext = ((0 === strcmp('true', $prevnext)) ? true : false);
        $dots = ((0 === strcmp('true', $dots)) ? true : false);
        $christmas = ((0 === strcmp('true', $christmas)) ? true : false);
        $autoplay = ((0 === strcmp('true', $autoplay)) ? true : false);
        $pauseonhover = ((0 === strcmp('true', $pauseonhover)) ? true : false);
        $loopcarousel = ((0 === strcmp('true', $loopcarousel)) ? true : false);
        $roundimg = ((0 === strcmp('true', $roundimg)) ? true : false);

        $css = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '), $this->settings['base'], $atts);

        wp_enqueue_script('owl-carousel', '', '', '', true);
        wp_enqueue_style('owl-carousel');

        global $wp;
        $current_url = basename(home_url(add_query_arg(array(), $wp->request)));

        $html = '';

        if (strlen($posttype) > 0) {

            if (0 == strcmp('testimonial', $posttype)) {
                // We only want 3 testimonials and if Christmas we only want the Christmas ones
                if ($christmas) {
                    $args = array('post_type' => 'page', 'category_name' => $posttype, 'orderby' => 'rand', 'posts_per_page' => 3, 'meta_key' => 'is_christmas', 'meta_value' => 'YES');
                } else {
                    $args = array('post_type' => 'page', 'category_name' => $posttype, 'orderby' => 'rand', 'posts_per_page' => 3);
                }
            } else if ('orc_staff_member' === $posttype) {
                if ($homepage) {
                    $args = array('post_type' => $posttype, 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1, 'meta_key' => 'on_home_page', 'meta_value' => '1');
                } else {
                    $args = array('post_type' => $posttype, 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1);
                }
            } else if (0 === strcmp('program-types', $posttype) || 0 === strcmp('make-history', $posttype) || 0 === strcmp('community', $posttype) || 0 === strcmp('tours', $posttype) || 0 === strcmp('videos', $posttype)) {
                $args = array('post_type' => 'page', 'category_name' => $posttype, 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1);
            }
            $the_query = new WP_Query($args);
            $data = array();
            if ($the_query->have_posts()) {
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    $id = get_the_ID();                  
                    $fields = get_post_custom($id);
                    if ('orc_staff_member' === $posttype) {
                        if ($homepage) {
                            if (strlen($fields['on_home_page'][0]) > 0) {
                                $data[] = array('id' => $id, 'name' => get_the_title(), 'homepage' => true, 'job' => $fields['position'][0], 'qualifications' => $fields['qualifications'][0], 'permalink' => get_the_permalink());
                            }
                        } else {
                            if (array_key_exists('qualifications', $fields)) {
                                $data[] = array('id' => $id, 'name' => get_the_title(), 'homepage' => false, 'job' => $fields['position'][0], 'qualifications' => $fields['qualifications'][0], 'permalink' => get_the_permalink());
                            } else {
                                $data[] = array('id' => $id, 'name' => get_the_title(), 'homepage' => false, 'job' => $fields['position'][0], 'permalink' => get_the_permalink());
                            }
                        }
                    } else if (0 === strcmp('program-types', $posttype)) {
                        $data[] = array('id' => $id, 'name' => get_the_title(), 'excerpt' => get_the_excerpt(), 'permalink' => get_the_permalink());
                    } else if (0 === strcmp('testimonial', $posttype)) {
                        $data[] = array('id' => $id, 'name' => get_the_title(), 'testimonial' => get_the_excerpt(), 'city' => $fields['city'][0]);
                    } else if (0 === strcmp('videos', $posttype)) {
                        $data[] = array('id' => $id, 'name' => get_the_title(), 'youtube_id' => $fields['youtube_id'][0]);
                    } else if (0 === strcmp('tours', $posttype)) {
                        $data[] = array('id' => $id, 'name' => get_the_title(), 'excerpt' => get_the_excerpt(), 'permalink' => get_the_permalink());
                    } else if (0 === strcmp('make-history', $posttype) || 0 === strcmp('community', $posttype)) {
                        $data[] = array('id' => $id, 'name' => get_the_title(), 'excerpt' => get_the_excerpt(), 'permalink' => get_the_permalink());
                    }
                }
            }
            wp_reset_query();

            $html = '<div class="' . $css . '">';
            $html .= '<div id="' . $posttype . '-carousel" class="orc-carousel owl-carousel owl-theme aligncenter" data-orcc-width="' . $imgwidth . '" data-orcc-height="' . $imgheight . '" data-orcc-dots="' . $dots . '" data-orcc-nav="' . $prevnext . '" data-orcc-autoplay="' . $autoplay . '" data-orcc-autoplay-timeout="' . $speed . '" data-orcc-loop="' . $loopcarousel . '" data-orcc-cols-desktop="' . $colsdesktop . '" data-orcc-cols-tablet="' . $colstablet . '" data-orcc-cols-mobile="' . $colsmobile . '" data-orcc-margin="' . $margin . '" data-orcc-stop-on-hover="' . $pauseonhover . '">';
            if (count($data) > 0) {
                foreach ($data as $element) {
                    if (array_key_exists('permalink', $element)) {
                        $currentpage = basename($element['permalink']);
                        $notoncurrentpage = (0 === strcmp($current_url, $currentpage) ? false : true);
                    } else {
                        $notoncurrentpage = true;
                    }
                    if ('orc_staff_member' === $posttype) {
                        
                        $qualifications = '';
                        if (array_key_exists('qualifications', $element)) {
                            $qualifications = '<hr style="margin:5px 0;">' . $element['qualifications'];
                        }
                        $html .= '<div class="owl-item aligncenter">';
                        if ($linkimage && $notoncurrentpage) {
                            $html .= '<a href="' . $element['permalink'] . '" title="View ' . $element['name'] . '">';
                        }
                        $html .= '<figure class="item">';
                        $html .= do_shortcode('[orc_image postid=' . $element['id'] . ' roundimg="' . $roundimg . '" width=' . $imgwidth . ' height=' . $imgheight . ' imgclass=' . $additionalclass . ']');
                        $html .= '<figcaption>' . $element['name'] . '<br /><small>' . $element['job'] . $qualifications . '</small></figcaption>';
                        $html .= '</figure>';
                        if ($linkimage && $notoncurrentpage) {
                            $html .= '</a>';
                        }
                        $html .= '</div>';
                    } else if (0 === strcmp('program-types', $posttype)) {
                        $html .= '<div class="owl-item" class="aligncenter" style="width:100%;height:100%;">';
                        if ($linkimage && $notoncurrentpage) {
                            $html .= '<a href="' . $element['permalink'] . '" title="View ' . $element['name'] . '">';
                        }
                        $html .= '<figure class="item">';
                        $html .= do_shortcode('[orc_image postid=' . $element['id'] . ' roundimg="' . $roundimg . '" width=' . $imgwidth . ' height=' . $imgheight . ' imgclass=' . $additionalclass . ']');
                        if ($linkimage && $notoncurrentpage) {
                            $html .= '</a>';
                        }
                        $html .= '<figcaption>';
                        $html .= '<h2>' . $element['name'] . '</h2><p>' . $element['excerpt'] . '</p>';
                        if ($linkimage && $notoncurrentpage && $homepage) {
                            $html .= '<a href="' . $element['permalink'] . '" title="View ' . $element['name'] . '">';
                            $html .= do_shortcode('[vc_btn title="View ' . $element['name'] . '" style="outline" color="black" size="lg" align="center" button_block="true"]');
                            $html .= '</a>';
                        }
                        $html .= '</figcaption>';
                        $html .= '</figure>';
                        $html .= '</div>';
                    } else if (0 == strcmp('testimonial', $posttype)) {
                        $html .= '<div class="owl-item" class="aligncenter" style="width:100%;height:100%;">';
                        $html .= '<blockquote>' . $element['testimonial'];
                        $html .= '<br/><br/><cite>' . $element['name'] . '<br/>' . $element['city'] . '</cite>';
                        $html .= '</blockquote>';
                        $html .= '</div>';
                    } else if (0 == strcmp('videos', $posttype)) {
                        $html .= '<div class="owl-item"  style="width:100%;height:100%;">';
                        $html .= do_shortcode('[vc_video link="https://www.youtube.com/embed/' . $element['youtube_id'] . '" align="center" title="' . $element['name'] . '"]');
                        $html .= '</div>';
                    } else if (0 == strcmp('tours', $posttype)) {
                        $html .= '<figure class="owl-item" class="aligncenter" style="width:100%;">';
                        if ($linkimage && $notoncurrentpage) {
                            $html .= '<a href="' . $element['permalink'] . '" title="View ' . $element['name'] . '">';
                        }
                        $html .= do_shortcode('[orc_image postid=' . $element['id'] . ' roundimg="' . $roundimg . '" width=' . $imgwidth . ' height=' . $imgheight . ' imgclass=' . $additionalclass . ']');
                        if ($linkimage && $notoncurrentpage) {
                            $html .= '</a>';
                        }
                        $html .= '<figcaption>';
                        $html .= '<h4>' . $element['name'] . '</h4>';
                        $html .= '</figcaption>';
                        $html .= '</figure>';
                    } else if (0 == strcmp('make-history', $posttype)) {
                        $html .= '<div class="owl-item aligncenter" style="width:100%;height:100%;">';
                        $html .= '<figure class="item">';
                        if ($linkimage) {
                            $html .= '<a href="' . $element['permalink'] . '" title="View ' . $element['name'] . '">';
                        }
                        $html .= do_shortcode('[orc_image postid=' . $element['id'] . ' roundimg="' . $roundimg . '" width=' . $imgwidth . ' height=' . $imgheight . ' imgclass=' . $additionalclass . ']');
                        if ($linkimage) {
                            $html .= '</a>';
                        }
                        $html .= '<figcaption>' . $element['name'] . '<br /><small>' . $element['excerpt'] . '</small></figcaption>';
                        $html .= '</figure>';
                        $html .= '</div>';
                    } else if (0 == strcmp('community', $posttype)) {
                        $html .= '<div class="owl-item aligncenter" style="width:100%;height:100%;">';
                        $html .= '<figure class="item">';
                        if ($linkimage) {
                            $html .= '<a href="' . $element['permalink'] . '" title="View ' . $element['name'] . '">';
                        }
                        $html .= do_shortcode('[orc_image postid=' . $element['id'] . ' roundimg="' . $roundimg . '" width=' . $imgwidth . ' height=' . $imgheight . ' imgclass=' . $additionalclass . ']');
                        if ($linkimage) {
                            $html .= '</a>';
                        }
                        $html .= '<figcaption>' . $element['name'] . '<br /><small>' . $element['excerpt'] . '</small></figcaption>';
                        $html .= '</figure>';
                        $html .= '</div>';
                    }
                }
            }
            $html .= '</div> <!-- /#post_type-carousel .orc-carousel owl-carousel -->';
            $html .= '</div>';
        }

        return $html;
    }

}

// End Element Class

/**
 * Initialize a new instance of the Carousel
 */
new orcCarousel();

