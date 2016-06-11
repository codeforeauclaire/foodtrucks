/**
 * Created by lowell on 6/11/16.
 *
 * Initialized by Anthony on 6/11/16
 */
;(function($) {
	$(document).ready(function() {
		// Wait until element exists
		// * http://stackoverflow.com/a/13709125/4747661
		// ** https://gist.github.com/PizzaBrandon/5709010
		// TODO: Hook into map creation correctly and remove this hack?
		var geoInputSel = '.geocode-controlls-wrapper > input';
		$(geoInputSel).waitUntilExists(function() {
			// Setup
			var geoInputEl = jQuery(geoInputSel)

			// Select all when focused (to easily type more)
			geoInputEl.focus(function() { $(this).select() })
			
			// Prevent mouse up unfocusing
			// http://stackoverflow.com/a/1269767/4747661
			geoInputEl.mouseup(function(e){
				e.preventDefault()
			})
		})
		// Fail submission if start time after end time
		$('.node-food-truck-event-scheduled-form').submit(function() {
			var startHour = Number($('#edit-field-event-start-hour').val())
			var startMinute = Number($('#edit-field-event-start-minute').val())
			var endHour = Number($('#edit-field-event-end-hour').val())
			var endMinute = Number($('#edit-field-event-end-minute').val())
			var start = (60 * startHour) + startMinute
			var end = (60 * endHour) + endMinute
			if (start >= end) {
				alert('Please adjust your event times so it ends after it starts')
				return false
			}
			return true
		})
	});
}(jQuery))
