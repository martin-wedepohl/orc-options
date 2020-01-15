( function ( $ ) {
	'use strict';
	
	jQuery(document).ready(function() {
		
		var mainvideo = videourls.mainVideo;
		var xmasvideo = videourls.xmasVideo;
	
		$('.mainVideo').on('click', function() {
			$('#video, #overlay').fadeIn('slow');
			$('#video-container').html('<iframe width=560 height=315 src=https://www.youtube.com/embed/' + mainvideo + '?rel=0 frameborder=0 allowfullscreen></iframe>');
		});
		
		$('.xmasVideo').on('click', function() {
			$('#video, #overlay').fadeIn('slow');
			$('#video-container').html('<iframe width=560 height=315 src=https://www.youtube.com/embed/' + xmasvideo + '?rel=0 frameborder=0 allowfullscreen></iframe>');
		});
		
		$(document).on('touchend, mouseup', function(e) {
			if (!$('#video').is(e.target)) {
				$('#video, #overlay').fadeOut('slow');
				$('#video-container').html('');
			}
		});

   }); // End (document).ready
	
}( jQuery ));