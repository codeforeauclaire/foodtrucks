$(document).on('pageshow', function() {
	$('#datepicker a').click(function (e) {
		e.preventDefault();
		var dateField = $(this).siblings('div').children('input');
		var date = new Date(dateField.datepicker('getDate'));
		if ($(this).children('.left-arrow').size() > 0) {
			dateField.datepicker('setDate', new Date(date.setDate(date.getDate() - 1)));
		} else {
			dateField.datepicker('setDate', new Date(date.setDate(date.getDate() + 1)));
		}
	});
});

