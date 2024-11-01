(function ( $ ) {
	"use strict";

	$(function () {
		// a little slight-of-hand to hide the white-space created
		// by hidden fields in Settings API
		var teledini_hide_rows = function() {
			// fetch the row parent of the hidden input field
			var teledini_input_parent = $('.teledini_hidden').parent().parent();
			
			// add the hidden class to the row
			teledini_input_parent.addClass('teledini_hidden')
		}
		
		teledini_hide_rows();

	});

}(jQuery));
