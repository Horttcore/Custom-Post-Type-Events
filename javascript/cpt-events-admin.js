jQuery(document).ready(function() {

	// All day
	var allDay = jQuery('#all-day'),
		dateTime = jQuery('.date-time');


	if ( allDay.attr('checked') )
		dateTime.hide();

	allDay.click(function(){
		if ( allDay.attr('checked') )
			dateTime.hide();
		else
			dateTime.show();
	});

});