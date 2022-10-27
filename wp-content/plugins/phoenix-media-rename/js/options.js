(function($) {

	//add event handler to "sanitize filenames" checkbox
	$(document).ready(function() {
		$('#pmr_sanitize_filenames').click(check_accents)
	});

	//add event handler to "remove accents" checkbox
	$(document).ready(function() {
		$('#pmr_remove_accents').click(check_sanitize)
	});

	//change "remove accents" checkbox state if needed
	var check_accents = function () {
		if ($('#pmr_sanitize_filenames').prop('checked')){
			//"sanitize filenames" is on: remove accents has to be on
			$('#pmr_remove_accents').prop('checked', true);
		};
	}

	//change "remove accents" checkbox state if needed
	var check_sanitize = function () {
		if (!$('#pmr_remove_accents').prop('checked') && $('#pmr_sanitize_filenames').prop('checked')){
			//"sanitize filenames" is on: remove accents has to be on
			$('#pmr_remove_accents').prop('checked', true);
		};
	}

})(jQuery);
