( function ( $ ) {
	'use strict';
	
   $(document).ready(function($) {
		$('.doemail').click(function() {
			var whotocontact = $(this).data( 'whotocontact' );
			var contactus = contactdata.contactuspage;
			var caller = location.href;
			$('#main-content').append('<form style="display:none;" id="gotocontactus" method="post" action="' + contactus + '">');
			$('#gotocontactus').append('<input type="hidden" name="whotocontact" value="' + whotocontact + '"><br/>');
			$('#gotocontactus').append('<input type="hidden" name="caller" value="' + caller + '"><br/>');
			$('#gotocontactus').append('<input type="hidden" name="contactus" value="' + contactus + '"><br/>');
			$('#gotocontactus').submit();
		});
   });

}( jQuery ));