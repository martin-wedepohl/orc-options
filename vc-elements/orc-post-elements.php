<?php

/**
 * Element Description: ORC Post
 */
// Element Class 
class orcPost extends WPBakeryShortCode {

    // Element Initialization
    function __construct() {
        add_action('init', array($this, 'orc_post_mapping'));
        add_shortcode('orc_post', array($this, 'orc_post_render'));
    }

    // Element Mapping of parameters
    public function orc_post_mapping() {

        $posts = array('Pick a post type' => '', 'Careers' => 'careers', 'Press Media Posts' => 'press-media', 'Testimonials' => 'testimonial');
        $alignmentarray = array('Left' => 'alignleft', 'Center' => 'aligncenter', 'Right' => 'alignright');
        $colsarray = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

        // Stop all if VC is not enabled
        if (!defined('WPB_VC_VERSION')) {
            return;
        }

        // Map the block with vc_map()
        vc_map(
                array(
                    'name' => __('ORC Post Type', 'text-domain'), // Human friendly name for the element
                    'base' => 'orc_post', // MUST be shortcode from above
                    'description' => __('Orchard Recovery Center Custom Posts', 'text-domain'), // Human friendly description for the element
                    'category' => __('ORC Custom Elements', 'text-domain'), // Category for Visual Composer
//				'icon' => get_template_directory_uri().'/assets/img/vc-icon.png',					// Icon to display  ***** TODO
                    'params' => array(// Parameters for the element

                        array(// First parameter
                            'type' => 'dropdown', // Dropdown box
//						'holder' => 'small',																		// HTML tag where Visual Composer will store attribuite value in Visual Composer edit mode
//						'class' => 'posttype-class',															// Class name that will be added to the "holder" HTML tag. For backent CSS
                            'heading' => __('Post Type', 'text-domain'), // Human friendly title for parameter, visible on shortcode's edit screen
                            'param_name' => 'posts', // Name of the field that the data will be saved under
                            'value' => $posts, // Default value
                            'description' => __('Select the post type', 'text-domain'), // Human friendly description of the parameter
                            'admin_label' => true, // Show/Hide value of param in Visual Composer editor
                            'weight' => 0, // Parameters with greater wight will be rendered first
                            'group' => 'Post type'                 // Group/Tab dividers
//						'dependency' => array(																	// Only show if dependency is met
//							'element' => 'posts',																// on this element
//							'value' => array( 'press-media', 'testimonial' ),							// with these values
//						),
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => __('Alignment', 'text-domain'),
                            'param_name' => 'alignment_left',
                            'value' => $alignmentarray,
                            'weight' => 0,
                            'group' => 'Responsive Left Options',
                            'dependency' => array(
                                'element' => 'posts',
                                'value' => array('press-media', 'testimonial'),
                            ),
                            'std' => 'aligncenter',
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => __('Alignment', 'text-domain'),
                            'param_name' => 'alignment_right',
                            'value' => $alignmentarray,
                            'weight' => 0,
                            'group' => 'Responsive Right Options',
                            'dependency' => array(
                                'element' => 'posts',
                                'value' => array('press-media', 'testimonial'),
                            ),
                            'std' => 'aligncenter',
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => __('Width', 'js_composer'),
                            'param_name' => 'width_left',
                            'value' => array(
                                __('1 column - 1/12', 'js_composer') => '1',
                                __('2 columns - 1/6', 'js_composer') => '2',
                                __('3 columns - 1/4', 'js_composer') => '3',
                                __('4 columns - 1/3', 'js_composer') => '4',
                                __('5 columns - 5/12', 'js_composer') => '5',
                                __('6 columns - 1/2', 'js_composer') => '6',
                                __('7 columns - 7/12', 'js_composer') => '7',
                                __('8 columns - 2/3', 'js_composer') => '8',
                                __('9 columns - 3/4', 'js_composer') => '9',
                                __('10 columns - 5/6', 'js_composer') => '10',
                                __('11 columns - 11/12', 'js_composer') => '11',
                                __('12 columns - 1/1', 'js_composer') => '12',
                            ),
                            'group' => __('Responsive Left Options', 'js_composer'),
                            'description' => __('Select column width.', 'js_composer'),
                            'dependency' => array(
                                'element' => 'posts',
                                'value' => array('press-media', 'testimonial'),
                            ),
                            'std' => '12',
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => __('Width', 'js_composer'),
                            'param_name' => 'width_right',
                            'value' => array(
                                __('1 column - 1/12', 'js_composer') => '1',
                                __('2 columns - 1/6', 'js_composer') => '2',
                                __('3 columns - 1/4', 'js_composer') => '3',
                                __('4 columns - 1/3', 'js_composer') => '4',
                                __('5 columns - 5/12', 'js_composer') => '5',
                                __('6 columns - 1/2', 'js_composer') => '6',
                                __('7 columns - 7/12', 'js_composer') => '7',
                                __('8 columns - 2/3', 'js_composer') => '8',
                                __('9 columns - 3/4', 'js_composer') => '9',
                                __('10 columns - 5/6', 'js_composer') => '10',
                                __('11 columns - 11/12', 'js_composer') => '11',
                                __('12 columns - 1/1', 'js_composer') => '12',
                            ),
                            'group' => __('Responsive Right Options', 'js_composer'),
                            'description' => __('Select column width.', 'js_composer'),
                            'dependency' => array(
                                'element' => 'posts',
                                'value' => array('press-media', 'testimonial'),
                            ),
                            'std' => '12',
                        ),
                        array(
                            'type' => 'column_offset',
                            'heading' => __('Responsiveness', 'js_composer'),
                            'param_name' => 'offset_left',
                            'group' => __('Responsive Left Options', 'js_composer'),
                            'description' => __('Adjust column for different screen sizes. Control width, offset and visibility settings.', 'js_composer'),
                            'dependency' => array(
                                'element' => 'posts',
                                'value' => array('press-media', 'testimonial'),
                            ),
                        ),
                        array(
                            'type' => 'column_offset',
                            'heading' => __('Responsiveness', 'js_composer'),
                            'param_name' => 'offset_right',
                            'group' => __('Responsive Right Options', 'js_composer'),
                            'description' => __('Adjust column for different screen sizes. Control width, offset and visibility settings.', 'js_composer'),
                            'dependency' => array(
                                'element' => 'posts',
                                'value' => array('press-media', 'testimonial'),
                            ),
                        ),
                        array(
                            'type' => 'css_editor',
                            'heading' => __('CSS box', 'js_composer'),
                            'param_name' => 'css',
                            'group' => __('Design Options', 'js_composer'),
                            'dependency' => array(
                                'element' => 'posts',
                                'value' => array('press-media', 'testimonial'),
                            ),
                        ),
                    )
                )
        );
    }

    // Element HTML
    public function orc_post_render($atts) {
        // Params extraction

        extract(
                shortcode_atts(
                        array(
                            'posts' => '',
                            'alignment_left' => 'aligncenter',
                            'alignment_right' => 'aligncenter',
                            'width_left' => '12',
                            'width_right' => '12',
                            'offset_left' => '',
                            'offset_right' => '',
                            'css' => '',
                        ),
                        $atts
                )
        );

        $css = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '), $this->settings['base'], $atts);

        $html = '';

        if (strlen($posts) > 0) {

            if (0 === strcmp('press-media', $posts)) {
                $args = array('post_type' => 'page', 'category_name' => $posts, 'orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => -1);
            } else if (0 === strcmp('testimonial', $posts)) {
                $args = array('post_type' => 'page', 'category_name' => $posts, 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1);
            } else if (0 === strcmp('careers', $posts)) {
                $args = array('post_type' => 'page', 'category_name' => $posts, 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1);
                $allcareerstags = get_tags(array('hide_empty' => false, 'name__like' => 'career', 'get' => 'all'));
                $thecareers = array();
                foreach ($allcareerstags as $career) {
                    $thecareers[$career->slug] = $career->description;
                }
                asort($thecareers);
                $html .= '<p>We employ people in the following areas:</p>';
                $html .= '<ul>';
                foreach ($thecareers as $slug => $description) {
                    $html .= '<li>' . $description . ' <span id="' . $slug . '-span" class="' . $slug . '">POSITION AVAILABLE</span></li>';
                }
                $html .= '</ul>';
                $html .= '<h3 class="aligncenter">Current Available Positions</h3>';
                foreach ($thecareers as $slug => $description) {
                    $html .= '<div id="' . $slug . '">';
                    $html .= '<h4 id="' . $slug . '-header">' . $description . '</h4>';
                    $html .= '</div>';
                }
            }
            $the_query = new WP_Query($args);
            $data = array();
            $scriptdata = '';
            if ($the_query->have_posts()) {
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    $id = get_the_ID();
                    if (0 === strcmp('careers', $posts)) {
                        $tagsarray = get_the_tags();
                        $tagsarray1 = get_the_tags(array('orderby' => 'name', 'hide_empty' => false, 'name__like' => 'career'));
                        if (count($tagsarray) > 0) {
                            $spanname = $tagsarray[0]->slug . '-span';
                            $divname = $tagsarray[0]->slug;
                            $headername = $tagsarray[0]->slug . '-header';
                            $scriptdata .= 'document.getElementById("' . $spanname . '").style.display = "inline-block";';
                            $scriptdata .= 'var divnode = document.getElementById("' . $divname . '");';
                            $scriptdata .= 'divnode.style.display = "block";';
                            $scriptdata .= 'var node=document.createElement("ul");';
                            $scriptdata .= 'var itemnode=document.createElement("li");';
                            $scriptdata .= 'var linknode=document.createElement("a");';
                            $scriptdata .= 'var textnode=document.createTextNode("' . get_the_title() . '");';
                            $scriptdata .= 'linknode.appendChild(textnode);';
                            $scriptdata .= 'linknode.href = "' . get_the_permalink() . '";';
                            $scriptdata .= 'itemnode.appendChild(linknode);';
                            $scriptdata .= 'node.appendChild(itemnode);';
                            $scriptdata .= 'divnode.appendChild(node);';
                        }
                    } else {
                        $fields = get_post_custom($id);
                        if (0 === strcmp('testimonial', $posts)) {
                            $html .= '<section id="testimonial-' . $id . '" class="' . $css . '">';
                            $html .= '<div class="vc_row wpb_row vc_row-fluid">';
                            $html .= '<div class="wpb_column vc_column_container vc_col-sm-' . $width_left . ' ' . $offset_left . ' ' . $alignment_left . '">';
                            $html .= '<p><br />' . get_the_title() . '<br />' . $fields['city'][0] . '</p>';
                            $html .= '</div>';
                            $html .= '<div class="wpb_column vc_column_container vc_col-sm-' . $width_right . ' ' . $offset_right . ' ' . $alignment_right . '">';
                            $html .= '<blockquote>' . apply_filters('the_content', get_the_content()) . '</blockquote>';
                            $html .= '</div>';
                            $html .= '</div>';
                            $html .= '</section>';
                        } else if (0 === strcmp('press-media', $posts)) {
                            $html .= '<section id="press-media-' . $id . '" class="' . $css . '">';
                            $html .= '<div class="vc_row wpb_row vc_row-fluid">';
                            $html .= '<div class="wpb_column vc_column_container vc_col-sm-' . $width_left . ' ' . $offset_left . ' ' . $alignment_left . '">';
                            if (array_key_exists('url', $fields)) {
                                if (strlen($fields['url'][0]) > 0) {
                                    $html .= '<a href="' . $fields['url'][0] . '" target="_blank" title="' . get_the_title() . '">';
                                } else {
                                    $html .= '<a href="' . get_the_permalink($id) . '" title="' . get_the_title() . '">';
                                }
                            } else {
                                $html .= '<a href="' . get_the_permalink($id) . '" title="' . get_the_title() . '">';
                            }
                            if (has_post_thumbnail()) {
                                $html .= do_shortcode('[orc_image postid=' . $id . ' height=300 width=300 bottom=20]');
                                get_the_post_thumbnail($id, 'medium');
                            }
                            $html .= '</a>';
                            $html .= '</div>';
                            $html .= '<div class="wpb_column vc_column_container vc_col-sm-' . $width_right . ' ' . $offset_right . ' ' . $alignment_right . '">';
                            $html .= '<h2><a href="' . get_the_permalink($id) . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h2><p>' . $fields['featured_on'][0] . ' - ' . get_post_time('F j, Y') . '</p>';
                            $html .= apply_filters('the_content', get_the_excerpt());
                            if (array_key_exists('url', $fields)) {
                                if (strlen($fields['url'][0]) > 0) {
                                    $html .= '<a href="' . $fields['url'][0] . '" target="_blank" title="' . get_the_title() . '">';
                                } else {
                                    $html .= '<a href="' . get_the_permalink($id) . '" title="' . get_the_title() . '">';
                                }
                            } else {
                                $html .= '<a href="' . get_the_permalink($id) . '" title="' . get_the_title() . '">';
                            }
                            $html .= '<p>Read More ...</p>';
                            $html .= '</a>';
                            $html .= '</div>';
                            $html .= '</div>';
                            $html .= '</section>';
                        }
                    }
                }
            }
            if (strlen($scriptdata) > 0) {
                $html .= '<script>' . $scriptdata . '</script>';
            }
        }

        return $html;
    }

}

// End Element Class

/**
 * Initialize a new instance of the Post
 */
new orcPost();
