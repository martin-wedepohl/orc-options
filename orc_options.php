<?php
/*
  Plugin Name: Orchard Recovery Center Options
  Plugin URI:
  Description: Optional information used in Orchard Recovery Center website
  Version: 1.4.0
  Author: Martin Wedepohl
  Author URI:
  License: GPLv2 or later
 */

/**
 * Called on plugin activation
 * Enables the cron to delete messages in the flamingo plugin
 */
function orc_options_activation() {
    if (wp_next_scheduled('orc_options_delete_emails')) {
        wp_clear_scheduled_hook('orc_options_delete_emails');
    }
    wp_schedule_event(time(), 'twicedaily', 'orc_options_delete_emails');
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'orc_options_activation');

/*
 * Called on plugin deactivation
 * Turn off the cron to delete messages in the flamingo plugin
 */

function orc_options_deactivation() {
    $timestamp = wp_next_scheduled('orc_options_delete_emails');
    wp_unschedule_event($timestamp, 'orc_options_delete_emails');
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'orc_options_deactivation');

function theme_prefix_rewrite_flush() {
    flush_rewrite_rules();
}
add_action('theme_prefix_rewrite_flush', 'theme_prefix_rewrite_flush');

/**
 * Function to remove messages and users from the flamingo plugin data after a certain amound of days days
 */
function orc_options_delete_emails_hook() {
    global $wpdb;

    // Get the number of days to hold the emails for or 30 days if not set
    $orc_options_email_delete_days = get_option('orc_options_email_delete_days', 30);

    /**
     * Get an array of ID and post_content
     * From the posts table where type post_type is a flamingo_inbound
     * with a post_date older than then umber of days set by the plugin
     * when the cron starts  
     */
    $postdataarray = array();
    foreach ($wpdb->get_results($wpdb->prepare('SELECT ID, post_content FROM ' . $wpdb->posts . ' WHERE post_type="flamingo_inbound" AND post_date < DATE(NOW() - INTERVAL %d DAY) + INTERVAL 0 SECOND;', array($orc_options_email_delete_days))) as $id => $postid) {
        $postdataarray[] = array('ID' => $postid->ID, 'post_content' => $postid->post_content);
    }

    /**
     * Loop through all the expired emails stored using Contact Form 7 and Flamingo
     */
    foreach ($postdataarray as $postid) {
        /**
         * Get the eamil address from the post_content
         */
        $post_content = $postid['post_content'];
        preg_match('/(\b[a-z0-9._%+-]+@[a-z0-9._%+-]+.[a-z0-9._%+-]+\b)/i', $post_content, $email);

        /**
         * If we have an email address
         * Delete the postmeta and posts with the appropriate ID
         */
        if (is_array($email)) {

            $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->postmeta . ' WHERE post_id=%d', array($postid['ID'])));
            $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->posts . ' WHERE ID=%d', array($postid['ID'])));

            /**
             * Don't delete email addresses of loggedin users
             * otherwise if there are no more posts from that email address, delete the user
             */
            $isloggedinuser = $wpdb->get_results($wpdb->prepare('SELECT ID FROM ' . $wpdb->users . ' WHERE user_email=%s', array($email[0])));
            if (!array_key_exists(0, $isloggedinuser)) {
                $numposts = $wpdb->get_results($wpdb->prepare('SELECT COUNT(ID) AS numposts FROM ' . $wpdb->posts . ' WHERE post_type="flamingo_inbound" AND post_content LIKE %s', array('%' . $email[0] . '%')));
                if (0 == $numposts[0]->numposts) {
                    $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->posts . ' WHERE post_title=%s', array($email[0])));
                }
            }
        } // if(is_array($email))
    } // foreach($postdataarray as $postid)
}
add_action('orc_options_delete_emails', 'orc_options_delete_emails_hook');

/**
 * Function called to register settings used in the plugin
 */
function register_orc_options_settings() {
    register_setting('orc_options_option-group', 'orc_options_phone');
    register_setting('orc_options_option-group', 'orc_options_tollfree');
    register_setting('orc_options_option-group', 'orc_options_text');
    register_setting('orc_options_option-group', 'orc_options_fax');
    register_setting('orc_options_option-group', 'orc_options_intake');
    register_setting('orc_options_option-group', 'orc_options_communications');
    register_setting('orc_options_option-group', 'orc_options_hr');
    register_setting('orc_options_option-group', 'orc_options_alumni');
    register_setting('orc_options_option-group', 'orc_options_mainvideo');
    register_setting('orc_options_option-group', 'orc_options_xmasvideo');
    register_setting('orc_options_option-group', 'orc_options_facebookappid');
    register_setting('orc_options_option-group', 'orc_options_facebookpixel');
    register_setting('orc_options_option-group', 'orc_options_bing');
    register_setting('orc_options_option-group', 'orc_options_linkedin');
    register_setting('orc_options_option-group', 'orc_options_twitter');
    register_setting('orc_options_option-group', 'orc_options_org_schema');
    register_setting('orc_options_option-group', 'orc_options_local_schema');
    register_setting('orc_options_option-group', 'orc_options_wpcf7id');
    register_setting('orc_options_option-group', 'orc_options_wpcf7id_comm');
    register_setting('orc_options_option-group', 'orc_options_wpcf7id_hr');
    register_setting('orc_options_option-group', 'orc_options_wpcf7id_alumni');
    register_setting('orc_options_option-group', 'orc_options_wpcf7id_website');
    register_setting('orc_options_option-group', 'orc_options_wpcf7id_privacy');
    register_setting('orc_options_option-group', 'orc_options_google_analytics');
    register_setting('orc_options_option-group', 'orc_options_email_delete_days');
    register_setting('orc_options_option-group', 'orc_options_staff_administrative_excerpt');
    register_setting('orc_options_option-group', 'orc_options_staff_clinical_excerpt');
    register_setting('orc_options_option-group', 'orc_options_staff_medical_excerpt');
    register_setting('orc_options_option-group', 'orc_options_staff_recovery_excerpt');
    register_setting('orc_options_option-group', 'orc_options_staff_support_excerpt');
    register_setting('orc_options_option-group', 'orc_options_staff_wellness_excerpt');
    register_setting('orc_options_option-group', 'orc_options_delete_wpb_key');
    
}
add_action('admin_init', 'register_orc_options_settings');

/**
 * Called to add the plugin settings to the plugins page
 * 
 * @param type $links Array of links to settings pages
 * 
 * @return array
 */
function orc_options_add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=orc_options_settings">' . __('Settings') . '</a>';
    $info_link = '<a href="admin.php?page=orc_options_info">' . __('Information') . '</a>';
    array_push($links, $settings_link);
    array_push($links, $info_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'orc_options_add_settings_link');

/**
 * Called to enqueue the admin styles used in this plugin
 */
function orc_options_admin_styles() {
    if (is_admin()) {
        wp_enqueue_style('orc_options', plugins_url('dist/css/orc_options.min.css', __FILE__));
    }
}
add_action('admin_enqueue_scripts', 'orc_options_admin_styles');

/**
 * Add any scripts used by this plugin
 */
function orc_options_add_scripts() {
    wp_enqueue_script('orc-videos', plugins_url('dist/js/orc.videos.min.js', __FILE__), array('jquery'), '1.0.0.0', true);
    wp_enqueue_script('orc-contacthandler', plugins_url('dist/js/orc.contacthandler.min.js', __FILE__), array('jquery'), '1.0.0.0', true);
    
    if ( is_page( 'staff' ) ) {
        wp_enqueue_script('orc-staff', plugins_url('dist/js/orc.staff.min.js', __FILE__), array('jquery'), '1.0.0.0', true);
        $excerpts = array(
            'administrative' => get_option('orc_options_staff_administrative_excerpt'),
            'clinical' => get_option('orc_options_staff_clinical_excerpt'),
            'medical' => get_option('orc_options_staff_medical_excerpt'),
            'recovery' => get_option('orc_options_staff_recovery_excerpt'),
            'support' => get_option('orc_options_staff_support_excerpt'),
            'wellness' => get_option('orc_options_staff_wellness_excerpt'),
        );
        wp_localize_script('orc-staff', 'staffexcerpt', $excerpts);
    }

    /* Pass Video URL's into the script to display the videos */
    $videourls = array('mainVideo' => get_option('orc_options_mainvideo'), 'xmasVideo' => get_option('orc_options_xmasvideo'));
    wp_localize_script('orc-videos', 'videourls', $videourls);

    // Pass the contact form permalink to the script
    wp_localize_script('orc-contacthandler', 'contactdata', array('contactuspage' => esc_url(get_permalink(get_page_by_title('Contact Us')))));
}
add_action('wp_enqueue_scripts', 'orc_options_add_scripts');

/**
 * Include custom Visual Composer Elements
 */
function orc_custom_before_init_actions() {
    // Include new custom element and shortcodes
    require_once plugin_dir_path(__FILE__) . 'includes/shortcodes/orc-options-shortcodes.php';
    require_once plugin_dir_path(__FILE__) . 'includes/vc-elements/orc-carousel-elements.php';
    require_once plugin_dir_path(__FILE__) . 'includes/vc-elements/orc-contact-elements.php';
    require_once plugin_dir_path(__FILE__) . 'includes/vc-elements/orc-post-elements.php';
}
add_action('vc_before_init', 'orc_custom_before_init_actions');

/**
 * Called to display the settings page
 */
function orc_options_settings() {
    add_menu_page('Orchard Recovery Center Options Settings', 'ORC Options', 'edit_posts', 'orc_options', 'orc_options_display_settings', 'dashicons-lightbulb', 25);

    add_submenu_page('orc_options', 'Orchard Recovery Center Custom Post Types Settings', 'Settings', 'edit_posts', 'orc_options_settings', 'orc_options_display_settings');
    add_submenu_page('orc_options', 'Orchard Recovery Center Custom Post Types Information', 'Information', 'edit_posts', 'orc_options_info', 'orc_options_display_info');
}
add_action('admin_menu', 'orc_options_settings');

/**
 * Display the information on the plugin
 */
function orc_options_display_info() {
    $html = '
	<h1>Orchard Recovery Options</h1>
	<h2>Contains the following options</h2>
	<ul class="orc_options_list">
	   <li>
			<h3>Phone Numbers:</h3><br/>
			<ul>
				<li>Local Phone Number - Greater Vancouver Area Phone Number</li>
				<li>Toll Free Phone Number - Toll Free North American Phone Number</li>
				<li>Text/Mobile Number - Number to receive text messages from the website on</li>
				<li>Fax Number - Greater Vancouver Area Fax Number</li>
			</ul>
		</li>
	   <li>
			<h3>Email Addresses (actually the Contact Form 7 ID\'s for each email address):</h3><br/>
			<ul>
				<li>Contact Form 7 ID (Intake) - Admissions Contact Form</li>
				<li>Contact Form 7 ID (Communications) - Communications/Social Media Contact Form</li>
				<li>Contact Form 7 ID (Human Resources) - Human Resources Contact Form</li>
				<li>Contact Form 7 ID (Alumni) - Alumni Contact Form</li>
				<li>Contact Form 7 ID (Website) - Website Administrator Contact Form</li>
				<li>Contact Form 7 ID (Privacy) - Privacy Contact Form</li>
			</ul>
		</li>
	   <li>
			<h3>Number of days to keep Contact Form 7 Emails</h3><br/>
			<ul>
				<li>Number of days before the emails are deleted</li>
			</ul>
		</li>
	   <li>
			<h3>YouTube URL\'s for Website embedded Video\'s:</h3><br/>
			<ul>
				<li>Main Video ID (shown on Home and Media Pages)</li>
				<li>Xmas Video ID (shown on the Home Page at Christmas)</li>
			</ul>
		</li>
	   <li>
			<h3>Google:</h3><br/>
			<ul>
				<li>Google Analytics Tracking Code</li>
			</ul>
		</li>
	   <li>
			<h3>Facebook:</h3><br/>
			<ul>
				<li>Facebook App ID (for displaying Orchard Recovery Center Facebook Feed on the Media Page)</li>
				<li>Facebook Pixel Code</li>
			</ul>
		</li>
	   <li>
			<h3>Bing:</h3><br/>
			<ul>
				<li>Bing Tracking Code</li>
			</ul>
		</li>
	   <li>
			<h3>LinkedIn:</h3><br/>
			<ul>
				<li>LinkedIn Partner Code</li>
			</ul>
		</li>
	   <li>
			<h3>Twitter:</h3><br/>
			<ul>
				<li>Twitter Universal Website Tag</li>
			</ul>
		</li>
	   <li>
			<h3>Schema\':</h3><br/>
			<ul>
				<li>Organization Schema (Displayed on Home Page ONLY)</li>
				<li>Local Business Schema (Displayed on ALL Pages)</li>
			</ul>
		</li>
	   <li>
			<h3>Staff Excerpts:</h3><br/>
			<ul>
                <li>Administrative</li>
                <li>Clinical</li>
				<li>Medical</li>
                <li>Recovery Coach</li>
                <li>Support</li>
                <li>Wellness</li>
			</ul>
		</li>
	</ul>
	<h2>Shortcodes</h2>
	<ul class="orc_options_list">
		<li>
			<h3>Create an image link with srcset information for the Post/Page Featured Image - needed mainly for svg files<br/><br/>[orc_image postid=P rounding="O" width="W" height="H"]</h3>
			<ul class="orc_options_list">
				<li>P = Post/Page ID (REQUIRED)</li>
				<li>O = true/false for rounded image - Default = false</li>
				<li>W,H = Width/Hight of the image in pixels - Default = thumbnail width/hight</li>
			</ul>
		</li>
		<li>
			<h3>Display a Facebook feed (requires Facebook App ID be set)<br/><br/>[orc_facebook height="H" width=W tabs="T" small_header="S" hide_cover="D" show_facepile="F"]</h3>
			<ul class="orc_options_list"><br>
				<li>H,W = Height/Width of feed - Default = 500px high, 340 px wide</li>
				<li>T = Facebook Tabs to show - Default = timeline,events</li>
				<li>S = Small feed header - Default = false</li>
				<li>D = Hide feed cover - Default = false</li>
				<li>F = Show faces of people who like page - Default = true</li>
			</ul>
		</li>
		<li>
			<h3>Display the family programming from the events<br/><br/>[orc_family_programs title="T" monthsbefore="B" monthsafter="A"]</h3>
			<ul class="orc_options_list"><br>
				<li>T = Title for the display - Default = ""</li>
				<li>B = Number of months to diplay before current date - Default = 0</li>
				<li>A = Number of months to display after current date - Default = 2</li>
			</ul>
		</li>
		<li>
			<h3>Display the department that is being emailed<br/><br/>[orc_department emailto="D"]</h3>
			<ul class="orc_options_list"><br>
				<li>D = Department (intakedepartment, communicationsdepartment, hrdepartment) - Default = ""</li>
			</ul>
		</li>
		<li>
			<h3>Get the contact form ID that is being emailed<br/><br/>[orc_email_id emailto="D"]</h3>
			<ul class="orc_options_list"><br>
				<li>D = Department (intakedepartment, communicationsdepartment, hrdepartment) - Default = "intakedepartment"</li>
			</ul>
		</li>
		<li>
			<h3>Convert a date to the number of years from today<br/><br/>[orc_date_to_years year="Y" month="M" day="D"]</h3>
			<ul class="orc_options_list"><br>
				<li>Y = Year - Default = Current Year</li>
				<li>M = Month - Default = Current Month Number</li>
				<li>D = Day - Default = Current Day of Month</li>
			</ul>
		</li>
		<li>
			<h3>Get the number of days before eamils are automatically deleted<br/><br/>[orc_get_email_delete_days]</h3>
		</li>
	</ul>
	<h2>Visual Composer Elements</h2>
	<p>These components are available through the Visual Composer GUI to build website pages (RECOMMENDED) or via the shortcode shown below</p>
	<ul class="orc_options_list">
		<li>
			<h3>Create a Carousel of different pages (customized for each element)<br/><br/>[orc_carousel posttype="P" homepage="H" linkimage="L" prevnext="N" dots="N" autoplay="A" speed=S pauseonhover="V" loopcarousel="O" christmas="C" margin="M" width="W" height="H" margintop="T" marginbottom="B" marginleft="L" marginright="R" roundimg="I" colsdesktop="D" colstablet="T" colsmobile="M" css="C"]</h3>
			<ul class="orc_options_list">
				<li>
				   P - Post/Page Type - From the Category of the page (Required)<br/>
					&nbsp;&nbsp;- tours (ORC Tour Types)<br/>
					&nbsp;&nbsp;- program-types (Programs)<br/>
					&nbsp;&nbsp;- staff (Staff Members)<br/>
					&nbsp;&nbsp;- testimonials (Testimonials)<br/>
					&nbsp;&nbsp;- video-posts (Video\'s)<br/>
					&nbsp;&nbsp;- make-history (Vision Make History)<br/>
					&nbsp;&nbsp;- community (Vision Comunity)<br/>
				</li>
				<li>H - On Home Page - Default = false</li>
				<li>L - Link image to permalink - Default = false</li>
				<li>N - Show prev/next arrows - Default = false</li>
				<li>D - Show dots - Default = false</li>
				<li>A - Autoplay carousel - Default = false</li>
				<li>S - Autoplay speed - Default = 7000 (7 seconds)</li>
				<li>V - Pause carousel on hover - Default = false</li>
				<li>O - Loop carousel - Default = false</li>
				<li>C - Is Christmas testimonial - Default = false (only valid for posttype=testimonial and homepage="true"</li>
				<li>M - Margin between elements - Default 30px (Carousel base uses right margin)</li>
				<li>W,H - Width/Hight of image - Default 200px</li>
				<li>I - Make image round - Default = false</li>
				<li>D,T,M - Number of colums/items on Desktop/Tablet/Mobile - Default 5 (Desktop), 3 (Tablet), 1 (Mobile)</li>
				<li>C - Visual composer Design Options CSS</li>
			</ul>
		</li>
		<li>
			<h3>Display ORC contact information<br/><br/>[orc_contact contacts="C" makelink="M" fonticon="F" prefix="P" suffix="S" alignment="A" hardbreak="B" colorpicker="H" linkclass="C"]</h3>
			<ul class="orc_options_list">
				<li>C - Contact type (phone, tollfree, mobile, fax, intake, communications, hr)</li>
				<li>M - Link text to phone, sms or email program - Default = false</li>
				<li>F - Display fontawesome icon - Default = false</li>
				<li>P,S - Prefix/Suffix text - Default = ""</li>
				<li>A - Alignment (alignleft, alignright, aligncenter) - Default = ""</li>
				<li>B - Append HTML break after text - Default = false</li>
				<li>H - HTML Hex color including # - Default = "#000000"</li>
				<li>L - Class for the normal/hover for the link - Default = ""</li>
			</ul>
		</li>
		<li>
			<h3>Display ORC post information - Careers, Press Media and Testimonials<br/><br/>[orc_post post="P" alignment_left="AL" alignment_right="AR" width_left="WL" width_right="WR" offset_left="OL" offset_right="OR" css="C"</h3>
			<ul class="orc_options_list">
				<li>P - Post type (careers, press-media, testimonials)</li>
				<li>AL,AR - Alignment (alignmentleft, alignmentcenter, alignmentright)</li>
				<li>WL,WR - Number of columns left/right (1-12) - Press Media and Testimonials only</li>
				<li>OL,OR - Visual Composer offset/width/hide - Press Media and Testimonials only</li>
				<li>C - Visual Composer Design Options CSS - Press Media and Testimonials only</li>
			</ul>
		</li>
	</ul>
';

    echo $html;
}

/**
 * Display the options that are used in the plugin and allow them to be updated
 */
function orc_options_display_settings() {

    $orc_options_phone = get_option('orc_options_phone');
    $orc_options_tollfree = get_option('orc_options_tollfree');
    $orc_options_text = get_option('orc_options_text');
    $orc_options_fax = get_option('orc_options_fax');
    $orc_options_intake = get_option('orc_options_intake');
    $orc_options_communications = get_option('orc_options_communications');
    $orc_options_hr = get_option('orc_options_hr');
    $orc_options_alumni = get_option('orc_options_alumni');
    $orc_options_mainvideo = get_option('orc_options_mainvideo');
    $orc_options_xmasvideo = get_option('orc_options_xmasvideo');
    $orc_options_facebookappid = get_option('orc_options_facebookappid');
    $orc_options_facebookpixel = get_option('orc_options_facebookpixel');
    $orc_options_bing = get_option('orc_options_bing');
    $orc_options_linkedin = get_option('orc_options_linkedin');
    $orc_options_twitter = get_option('orc_options_twitter');
    $orc_options_org_schema = stripslashes(get_option('orc_options_org_schema'));
    $orc_options_local_schema = stripslashes(get_option('orc_options_local_schema'));
    $orc_options_wpcf7id = get_option('orc_options_wpcf7id');
    $orc_options_wpcf7id_comm = get_option('orc_options_wpcf7id_comm');
    $orc_options_wpcf7id_hr = get_option('orc_options_wpcf7id_hr');
    $orc_options_wpcf7id_alumni = get_option('orc_options_wpcf7id_alumni');
    $orc_options_wpcf7id_website = get_option('orc_options_wpcf7id_website');
    $orc_options_wpcf7id_privacy = get_option('orc_options_wpcf7id_privacy');
    $orc_options_email_delete_days = get_option('orc_options_email_delete_days');
    $orc_options_google_analytics = get_option('orc_options_google_analytics');
    $orc_options_staff_administrative_excerpt = get_option('orc_options_staff_administrative_excerpt');
    $orc_options_staff_clinical_excerpt = get_option('orc_options_staff_clinical_excerpt');
    $orc_options_staff_medical_excerpt = get_option('orc_options_staff_medical_excerpt');
    $orc_options_staff_recovery_excerpt = get_option('orc_options_staff_recovery_excerpt');
    $orc_options_staff_support_excerpt = get_option('orc_options_staff_support_excerpt');
    $orc_options_staff_wellness_excerpt = get_option('orc_options_staff_wellness_excerpt');
    $orc_options_delete_wpb_key = get_option('orc_options_delete_wpb_key');
    
    $html = '
<div class="wrap"><form action="options.php" method="post" name="options">
	<h1>Orchard Recovery Center Custom Settings</h1>
' . wp_nonce_field('update-options') . '
	<table class="form-table orc_options" width="300px" cellpadding="0">
	<tbody>
	
		<tr><td><h3>Phone Numbers</h3></td></tr>
		<tr><td><label>Local Phone Number: </label><input type="text" name="orc_options_phone" class="widefat" placeholder="Enter the Local Phone number" value="' . $orc_options_phone . '" /></td></tr>
		<tr><td><label>Toll Free Phone Number: </label><input type="text" name="orc_options_tollfree" class="widefat" placeholder="Enter the Toll Free Phone number" value="' . $orc_options_tollfree . '" /></td></tr>
		<tr><td><label>Text/Mobile Number: </label><input type="text" name="orc_options_text" class="widefat" placeholder="Enter the Text/Mobile Phone number" value="' . $orc_options_text . '" /></td></tr>
		<tr><td><label>Fax Number: </label><input type="text" name="orc_options_fax" class="widefat" placeholder="Enter the Fax number" value="' . $orc_options_fax . '" /></td></tr>
		
		<tr><td><h3>Email Addresses</h3></td></tr>
		<tr><td><label>Contact Form 7 ID (Intake): </label><input type="text" name="orc_options_wpcf7id" class="widefat" placeholder="Enter the ID of the contact form 7 used to send intake emails" value="' . $orc_options_wpcf7id . '" /></td></tr>
		<tr><td><label>Contact Form 7 ID (Communications): </label><input type="text" name="orc_options_wpcf7id_comm" class="widefat" placeholder="Enter the ID of the contact form 7 used to send communications emails" value="' . $orc_options_wpcf7id_comm . '" /></td></tr>
		<tr><td><label>Contact Form 7 ID (Human Resources): </label><input type="text" name="orc_options_wpcf7id_hr" class="widefat" placeholder="Enter the ID of the contact form 7 used to send human resources emails" value="' . $orc_options_wpcf7id_hr . '" /></td></tr>
		<tr><td><label>Contact Form 7 ID (Alumni): </label><input type="text" name="orc_options_wpcf7id_alumni" class="widefat" placeholder="Enter the ID of the contact form 7 used to send alumni coordinator emails" value="' . $orc_options_wpcf7id_alumni . '" /></td></tr>
		<tr><td><label>Contact Form 7 ID (Website): </label><input type="text" name="orc_options_wpcf7id_website" class="widefat" placeholder="Enter the ID of the contact form 7 used to send the website emails" value="' . $orc_options_wpcf7id_website . '" /></td></tr>
		<tr><td><label>Contact Form 7 ID (Privacy): </label><input type="text" name="orc_options_wpcf7id_privacy" class="widefat" placeholder="Enter the ID of the contact form 7 used to send the privacy officer emails" value="' . $orc_options_wpcf7id_privacy . '" /></td></tr>
			
		<tr><td><h3>Number of days to keep Contact Form 7 Emails</h3></td></tr>
		<tr><td><label>Contact Form Auto Delete (days): </label><input type="text" name="orc_options_email_delete_days" class="widefat" placeholder="Enter the number of days to keep the Contact Form 7 emails for" value="' . $orc_options_email_delete_days . '" /></td></tr>

		<tr><td><h3>YouTube Video URL\'s</h3></td></tr>
		<tr><td><label>Main Video ID: </label><input type="text" name="orc_options_mainvideo" class="widefat" placeholder="Enter the Main YouTube Video ID" value="' . $orc_options_mainvideo . '" /></td></tr>
		<tr><td><label>Xmas Video ID: </label><input type="text" name="orc_options_xmasvideo" class="widefat" placeholder="Enter the Xmas YouTube Video ID" value="' . $orc_options_xmasvideo . '" /></td></tr>
			
		<tr><td><h3>Google</h3></td></tr>
		<tr><td><label>Google Analytics Code: </label><input type="text" name="orc_options_google_analytics" class="widefat" placeholder="Enter the Google Analytics Code (UA-XXXXXX-1)" value="' . $orc_options_google_analytics . '" /></td></tr>
			
		<tr><td><h3>Facebook</h3></td></tr>
		<tr><td><label>Facebook App ID: </label><input type="text" name="orc_options_facebookappid" class="widefat" placeholder="Enter the Facebook App ID" value="' . $orc_options_facebookappid . '" /></td></tr>
		<tr><td><label>Facebook Pixel: </label><input type="text" name="orc_options_facebookpixel" class="widefat" placeholder="Enter the Facebook Pixel Code" value="' . $orc_options_facebookpixel . '" /></td></tr>
			
		<tr><td><h3>Bing</h3></td></tr>
		<tr><td><label>Bing Tracking: </label><input type="text" name="orc_options_bing" class="widefat" placeholder="Enter the Bing Tracking Code" value="' . $orc_options_bing . '" /></td></tr>

		<tr><td><h3>LinkedIn</h3></td></tr>
		<tr><td><label>LinkedIn Partner Code: </label><input type="text" name="orc_options_linkedin" class="widefat" placeholder="Enter the LinkedIn Partner Code" value="' . $orc_options_linkedin . '" /></td></tr>

		<tr><td><h3>Twitter</h3></td></tr>
		<tr><td><label>Twitter Universal Website Tag: </label><input type="text" name="orc_options_twitter" class="widefat" placeholder="Enter the Twitter Universal Website Tag" value="' . $orc_options_twitter . '" /></td></tr>

		<tr><td><h3>Schema\'s</h3></td></tr>
		<tr><td><label>Organization Schema (Displayed on Home Page ONLY): </label><textarea style="font-family:Courier;" name="orc_options_org_schema" class="widefat" rows="8" placeholder="Enter the schema script WITHOUT the script tags">' . $orc_options_org_schema . '</textarea></td></tr>
		<tr><td><label>Local Business Schema (Displayed on ALL Pages): </label><textarea style="font-family:Courier;" name="orc_options_local_schema" class="widefat" rows="8" placeholder="Enter the schema script WITHOUT the script tags">' . $orc_options_local_schema . '</textarea></td></tr>

		<tr><td><h3>Staff Excerpts</h3></td></tr>
		<tr><td><label>Administrative: </label><textarea name="orc_options_staff_administrative_excerpt" class="widefat" rows="8" placeholder="Enter the excerpt for the Administrative Staff">' . $orc_options_staff_administrative_excerpt . '</textarea></td></tr>
		<tr><td><label>Clinical: </label><textarea name="orc_options_staff_clinical_excerpt" class="widefat" rows="8" placeholder="Enter the excerpt for the Clinical Team">' . $orc_options_staff_clinical_excerpt . '</textarea></td></tr>
		<tr><td><label>Medical: </label><textarea name="orc_options_staff_medical_excerpt" class="widefat" rows="8" placeholder="Enter the excerpt for the Medical Team">' . $orc_options_staff_medical_excerpt . '</textarea></td></tr>
		<tr><td><label>Recovery Coach\'s: </label><textarea name="orc_options_staff_recovery_excerpt" class="widefat" rows="8" placeholder="Enter the excerpt for the Recovery Coach\'s">' . $orc_options_staff_recovery_excerpt . '</textarea></td></tr>
		<tr><td><label>Support Staff: </label><textarea name="orc_options_staff_support_excerpt" class="widefat" rows="8" placeholder="Enter the excerpt for the Suppor Staff">' . $orc_options_staff_support_excerpt . '</textarea></td></tr>
		<tr><td><label>Wellness: </label><textarea name="orc_options_staff_wellness_excerpt" class="widefat" rows="8" placeholder="Enter the excerpt for the Wellness">' . $orc_options_staff_wellness_excerpt . '</textarea></td></tr>
            
		<tr><td><h3>DEBUGGING</h3></td></tr>
		<tr><td><label>Delete WPBakery Key: </label><input type="text" name="orc_options_delete_wpb_key" class="widefat" placeholder="Enter DELETE to remove key" value="' . $orc_options_delete_wpb_key . '" /></td></tr>
            
    </tbody>
</table>
<br><br>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="orc_options_delete_wpb_key,orc_options_phone,orc_options_tollfree,orc_options_text,orc_options_fax,orc_options_mainvideo,orc_options_xmasvideo,orc_options_facebookappid,orc_options_facebookpixel,orc_options_bing,orc_options_linkedin,orc_options_twitter,orc_options_org_schema,orc_options_local_schema,orc_options_wpcf7id,orc_options_wpcf7id_comm,orc_options_wpcf7id_hr,orc_options_wpcf7id_alumni,orc_options_wpcf7id_website,orc_options_wpcf7id_privacy,orc_options_staff_administrative_excerpt,orc_options_email_delete_days,orc_options_google_analytics,,orc_options_staff_clinical_excerpt,orc_options_staff_medical_excerpt,orc_options_staff_recovery_excerpt,orc_options_staff_support_excerpt,orc_options_staff_wellness_excerpt" />
<input type="submit" name="Submit" value="Update" /></form></div>

';

    echo $html;
}

/**
 * Reorder the submenu
 * 
 * Info and Settings will be the last menu items, but we want them first
 * 
 * @global array $submenu 
 * @param type $menu_ord
 * @return type
 */
function orc_options_submenu_order($menu_ord) {
    global $submenu;

    $arr = array();
    $totalitems = 2;             // Amount of included files plus 2
    $arr[] = $submenu['orc_options'][$totalitems - 1];  // Information
    $arr[] = $submenu['orc_options'][$totalitems];   // Settings

    $totalitems = $totalitems - 2;
    for ($item = 0; $item < $totalitems; $item++) {
        $arr[] = $submenu['orc_options'][$item];
    }
    $submenu['orc_options'] = $arr;

    return $menu_ord;
}
add_filter('custom_menu_order', 'orc_options_submenu_order');

/**
 * Allow widgets to use short codes
 */
add_filter('widget_text', 'do_shortcode');

/**
 * Allow excerpts in pages
 */
add_post_type_support('page', 'excerpt');

/**
 * Allow categories and tags in pages
 */
function orc_page_settings() {
    // Add tag metabox to page
    register_taxonomy_for_object_type('post_tag', 'page');
    // Add category metabox to page
    register_taxonomy_for_object_type('category', 'page');
}
add_action('init', 'orc_page_settings');

/**
 * Disable comments and pingbacks for the entire site
 */
// Disable support for comments and trackbacks in post types
function orc_disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'orc_disable_comments_post_types_support');

// Close comments on the front-end
function orc_disable_comments_status() {
    return false;
}
add_filter('comments_open', 'orc_disable_comments_status', 20, 2);
add_filter('pings_open', 'orc_disable_comments_status', 20, 2);

// Hide existing comments
function orc_disable_comments_hide_existing_comments($comments) {
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'orc_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function orc_disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'orc_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function orc_disable_comments_admin_menu_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'orc_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function orc_disable_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'orc_disable_comments_dashboard');

// Remove comments links from admin bar
function orc_disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('admin_init', 'orc_disable_comments_admin_bar');

// Add Google Analytics Code
function orc_add_googleanalytics() {
    $orc_options_google_analytics = get_option('orc_options_google_analytics');
    $orc_options_wpcf7id = get_option('orc_options_wpcf7id');
    $orc_options_wpcf7id_comm = get_option('orc_options_wpcf7id_comm');
    $orc_options_wpcf7id_hr = get_option('orc_options_wpcf7id_hr');
    $orc_options_wpcf7id_alumni = get_option('orc_options_wpcf7id_alumni');
    $orc_options_wpcf7id_website = get_option('orc_options_wpcf7id_website');
    $orc_options_wpcf7id_privacy = get_option('orc_options_wpcf7id_privacy');

    if (strlen($orc_options_google_analytics) > 0) {
        ?>
        <!-- Google Analytics -->
        <script>
            window.ga = window.ga || function () {
                (ga.q = ga.q || []).push(arguments);
            };
            ga.l = +new Date;
            ga('create', '<?php echo $orc_options_google_analytics; ?>', 'auto');
            ga('send', 'pageview');
        </script>
        <script async src='https://www.google-analytics.com/analytics.js'></script>
        <!-- End Google Analytics -->
        <!-- Contact Form 7 hook into Google Analytics -->
        <script>
            document.addEventListener('wpcf7mailsent', function (event) {
                var analytics_event = 'Contact Form - Unknown';
                
                if(<?php echo $orc_options_wpcf7id; ?> == event.detail.contactFormId) {
                    analytics_event = 'Contact Form - Intake';
                } else if(<?php echo $orc_options_wpcf7id_comm; ?> == event.detail.contactFormId) {
                    analytics_event = 'Contact Form - Communications';
                } else if(<?php echo $orc_options_wpcf7id_hr; ?> == event.detail.contactFormId) {
                    analytics_event = 'Contact Form - HR';
                } else if(<?php echo $orc_options_wpcf7id_alumni; ?> == event.detail.contactFormId) {
                    analytics_event = 'Contact Form - Alumni';
                } else if(<?php echo $orc_options_wpcf7id_website; ?> == event.detail.contactFormId) {
                    analytics_event = 'Contact Form - Website';
                } else if(<?php echo $orc_options_wpcf7id_privacy; ?> == event.detail.contactFormId) {
                    analytics_event = 'Contact Form - Privacy';
                }
                
                ga('send', 'event', analytics_event, 'submit');
            }, false);
        </script>		
        <!-- End Contact Form 7 hook -->
        <?php
    }
}
add_action('wp_head', 'orc_add_googleanalytics');

// Add Required Schema's
//
// Organizational Schema is only on the home page
function orc_add_schemas() {
    $orc_options_org_schema = stripslashes(get_option('orc_options_org_schema'));
    $orc_options_org_schema = preg_replace( '/\R+|"[^"]*"(*SKIP)(*FAIL)|\s*/', '', $orc_options_org_schema );
    $orc_options_local_schema = stripslashes(get_option('orc_options_local_schema'));
    $orc_options_local_schema = preg_replace( '/\R+|"[^"]*"(*SKIP)(*FAIL)|\s*/', '', $orc_options_local_schema );

    if (is_front_page() && strlen($orc_options_org_schema) > 0) {
        ?>
        <!-- Organizational Schema -->
        <script type="application/ld+json">
            <?php echo $orc_options_org_schema; ?>
        </script>
        <?php
    }
    
    if (strlen($orc_options_local_schema) > 0) {
        ?>
        <!-- Organizational Schema -->
        <script type="application/ld+json">
            <?php echo $orc_options_local_schema; ?>
        </script>
        <?php
    }
}
add_action('wp_head', 'orc_add_schemas');


// Add Facebook Pixel Code
function orc_add_facebookpixel() {
    $orc_options_facebookpixel = get_option('orc_options_facebookpixel');

    if (strlen($orc_options_facebookpixel) > 0) {
        ?>
        <!-- Facebook Pixel Code -->
        <script>
            var facebook_pixel = '<?php echo $orc_options_facebookpixel; ?>';
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t, s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', facebook_pixel);
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo $orc_options_facebookpixel; ?>&amp;ev=PageView&amp;noscript=1" />
        </noscript>
        <!-- End Facebook Pixel Code -->
        <?php
    }
}
add_action('wp_head', 'orc_add_facebookpixel');

// Add Bing Tracking Code
function orc_add_bingtracking() {
    $orc_options_bing = get_option('orc_options_bing');

    if (strlen($orc_options_bing) > 0) {
        ?>
        <!-- Bing Tracking Code -->
        <script>
            var bing = "<?php echo $orc_options_bing; ?>";
            (function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:bing};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");
        </script>
        <!-- End Bing Tracking Code -->
        <?php
    }
}
add_action('wp_head', 'orc_add_bingtracking');

// Add LinkedIn Partner Code
function orc_add_linkedin() {
    $orc_options_linkedin = get_option('orc_options_linkedin');

    if (strlen($orc_options_linkedin) > 0) {
        ?>
        <!-- LinkedIn Partner Code -->
        <script>
            _linkedin_partner_id = "<?php echo $orc_options_linkedin; ?>";
            window._linkedin_data_partner_ids=window._linkedin_data_partner_ids||[];window._linkedin_data_partner_ids.push(_linkedin_partner_id);
            (function(){var s=document.getElementsByTagName("script")[0];var b=document.createElement("script");b.type="text/javascript";b.async=true;b.src="https://snap.licdn.com/li.lms-analytics/insight.min.js";s.parentNode.insertBefore(b,s);})();
        </script>
        <noscript>
            <img height="1" width="1" style="display:none;" alt="" src="https://dc.ads.linkedin.com/collect/?pid=<?php echo $orc_options_linkedin; ?>&fmt=gif" />
        </noscript>
        <!-- End LinkedIn Partner Code -->
        <?php
    }
}
add_action('wp_footer', 'orc_add_linkedin');

// Add Twitter Universal Website Tag
function orc_add_twitter() {
    $orc_options_twitter = get_option('orc_options_twitter');

    if (strlen($orc_options_twitter) > 0) {
        ?>
        <!-- Twitter universal website tag code -->
        <script>
            var twitter = '<?php echo $orc_options_twitter; ?>';
            !function(e,t,n,s,u,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);},s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='//static.ads-twitter.com/uwt.js',a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))}(window,document,'script');
            // Insert Twitter Pixel ID and Standard Event data below
            twq('init',twitter);
            twq('track','PageView');
        </script>
        <!-- End Twitter universal website tag code -->
        <?php
    }
}
add_action('wp_footer', 'orc_add_twitter');

/**
 * Disable Yoast Schema data
 */
add_filter( 'wpseo_json_ld_output', '__return_false' );

/*Removes RSD, XMLRPC, WLW, WP Generator, ShortLink and Comment Feed links*/
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action( 'wp_head', 'feed_links', 2 ); 
remove_action('wp_head', 'feed_links_extra', 3 );

/**
 * Reoccurring events create a post for each of the events.
 * This will replace the individual links with one link for the event
 * 
 * @param string $content - Original page content
 * @return string $content - Modified page content
 */
function my_em_custom_content($content) {
        
    // Modify the specific dates to point to just one event
    $content = preg_replace('/(events\/foo-west-vancouver-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'alumni/foo-meetings/#westvan', $content, -1, $count);
    $content = preg_replace('/(events\/foo-calgary-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'alumni/foo-meetings/#calgary', $content, -1, $count);
    $content = preg_replace('/(events\/foo-fraser-valley-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'alumni/foo-meetings/#fraservalley', $content, -1, $count);
    $content = preg_replace('/(events\/foo-victoria-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'alumni/foo-meetings/#vancouverisland', $content, -1, $count);
    
    $content = preg_replace('/(events\/alumni-meeting-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'alumni/#alumnimeeting', $content, -1, $count);
    
    $content = preg_replace('/(events\/shame-drama-triangle-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count);
    $content = preg_replace('/(events\/underlying-issues-addiction-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count);
    $content = preg_replace('/(events\/family-long-weekend-rebuilding-trust-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count);
    $content = preg_replace('/(events\/family-long-weekend-addiction-family-disease-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count);
    $content = preg_replace('/(events\/codependency-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count);
    $content = preg_replace('/(events\/addiction-101-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count);
    $content = preg_replace('/(events\/recovery-relapse-prevention-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count);
    
    // Whatever happens, you must return the $content variable, altered or not.
    return $content;
}
add_filter('em_content','my_em_custom_content');

// Remove WPBakery Key
add_action( 'init', function () {
    $deleteWPBakeryKey = get_option('orc_options_delete_wpb_key');
    // Only if called from the page [address of website backend]/wp-admin/index.php?forcedeactivate
    if ( 'DELETE' === $deleteWPBakeryKey && isset( $_GET['forcedeactivate'] ) ) {
        // Delete the WPBakery Key
        delete_option( 'vc_license_activation_key' );
        delete_option( 'wpb_js_js_composer_purchase_code' );
        update_option( 'orc_options_delete_wpb_key', '');
    }
});
    

// Include all the custom post types

require_once plugin_dir_path(__FILE__) . 'includes/cpt/staff_member_cpt.php';
