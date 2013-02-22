var CPTEVENTS;

jQuery(document).ready(function() {

	CPTEVENTS = {

		init:function(){

			// Cache
			CPTEVENTS.eventStart = jQuery('#event-date-start');
			CPTEVENTS.eventStartTimeHour = jQuery('#event-from-hour');
			CPTEVENTS.eventStartTimeMin = jQuery('#event-from-minute');
			CPTEVENTS.eventEnd = jQuery('#event-date-end');
			CPTEVENTS.eventEndTimeHour = jQuery('#event-to-hour');
			CPTEVENTS.eventEndTimeMin = jQuery('#event-to-minute');
			CPTEVENTS.multiDay = jQuery('#event-multi-day');
			CPTEVENTS.multiDayWrapper = jQuery('.multi-day');
			CPTEVENTS.time = jQuery('#event-time');
			CPTEVENTS.timeWrapper = jQuery('.event-time');
			CPTEVENTS.toggleLocation = jQuery('.toggle-location');
			CPTEVENTS.loc = jQuery('.location');

			// Bootstrap
			CPTEVENTS.bindEvents();
		},

		bindEvents:function(){

			// Multi day
			CPTEVENTS.multiDayDisplay();
			CPTEVENTS.multiDay.change(function(){
				CPTEVENTS.multiDayDisplay();
			});

			// Time
			CPTEVENTS.timeDisplay();
			CPTEVENTS.time.change(function(){
				CPTEVENTS.timeDisplay();
			});

			// Location
			CPTEVENTS.toggleLocation.click(function(e){
				e.preventDefault();
				var obj = jQuery(this);

				if ( 'closed' == obj.data('status') ) {
					obj.data('status','opened');
					CPTEVENTS.loc.show();
					obj.text( cptEvents.removeLocation );
				} else {
					obj.data('status','closed');
					CPTEVENTS.loc.hide();
					obj.text( cptEvents.addLocation );
					CPTEVENTS.loc.find('input').val('');
				}

			});
		},

		multiDayDisplay:function(){
			if ( CPTEVENTS.multiDay.attr('checked') ) {
				CPTEVENTS.multiDayWrapper.show();
				if ( '' === CPTEVENTS.eventEnd.val() )
					CPTEVENTS.eventEnd.val( CPTEVENTS.eventStart.val() );
			} else {
				CPTEVENTS.multiDayWrapper.hide();
				CPTEVENTS.eventEnd.val('');
			}
		},

		timeDisplay:function(){
			if ( CPTEVENTS.time.attr('checked') ) {
				if ( '' === CPTEVENTS.eventStartTimeHour.val() )
					CPTEVENTS.eventStartTimeHour.val('12');
				if ( '' === CPTEVENTS.eventStartTimeMin.val() )
					CPTEVENTS.eventStartTimeMin.val('00');
				if ( '' === CPTEVENTS.eventEndTimeHour.val() )
					CPTEVENTS.eventEndTimeHour.val('13');
				if ( '' === CPTEVENTS.eventEndTimeMin.val() )
					CPTEVENTS.eventEndTimeMin.val('00');
				CPTEVENTS.timeWrapper.show();
			} else {
				CPTEVENTS.timeWrapper.hide();
				CPTEVENTS.timeWrapper.find('input').val('');
			}
		}

	};

	CPTEVENTS.init();

});