<?php

/**
 * Element Description: ORC Contact (Phone/Email)
 */
 
// Element Class 
class orcContact extends WPBakeryShortCode {
     
	// Element Initialization
	function __construct() {
		add_action( 'init', array( $this, 'orc_contact_mapping' ) );
		add_shortcode( 'orc_contact', array( $this, 'orc_contact_render' ) );
	}
     
	/**
	 * This will parse the phone number, remove any non numeric character and
	 * if it doesn't start with a 1 add it
	 * 
	 * @param type $number Phone number
	 */
	private function orc_contact_anchor($number) {
		$number = preg_replace('/[^0-9]/i', '', $number);			// Remove non numeric
		if('1' != $number[0]) {
			$number = '1' . $number;										// Add the 1 prefix if required
		}
		
		return $number;
	}
     
	// Element Mapping of parameters
	public function orc_contact_mapping() {

		$contacts = array('Pick a contact type' => '', 'Phone' => 'phone', 'Toll Free' => 'tollfree', 'Mobile/Text' => 'mobile', 'Fax' => 'fax', 'Email Intake' => 'intake', 'Email Communications' => 'communications', 'Email Human Resources' => 'hr', 'Email Alumni Coordinator' => 'alumni', 'Email Website Administrator' => 'website', 'Email Privacy Officer' => 'privacy');
      $alignmentarray = array('Default' => '', 'Left' => 'alignleft', 'Center' => 'aligncenter', 'Right' => 'alignright');
		
		// Stop all if VC is not enabled
		if ( !defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		// Map the block with vc_map()
		vc_map( 
			array(
				'name' => __('ORC Contact', 'text-domain'),									// Human friendly name for the element
				'base' => 'orc_contact',																// MUST be shortcode from above
				'description' => __('Orchard Recovery Center Custom Contact', 'text-domain'),			// Human friendly description for the element
				'category' => __('ORC Custom Elements', 'text-domain'),					// Category for Visual Composer
//				'icon' => get_template_directory_uri().'/assets/img/vc-icon.png',		// Icon to display  ***** TODO
				'params' => array(																	// Parameters for the element

					array(																				// First parameter
						'type' => 'dropdown',															// Text filed
//						'holder' => 'small',																// HTML tag where Visual Composer will store attribuite value in Visual Composer edit mode
//						'class' => 'posttype-class',													// Class name that will be added to the "holder" HTML tag. For backent CSS
						'heading' => __( 'Contact Type', 'text-domain' ),							// Human friendly title for parameter, visible on shortcode's edit screen
						'param_name' => 'contacts',													// Name of the field that the data will be saved under
						'value' => $contacts,					// Default value
						'description' => __( 'Select the contact type', 'text-domain' ),					// Human friendly description of the parameter
						'admin_label' => true,														// Show/Hide value of param in Visual Composer editor
						'weight' => 0,																	// Parameters with greater wight will be rendered first
						'group' => 'Contact Info'													// Group/Tab dividers
					),  

					array(
						'type' => 'checkbox',
						'heading' => __( 'Make Link?', 'text-domain' ),
						'param_name' => 'makelink',
						'value' => __( 'false', 'text-domain' ),
						'description' => __( 'Should the contact be linkable?', 'text-domain' ),
						'admin_label' => true,
						'weight' => 0,
						'group' => 'Contact Info'
					),  

					array(
						'type' => 'checkbox',
						'heading' => __( 'Display Icon?', 'text-domain' ),
						'param_name' => 'fonticon',
						'value' => __( 'false', 'text-domain' ),
						'description' => __( 'Should the contact have a Font Awesome Icon?', 'text-domain' ),
						'admin_label' => true,
						'weight' => 0,
						'group' => 'Contact Info'
					),  

					array(
						'type' => 'textfield',
						'heading' => __( 'Prefix', 'text-domain' ),
						'param_name' => 'prefix',
						'value' => __( '', 'text-domain' ),
						'admin_label' => true,
						'weight' => 0,
						'group' => 'Contact Info'
					),

					array(
						'type' => 'textfield',
						'heading' => __( 'Suffix', 'text-domain' ),
						'param_name' => 'suffix',
						'value' => __( '', 'text-domain' ),
						'admin_label' => true,
						'weight' => 0,
						'group' => 'Contact Info'
					),

               array(
						'type' => 'dropdown',															// Text filed
						'heading' => __( 'Alignment', 'text-domain' ),							// Human friendly title for parameter, visible on shortcode's edit screen
						'param_name' => 'alignment',													// Name of the field that the data will be saved under
						'value' => $alignmentarray,					// Default value
						'admin_label' => true,														// Show/Hide value of param in Visual Composer editor
						'weight' => 0,																	// Parameters with greater wight will be rendered first
						'group' => 'Format'													// Group/Tab dividers
					),  

					array(
						'type' => 'checkbox',
						'heading' => __( 'Hard Break?', 'text-domain' ),
						'param_name' => 'hardbreak',
						'value' => __( 'false', 'text-domain' ),
						'description' => __( 'Should there be a hard break after the contact?', 'text-domain' ),
						'admin_label' => true,
						'weight' => 0,
						'group' => 'Format'
					),  

               array(
                  'type' => 'colorpicker',
						'heading' => __( 'Font Color', 'text-domain' ),
						'param_name' => 'fontcolor',
                  'description' => __( 'Select custom font color for your element.', 'text-domain' ),
                  'edit_field_class' => 'vc_col-sm-6',
                  'std' => '#000000',
						'admin_label' => true,
						'weight' => 0,
						'group' => 'Format'
               ),

					array(
						'type' => 'textfield',
						'heading' => __( 'Link Class', 'text-domain' ),
						'param_name' => 'linkclass',
                  'description' => __( 'Select the link color/hover class.', 'text-domain' ),
						'value' => __( '', 'text-domain' ),
						'admin_label' => true,
						'weight' => 0,
						'group' => 'Format'
					),

				)
			)
		);
	} 
     
	// Element HTML
	public function orc_contact_render( $atts ) {
		// Params extraction

		extract(
			shortcode_atts(
				array(
					'contacts' => '',
					'makelink'   => 'false',
					'fonticon' => 'false',
					'prefix' => '',
					'suffix' => '',
					'alignment' => '',
					'fontcolor' => '',
					'linkclass' => '',
					'hardbreak' => 'false'
				), 
				$atts
			)
		);

		$html = '';
		$data = '';
		$fontcoloranchor = '';
		
		$content_links_color_hover = esc_attr(engage_option('content_links_color_hover'));
		
		if(strlen($contacts) > 0) {

			if(0 === strcmp('true', $makelink)) {
				$makelink = true;
			} else {
				$makelink = false;
			}

			if(0 === strcmp('true', $fonticon)) {
				$fonticon = true;
			} else {
				$fonticon = false;
			}

			if(strlen($prefix) > 0) {
				$prefix .= ' ';
			}

			if(strlen($suffix) > 0) {
				$suffix = ' ' . $suffix . ' ';
			}

			if(0 === strlen($fontcolor)) {
				$fontcolor = 'style="color:' . $content_links_color_hover . ';"';
			} else {
				$fontcoloranchor = 'onMouseOut="this.style.color=\'' . $fontcolor .'\'" onMouseOver="this.style.color=\'' . $content_links_color_hover . '\'" style="color:' . $fontcolor . ';"';
				$fontcolor = 'style="color:' . $fontcolor . ';"';
			}

			if(0 === strcmp('true', $hardbreak)) {
				$hardbreak = '<br>';
			} else {
				$hardbreak = '';
			}

			if(strlen($alignment) > 0) {
				$padding = ' padding:0;';
			} else {
				$padding = '';
			}

			if(0 === strcmp('phone', $contacts)) {
				if($fonticon) {
					$fonticon = '<i class="fa fa-phone"></i> ';
				} else {
					$fonticon = '';
				}
				$link = get_option('orc_options_phone');
				$anchor = 'href="tel:+' . $this->orc_contact_anchor($link) . '" title = "Call Us" rel="nofollow"';
			} else if(0 === strcmp('tollfree', $contacts)) {
				if($fonticon) {
					$fonticon = '<i class="fa fa-phone"></i> ';
				} else {
					$fonticon = '';
				}
				$link = get_option('orc_options_tollfree');
				$anchor = 'href="tel:+' . $this->orc_contact_anchor($link) . '" title = "Call Us Toll Free" rel="nofollow"';
			} else if(0 === strcmp('mobile', $contacts)) {
				if($fonticon) {
					$fonticon = '<i class="fa fa-keyboard-o"></i> ';
				} else {
					$fonticon = '';
				}
				$link = get_option('orc_options_text');
				$anchor = 'href="sms:+' . $this->orc_contact_anchor($link) . '" title = "Text Us" rel="nofollow"';
			} else if(0 === strcmp('fax', $contacts)) {
				if($fonticon) {
					$fonticon = '<i class="fa fa-fax"></i> ';
				} else {
					$fonticon = '';
				}
				$link = get_option('orc_options_fax');
				$anchor = 'href="tel:+' . $this->orc_contact_anchor($link) . '" title = "Fax Us" rel="nofollow"';
			} else if(0 === strcmp('intake', $contacts)) {
				if($fonticon) {
					$fonticon = '<i class="fa fa-send"></i> ';
				} else {
					$fonticon = '';
				}
				$link = 'Email Orchard Recovery';
				$anchor = 'href="#" title = "Email Orchard Recovery"';
				$linkclass .= ' doemail';
				$data = ' data-whotocontact="intakedepartment" ';
			} else if(0 === strcmp('communications', $contacts)) {
				if($fonticon) {
					$fonticon = '<i class="fa fa-send"></i> ';
				} else {
					$fonticon = '';
				}
				$link = 'Email Orchard Communications';
				$anchor = 'href="#" title = "Email Orchard Communications"';
				$linkclass .= ' doemail';
				$data = ' data-whotocontact="communicationsdepartment" ';
			} else if(0 === strcmp('hr', $contacts)) {
				if($fonticon) {
					$fonticon = '<i class="fa fa-send"></i> ';
				} else {
					$fonticon = '';
				}
				$link = 'Email Orchard Human Resources';
				$anchor = 'href="#" title = "Email Human Resources"';
				$linkclass .= ' doemail';
				$data = ' data-whotocontact="hrdepartment" ';
			} else if(0 === strcmp('alumni', $contacts)) {
				if($fonticon) {
					$fonticon = '<i class="fa fa-send"></i> ';
				} else {
					$fonticon = '';
				}
				$link = 'Email Orchard Alumni Coordinator';
				$anchor = 'href="#" title = "Email Alumni Coordinator"';
				$linkclass .= ' doemail';
				$data = ' data-whotocontact="alumnidepartment" ';
			} else if(0 === strcmp('website', $contacts)) {
				if($fonticon) {
					$fonticon = '<i class="fa fa-send"></i> ';
				} else {
					$fonticon = '';
				}
				$link = 'Email Website Administrator';
				$anchor = 'href="#" title = "Email Website Administrator"';
				$linkclass .= ' doemail';
				$data = ' data-whotocontact="websitedepartment" ';
			} else if(0 === strcmp('privacy', $contacts)) {
				if($fonticon) {
					$fonticon = '<i class="fa fa-send"></i> ';
				} else {
					$fonticon = '';
				}
				$link = 'Email Privacy Officer';
				$anchor = 'href="#" title = "Email Privacy Officer"';
				$linkclass .= ' doemail';
				$data = ' data-whotocontact="privacydepartment" ';
			}
			if(0 === strlen($alignment)) {
				$html = '<span ' . $fontcolor . ' ';
			} else {
				$html = '<div class="' . $alignment . '" ' . $fontcolor . ' ';
			}
			$html .= ' style="' . $padding . '">' . $fonticon . $prefix;
			if($makelink) {
				if(strlen($linkclass) > 0) {
					$linkclass = ' class="' . $linkclass . '" ';
				}
				$html .= '<a ' . $fontcoloranchor . ' ' . $linkclass . ' ' . $anchor . $data . '>' . $link . '</a>';
			} else {
				$html .= '<span ' . $fontcolor . '>' . $link . "</span>";
			}
			$html .= $suffix;
			if(0 === strlen($alignment)) {
				$html .= '</span>';
			} else {
				$html .= '</div>';
			}
			$html .= $hardbreak;
		}

		return $html;
     
	}
	
} // End Element Class

/**
 * Initialize a new instance of the Contact shortcodes
 */
new orcContact();

function orc_wpcf7_form_elements( $form ) {
	$form = do_shortcode( $form );
	return $form;
}
add_filter( 'wpcf7_form_elements', 'orc_wpcf7_form_elements' );
