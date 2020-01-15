( function ( $ ) {
	'use strict';
	
	jQuery(document).ready(function() {

        var excerpt;
        $(document).on('click', 'span', function() {
            if('Administrative Staff' === $(this).text()) {
        		excerpt = staffexcerpt.administrative;
                $('#staff-excerpt').html(excerpt);
            } else if('Clinical Team' === $(this).text()) {
        		excerpt = staffexcerpt.clinical;
                $('#staff-excerpt').html(excerpt);
            } else if('Medical Team' === $(this).text()) {
        		excerpt = staffexcerpt.medical;
                $('#staff-excerpt').html(excerpt);
            } else if('Recovery Coach' === $(this).text()) {
        		excerpt = staffexcerpt.recovery;
                $('#staff-excerpt').html(excerpt);
            } else if('Support Staff' === $(this).text()) {
        		excerpt = staffexcerpt.support;
                $('#staff-excerpt').html(excerpt);
            } else if('Wellness' === $(this).text()) {
        		excerpt = staffexcerpt.wellness;
                $('#staff-excerpt').html(excerpt);
            }
        });

   }); // End (document).ready
	
}( jQuery ));
