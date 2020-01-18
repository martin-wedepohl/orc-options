<?php
/*
  Plugin Name: Orchard Recovery Center Options
  Plugin URI:
  Description: Optional information used in Orchard Recovery Center website
  Version: 1.4.1
  Author: Martin Wedepohl
  Author URI:
  License: GPLv2 or later
 */

 namespace ORCOptions;

 defined('ABSPATH') or die;

 require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

 use ORCOptions\Includes\Config;
 use ORCOptions\Includes\Settings;
 use ORCOptions\Includes\Options;

 class ORCOptions {
    public function __construct() {

        register_activation_hook(__FILE__, [$this, 'orc_options_activation']);
        register_deactivation_hook(__FILE__, [$this, 'orc_options_deactivation']);
        add_action('theme_prefix_rewrite_flush', [$this, 'theme_prefix_rewrite_flush']);

        add_action('orc_options_delete_emails', [$this, 'orc_options_delete_emails_hook']);

        // Enqueue styles and scripts
        add_action('admin_enqueue_scripts', [$this, 'orc_options_admin_styles']);
        add_action('wp_enqueue_scripts', [$this, 'orc_options_add_scripts']);

        // Add the custom WPBakery code
        add_action('vc_before_init', [$this, 'orc_custom_before_init_actions']);

        /**
         * Allow widgets to use short codes
         */
        add_filter('widget_text', 'do_shortcode');

        /**
         * Allow excerpts in pages
         */
        add_post_type_support('page', 'excerpt');

        add_action('init', [$this, 'orc_page_settings']);
        add_action('admin_init', [$this, 'orc_disable_comments_post_types_support']);
        add_filter('comments_open', [$this, 'orc_disable_comments_status', 20, 2]);
        add_filter('pings_open', [$this, 'orc_disable_comments_status', 20, 2]);
        add_filter('comments_array', [$this, 'orc_disable_comments_hide_existing_comments', 10, 2]);
        add_action('admin_menu', [$this, 'orc_disable_comments_admin_menu']);
        add_action('admin_init', [$this, 'orc_disable_comments_admin_menu_redirect']);
        add_action('admin_init', [$this, 'orc_disable_comments_dashboard']);
        add_action('admin_init', [$this, 'orc_disable_comments_admin_bar']);
        add_action('wp_head', [$this, 'orc_add_googleanalytics']);
        add_action('wp_head', [$this, 'orc_add_schemas']);
        add_action('wp_head', [$this, 'orc_add_facebookpixel']);
        add_action('wp_head', [$this, 'orc_add_bingtracking']);
        add_action('wp_footer', [$this, 'orc_add_linkedin']);
        add_action('wp_footer', [$this, 'orc_add_twitter']);
        add_filter( 'wpseo_json_ld_output', [$this, '__return_false'] );
        remove_action('wp_head', [$this, 'rsd_link']);
        remove_action('wp_head', [$this, 'wlwmanifest_link']);
        remove_action('wp_head', [$this, 'wp_generator']);
        remove_action('wp_head', [$this, 'wp_shortlink_wp_head']);
        remove_action( 'wp_head', [$this, 'feed_links', 2] ); 
        remove_action('wp_head', [$this, 'feed_links_extra', 3] );
        add_filter('em_content', [$this, 'my_em_custom_content']);
        add_action( 'init', [$this, 'delete_wpbakery_key']);

        Options::initialize();
        
        $this->registerSettingsPage();
    }

    /**
     * Called on plugin activation
     * Enables the cron to delete messages in the flamingo plugin
     */
    public function orc_options_activation() {
        if (wp_next_scheduled('orc_options_delete_emails')) {
            wp_clear_scheduled_hook('orc_options_delete_emails');
        }
        wp_schedule_event(time(), 'twicedaily', 'orc_options_delete_emails');
        flush_rewrite_rules();
    }


    /*
    * Called on plugin deactivation
    * Turn off the cron to delete messages in the flamingo plugin
    */
    public function orc_options_deactivation() {
        $timestamp = wp_next_scheduled('orc_options_delete_emails');
        wp_unschedule_event($timestamp, 'orc_options_delete_emails');
        flush_rewrite_rules();
    }

    public function theme_prefix_rewrite_flush() {
        flush_rewrite_rules();
    }

    /**
     * Function to remove messages and users from the flamingo plugin data after a certain amound of days days
     */
    public function orc_options_delete_emails_hook() {
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

    public function enqueueScripts() {

    }

    /**
     * Called to enqueue the admin styles used in this plugin
     */
    function orc_options_admin_styles() {
        if (is_admin()) {
            wp_enqueue_style('orc_options', plugins_url('dist/css/orc_options.min.css', __FILE__));
        }
    }

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

    /**
     * Include custom Visual Composer Elements
     */
    public function orc_custom_before_init_actions() {
        // Include new custom element and shortcodes
        require_once plugin_dir_path(__FILE__) . 'includes/shortcodes/orc-options-shortcodes.php';
        require_once plugin_dir_path(__FILE__) . 'includes/vc-elements/orc-carousel-elements.php';
        require_once plugin_dir_path(__FILE__) . 'includes/vc-elements/orc-contact-elements.php';
        require_once plugin_dir_path(__FILE__) . 'includes/vc-elements/orc-post-elements.php';
    }

    /**
     * Helper function for registering the settings page
     */
    public function registerSettingsPage() {
        if (is_admin()) {
            $plugin_name = plugin_basename(__FILE__);
            new Settings($plugin_name);
        }
    }

    /**
     * Allow categories and tags in pages
     */
    public function orc_page_settings() {
        // Add tag metabox to page
        register_taxonomy_for_object_type('post_tag', 'page');
        // Add category metabox to page
        register_taxonomy_for_object_type('category', 'page');
    }

    /**
     * Disable comments and pingbacks for the entire site
     */
    // Disable support for comments and trackbacks in post types
    public function orc_disable_comments_post_types_support() {
        $post_types = get_post_types();
        foreach ($post_types as $post_type) {
            if (post_type_supports($post_type, 'comments')) {
                remove_post_type_support($post_type, 'comments');
                remove_post_type_support($post_type, 'trackbacks');
            }
        }
    }

    // Close comments on the front-end
    public function orc_disable_comments_status() {
        return false;
    }

    // Hide existing comments
    public function orc_disable_comments_hide_existing_comments($comments) {
        $comments = array();
        return $comments;
    }

    // Remove comments page in menu
    public function orc_disable_comments_admin_menu() {
        remove_menu_page('edit-comments.php');
    }

    // Redirect any user trying to access comments page
    public function orc_disable_comments_admin_menu_redirect() {
        global $pagenow;
        if ($pagenow === 'edit-comments.php') {
            wp_redirect(admin_url());
            exit;
        }
    }

    // Remove comments metabox from dashboard
    public function orc_disable_comments_dashboard() {
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    }

    // Remove comments links from admin bar
    public function orc_disable_comments_admin_bar() {
        if (is_admin_bar_showing()) {
            remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
        }
    }

    // Add Google Analytics Code
    public function orc_add_googleanalytics() {
        $orc_options_google_analytics = Options::getOption('orc_options_google_analytics');
        $orc_options_wpcf7id = Options::getOption('orc_options_wpcf7id');
        $orc_options_wpcf7id_comm = Options::getOption('orc_options_wpcf7id_comm');
        $orc_options_wpcf7id_hr = Options::getOption('orc_options_wpcf7id_hr');
        $orc_options_wpcf7id_alumni = Options::getOption('orc_options_wpcf7id_alumni');
        $orc_options_wpcf7id_website = Options::getOption('orc_options_wpcf7id_website');
        $orc_options_wpcf7id_privacy = Options::getOption('orc_options_wpcf7id_privacy');

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

    // Add Required Schema's
    //
    // Organizational Schema is only on the home page
    public function orc_add_schemas() {
        $orc_options_org_schema = stripslashes(Options::getOption('orc_options_org_schema'));
        $orc_options_org_schema = preg_replace( '/\R+|"[^"]*"(*SKIP)(*FAIL)|\s*/', '', $orc_options_org_schema );
        $orc_options_local_schema = stripslashes(Options::getOption('orc_options_local_schema'));
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

    // Add Facebook Pixel Code
    public function orc_add_facebookpixel() {
        $orc_options_facebookpixel = Options::getOption('orc_options_facebookpixel');

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

    // Add Bing Tracking Code
    public function orc_add_bingtracking() {
        $orc_options_bing = Options::getOption('orc_options_bing');

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

    // Add LinkedIn Partner Code
    public function orc_add_linkedin() {
        $orc_options_linkedin = Options::getOption('orc_options_linkedin');

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

    // Add Twitter Universal Website Tag
    public function orc_add_twitter() {
        $orc_options_twitter = Options::getOption('orc_options_twitter');

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


    /**
     * Reoccurring events create a post for each of the events.
     * This will replace the individual links with one link for the event
     * 
     * @param string $content - Original page content
     * @return string $content - Modified page content
     */
    public function my_em_custom_content($content) {
            
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

    public function delete_wpbakery_key() {
        $deleteWPBakeryKey = get_option('orc_options_delete_wpb_key');
        // Only if called from the page [address of website backend]/wp-admin/index.php?forcedeactivate
        if ( 'DELETE' === $deleteWPBakeryKey && isset( $_GET['forcedeactivate'] ) ) {
            // Delete the WPBakery Key
            delete_option( 'vc_license_activation_key' );
            delete_option( 'wpb_js_js_composer_purchase_code' );
            update_option( 'orc_options_delete_wpb_key', '');
        }
    }
        


}

new ORCOptions();

// Include all the custom post types

require_once plugin_dir_path(__FILE__) . 'includes/cpt/staff_member_cpt.php';
