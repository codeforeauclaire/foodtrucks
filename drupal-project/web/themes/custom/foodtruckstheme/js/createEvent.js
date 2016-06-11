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
	});
}(jQuery))
