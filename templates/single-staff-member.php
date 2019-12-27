<?php 

$post = $wp_query->post;

get_header(); 

$post_id = get_the_ID();

$layout = engage_page_layout( $post_id );
$general_layout = engage_general_layout( $layout );
$sidebar_width = engage_sidebar_width( $post_id );
$page_width = engage_page_width( $post_id );
$container_class = engage_container_class( $page_width );
$metadata = get_post_meta($post_id);
$position = array_key_exists( 'position', $metadata ) ? '<p class="position">' . $metadata['position'][0] . '</p>' : '';
$qualifications = array_key_exists( 'qualifications', $metadata ) ? '<p class="qualifications">' . $metadata['qualifications'][0] . '</p>' : '';
?>

<section class="section-page <?php echo esc_attr( $general_layout ); ?> page-layout-<?php echo esc_attr( $layout ); ?> sidebar-width-<?php echo esc_attr( $sidebar_width ); ?> page-width-<?php echo esc_attr( $page_width ); ?>"<?php engage_page_content_styles(); ?>>
	
	<div class="container<?php echo esc_attr( $container_class ); ?>">
	
		<div class="row main-row">
		
			<div id="page-content" class="page-content">
		
			<?php
			
			// Post Content Loop
			
			if (have_posts()) : while (have_posts()) : the_post(); 
				
				$extra_classes = array('post-holder', 'aligncenter');
				
				?>
				
				<div <?php post_class( $extra_classes ); ?>>
				
				<?php
				
				if ( has_post_thumbnail() ) {
                    the_post_thumbnail( array( 500, 500 ), array( 'class' => 'section-page wpb_content_element' ) );
				}
				
				?>
                <?php echo $position; ?>
                
                <?php echo $qualifications; ?>
                
				<?php the_content(); ?>
				
				</div>
								
				<?php
				
			// End The Loop
			          
			endwhile; endif; 
			
            echo '<div style="margin-bottom: 20px !important;border-top-width: 1px !important;padding-top: 20px !important;border-top-color: #e0e0e0 !important;border-top-style: solid !important;">';
            echo do_shortcode('[orc_carousel posttype="orc_staff_member" linkimage="true" loopcarousel="true" prevnext="true" additionalclass="skiplazyimage" roundimg="true" colsdesktop="7" colstablet="3" colsmobile="1" marginleft="0" marginright="0"]');
            echo '</div>';
			?>
			
			</div>
			
			<?php
			
			// Page Sidebar
		
			if( $layout != "no_sidebar" ) {
				get_sidebar();    
			}
			
			?>
		
		</div>
	
	</div>

</section>

<?php get_footer(); ?>