( function ( $ ) {
	'use strict';
	
	jQuery(document).ready(function() {
	
		$('#program-types-carousel').each(function() {
		
			var $owl = $(this);
			
			var autoPlay = $owl.data( 'orcc-autoplay' );
			var autoplayTimeout = $owl.data( 'orcc-autoplay-timeout' );
			var colsMobile = $owl.data( 'orcc-cols-mobile' );
			var colsTablet = $owl.data( 'orcc-cols-tablet' );
			var colsDesktop = $owl.data( 'orcc-cols-desktop' );
			var dots = $owl.data( 'orcc-dots' );
			var nav = $owl.data( 'orcc-nav' );
			var stopOnHover = $owl.data( 'orcc-stop-on-hover' );
			var margin = $owl.data( 'orcc-margin' );
			var loop = $owl.data( 'orcc-loop' );
			
			$owl.owlCarousel({
            responsiveClass:true,
            responsive:{
               0:   { items:colsMobile  },
               768: { items:colsTablet  },
               1280:{ items:colsDesktop }
            },
				dots:dots,
				nav:nav,
				mouseDrag:true,
			 	stopOnHover:stopOnHover,
			 	margin:margin,
			 	autoplayHoverPause:stopOnHover,
			 	autoplayTimeout:autoplayTimeout,
			 	autoplay:autoPlay,
			 	loop:loop,
            rewind:true,
			});
			 			
			$('.owl-item').on('mouseenter',function(e){
				$(this).closest('.owl-carousel').trigger('stop.owl.autoplay');
			});

			$('.owl-item').on('mouseleave',function(e){
				var $owl = $(this).closest('.owl-carousel');
				var autoPlay = ( $owl.data( 'autoplay' ) ) ? $owl.data( 'autoplay' ) : false;
				if(autoPlay) {
					$owl.trigger('play.owl.autoplay');
				}
			});
		
		}); // End $("#program-carousel").each
	
		$('#staff-carousel,#orc_staff_member-carousel').each(function() {
		
			var $owl = $(this);
			
			var autoPlay = $owl.data( 'orcc-autoplay' );
			var autoplayTimeout = $owl.data( 'orcc-autoplay-timeout' );
			var colsMobile = $owl.data( 'orcc-cols-mobile' );
			var colsTablet = $owl.data( 'orcc-cols-tablet' );
			var colsDesktop = $owl.data( 'orcc-cols-desktop' );
			var dots = $owl.data( 'orcc-dots' );
			var nav = $owl.data( 'orcc-nav' );
			var stopOnHover = $owl.data( 'orcc-stop-on-hover' );
			var margin = $owl.data( 'orcc-margin' );
			var loop = $owl.data( 'orcc-loop' );
			
			$owl.owlCarousel({
            responsiveClass:true,
            responsive:{
               0:   { items:colsMobile  },
               768: { items:colsTablet  },
               1280:{ items:colsDesktop }
            },
				dots:dots,
				nav:nav,
				mouseDrag:true,
			 	stopOnHover:stopOnHover,
			 	margin:margin,
			 	autoplayHoverPause:stopOnHover,
			 	autoplayTimeout:autoplayTimeout,
			 	autoplay:autoPlay,
			 	loop:loop,
            rewind:true,
			});
			 			
			$('.owl-item').on('mouseenter',function(e){
				$(this).closest('.owl-carousel').trigger('stop.owl.autoplay');
			});

			$('.owl-item').on('mouseleave',function(e){
				var $owl = $(this).closest('.owl-carousel');
				var autoPlay = ( $owl.data( 'autoplay' ) ) ? $owl.data( 'autoplay' ) : false;
				if(autoPlay) {
					$owl.trigger('play.owl.autoplay');
				}
			});
		
		}); // End $("#staff_member-carousel").each

		$('#testimonial-carousel').each(function() {
		
			var $owl = $(this);
			
			var autoPlay = $owl.data( 'orcc-autoplay' );
			var autoplayTimeout = $owl.data( 'orcc-autoplay-timeout' );
			var colsMobile = $owl.data( 'orcc-cols-mobile' );
			var colsTablet = $owl.data( 'orcc-cols-tablet' );
			var colsDesktop = $owl.data( 'orcc-cols-desktop' );
			var dots = $owl.data( 'orcc-dots' );
			var nav = $owl.data( 'orcc-nav' );
			var stopOnHover = $owl.data( 'orcc-stop-on-hover' );
			var margin = $owl.data( 'orcc-margin' );
			var loop = $owl.data( 'orcc-loop' );
			
			$owl.owlCarousel({
            responsiveClass:true,
            responsive:{
               0:   { items:colsMobile  },
               768: { items:colsTablet  },
               1280:{ items:colsDesktop }
            },
				dots:dots,
				nav:nav,
				mouseDrag:true,
			 	stopOnHover:stopOnHover,
			 	margin:margin,
			 	autoplayHoverPause:stopOnHover,
			 	autoplayTimeout:autoplayTimeout,
			 	autoplay:autoPlay,
			 	loop:loop,
            rewind:true,
			});
			 			
			$('.owl-item').on('mouseenter',function(e){
				$(this).closest('.owl-carousel').trigger('stop.owl.autoplay');
			});

			$('.owl-item').on('mouseleave',function(e){
				var $owl = $(this).closest('.owl-carousel');
				var autoPlay = ( $owl.data( 'autoplay' ) ) ? $owl.data( 'autoplay' ) : false;
				if(autoPlay) {
					$owl.trigger('play.owl.autoplay');
				}
			});
		
		}); // End $("#testimonial-carousel").each

		$('#tours-carousel').each(function() {
		
			var $owl = $(this);
			
			var autoPlay = $owl.data( 'orcc-autoplay' );
			var autoplayTimeout = $owl.data( 'orcc-autoplay-timeout' );
			var colsMobile = $owl.data( 'orcc-cols-mobile' );
			var colsTablet = $owl.data( 'orcc-cols-tablet' );
			var colsDesktop = $owl.data( 'orcc-cols-desktop' );
			var dots = $owl.data( 'orcc-dots' );
			var nav = $owl.data( 'orcc-nav' );
			var stopOnHover = $owl.data( 'orcc-stop-on-hover' );
			var margin = $owl.data( 'orcc-margin' );
			var loop = $owl.data( 'orcc-loop' );
			
			$owl.owlCarousel({
            responsiveClass:true,
            responsive:{
               0:   { items:colsMobile  },
               768: { items:colsTablet  },
               1280:{ items:colsDesktop }
            },
				dots:dots,
				nav:nav,
				mouseDrag:true,
			 	stopOnHover:stopOnHover,
			 	margin:margin,
			 	autoplayHoverPause:stopOnHover,
			 	autoplayTimeout:autoplayTimeout,
			 	autoplay:autoPlay,
			 	loop:loop,
            rewind:true,
			});
			 			
			$('.owl-item').on('mouseenter',function(e){
				$(this).closest('.owl-carousel').trigger('stop.owl.autoplay');
			});

			$('.owl-item').on('mouseleave',function(e){
				var $owl = $(this).closest('.owl-carousel');
				var autoPlay = ( $owl.data( 'autoplay' ) ) ? $owl.data( 'autoplay' ) : false;
				if(autoPlay) {
					$owl.trigger('play.owl.autoplay');
				}
			});
		
		}); // End $("#tours-carousel").each
		
		$('#videos-carousel').each(function() {
		
			var $owl = $(this);
			
			var autoPlay = $owl.data( 'orcc-autoplay' );
			var autoplayTimeout = $owl.data( 'orcc-autoplay-timeout' );
			var colsMobile = $owl.data( 'orcc-cols-mobile' );
			var colsTablet = $owl.data( 'orcc-cols-tablet' );
			var colsDesktop = $owl.data( 'orcc-cols-desktop' );
			var dots = $owl.data( 'orcc-dots' );
			var nav = $owl.data( 'orcc-nav' );
			var stopOnHover = $owl.data( 'orcc-stop-on-hover' );
			var margin = $owl.data( 'orcc-margin' );
			var loop = $owl.data( 'orcc-loop' );

         $owl.owlCarousel({
            responsiveClass:true,
            responsive:{
               0:   { items:colsMobile  },
               768: { items:colsTablet  },
               1280:{ items:colsDesktop }
            },
				dots:dots,
				nav:nav,
				mouseDrag:true,
			 	stopOnHover:stopOnHover,
			 	margin:margin,
			 	autoplayHoverPause:stopOnHover,
			 	autoplayTimeout:autoplayTimeout,
			 	autoplay:autoPlay,
			 	loop:loop,
            lazyLoad:true,
            rewind:true,
            center:true,
			});

         $('.owl-item').on('mouseenter',function(e){
				$(this).closest('.owl-carousel').trigger('stop.owl.autoplay');
			});

			$('.owl-item').on('mouseleave',function(e){
				var $owl = $(this).closest('.owl-carousel');
				var autoPlay = ( $owl.data( 'autoplay' ) ) ? $owl.data( 'autoplay' ) : false;
				if(autoPlay) {
					$owl.trigger('play.owl.autoplay');
				}
			});
		
		}); // End $("#video_posts-carousel").each

		$('#make-history-carousel').each(function() {
		
			var $owl = $(this);
			
			var autoPlay = $owl.data( 'orcc-autoplay' );
			var autoplayTimeout = $owl.data( 'orcc-autoplay-timeout' );
			var colsMobile = $owl.data( 'orcc-cols-mobile' );
			var colsTablet = $owl.data( 'orcc-cols-tablet' );
			var colsDesktop = $owl.data( 'orcc-cols-desktop' );
			var dots = $owl.data( 'orcc-dots' );
			var nav = $owl.data( 'orcc-nav' );
			var stopOnHover = $owl.data( 'orcc-stop-on-hover' );
			var margin = $owl.data( 'orcc-margin' );
			var loop = $owl.data( 'orcc-loop' );
			
			$owl.owlCarousel({
            responsiveClass:true,
            responsive:{
               0:   { items:colsMobile  },
               768: { items:colsTablet  },
               1280:{ items:colsDesktop }
            },
				dots:dots,
				nav:nav,
				mouseDrag:true,
			 	stopOnHover:stopOnHover,
			 	margin:margin,
			 	autoplayHoverPause:stopOnHover,
			 	autoplayTimeout:autoplayTimeout,
			 	autoplay:autoPlay,
			 	loop:loop,
            rewind:true,
			});
			 			
			$('.owl-item').on('mouseenter',function(e){
				$(this).closest('.owl-carousel').trigger('stop.owl.autoplay');
			});

			$('.owl-item').on('mouseleave',function(e){
				var $owl = $(this).closest('.owl-carousel');
				var autoPlay = ( $owl.data( 'autoplay' ) ) ? $owl.data( 'autoplay' ) : false;
				if(autoPlay) {
					$owl.trigger('play.owl.autoplay');
				}
			});
		
		}); // End $("#vision_make_history-carousel").each

		$('#community-carousel').each(function() {
		
			var $owl = $(this);
			
			var autoPlay = $owl.data( 'orcc-autoplay' );
			var autoplayTimeout = $owl.data( 'orcc-autoplay-timeout' );
			var colsMobile = $owl.data( 'orcc-cols-mobile' );
			var colsTablet = $owl.data( 'orcc-cols-tablet' );
			var colsDesktop = $owl.data( 'orcc-cols-desktop' );
			var dots = $owl.data( 'orcc-dots' );
			var nav = $owl.data( 'orcc-nav' );
			var stopOnHover = $owl.data( 'orcc-stop-on-hover' );
			var margin = $owl.data( 'orcc-margin' );
			var loop = $owl.data( 'orcc-loop' );
			
			$owl.owlCarousel({
            responsiveClass:true,
            responsive:{
               0:   { items:colsMobile  },
               768: { items:colsTablet  },
               1280:{ items:colsDesktop }
            },
				dots:dots,
				nav:nav,
				mouseDrag:true,
			 	stopOnHover:stopOnHover,
			 	margin:margin,
			 	autoplayHoverPause:stopOnHover,
			 	autoplayTimeout:autoplayTimeout,
			 	autoplay:autoPlay,
			 	loop:loop,
            rewind:true,
			});
			 			
			$('.owl-item').on('mouseenter',function(e){
				$(this).closest('.owl-carousel').trigger('stop.owl.autoplay');
			});

			$('.owl-item').on('mouseleave',function(e){
				var $owl = $(this).closest('.owl-carousel');
				var autoPlay = ( $owl.data( 'autoplay' ) ) ? $owl.data( 'autoplay' ) : false;
				if(autoPlay) {
					$owl.trigger('play.owl.autoplay');
				}
			});
		
		}); // End $("#vision_community-carousel").each

   }); // End (document).ready
	
}( jQuery ));