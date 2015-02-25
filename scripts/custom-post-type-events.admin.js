jQuery(document).ready(function() {

	Plugin = {

		init:function(){

			// Cache
			Plugin.eventStart = jQuery('#event-date-start');
			Plugin.eventStartTimeHour = jQuery('#event-from-hour');
			Plugin.eventStartTimeMin = jQuery('#event-from-minute');
			Plugin.eventEnd = jQuery('#event-date-end');
			Plugin.eventEndTimeHour = jQuery('#event-to-hour');
			Plugin.eventEndTimeMin = jQuery('#event-to-minute');
			Plugin.multiDay = jQuery('#event-multi-day');
			Plugin.multiDayWrapper = jQuery('.multi-day');
			Plugin.time = jQuery('#event-time');
			Plugin.timeWrapper = jQuery('.event-time');

			Plugin.street = jQuery('#event-street');
			Plugin.streetnumber = jQuery('#event-street-number');
			Plugin.zip = jQuery('#event-zip');
			Plugin.city = jQuery('#event-city');
			Plugin.country = jQuery('#event-country');
			Plugin.latitude = jQuery('#event-latitude');
			Plugin.longitude = jQuery('#event-longitude');
			Plugin.getLatLngButton = jQuery('.get-lat-long');

			// Bootstrap
			Plugin.bindEvents();
		},

		bindEvents:function(){

			// Multi day
			Plugin.multiDayDisplay();
			Plugin.multiDay.change(function(){
				Plugin.multiDayDisplay();
			});

			// Time
			Plugin.timeDisplay();
			Plugin.time.change(function(){
				Plugin.timeDisplay();
			});

			// Location
			Plugin.getLatLngButton.click(function(e){
				e.preventDefault();
				Plugin.getLatLng();
			});

		},

		getLatLng:function(){

			var data = {
				action: 'get_event_lat_long',
				street: Plugin.street.val(),
				streetnumber: Plugin.streetnumber.val(),
				zip: Plugin.zip.val(),
				city: Plugin.city.val(),
				country: Plugin.country.val(),
			};

			jQuery.post(ajaxurl, data, function( response ){
				Plugin.latitude.val( response.latitude );
				Plugin.longitude.val( response.longitude );
			}, 'json' );

		},

		multiDayDisplay:function(){
			if ( Plugin.multiDay.prop('checked') ) {
				Plugin.multiDayWrapper.show();
				if ( '' === Plugin.eventEnd.val() )
					Plugin.eventEnd.val( Plugin.eventStart.val() );
			} else {
				Plugin.multiDayWrapper.hide();
				Plugin.eventEnd.val('');
			}
		},

		timeDisplay:function(){
			if ( Plugin.time.prop('checked') ) {
				if ( '' === Plugin.eventStartTimeHour.val() )
					Plugin.eventStartTimeHour.val('12');
				if ( '' === Plugin.eventStartTimeMin.val() )
					Plugin.eventStartTimeMin.val('00');
				if ( '' === Plugin.eventEndTimeHour.val() )
					Plugin.eventEndTimeHour.val('13');
				if ( '' === Plugin.eventEndTimeMin.val() )
					Plugin.eventEndTimeMin.val('00');
				Plugin.timeWrapper.show();
			} else {
				Plugin.timeWrapper.hide();
				Plugin.timeWrapper.find('input').val('');
			}
		}

	};

	Plugin.init();

});
