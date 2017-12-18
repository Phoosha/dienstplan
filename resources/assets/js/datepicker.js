window.$ = window.jQuery = require('jquery');
require('jquery-ui/themes/base/core.css');
require('jquery-ui/themes/base/datepicker.css');
require('jquery-ui/themes/base/theme.css');
require('jquery-ui/ui/widgets/datepicker');


/****************************************************************
 * Setup datepicker
 ****************************************************************/

var de = require('jquery-ui/ui/i18n/datepicker-de.js');

$(function() {

	$.datepicker.setDefaults(de);

	var startInputs = $('input.start-date');
    var endInputs   = $('input.end-date');

	startInputs.datepicker({
		onClose: function() {
			$(this).attr("disabled", false);

            var group  		 = $(this).attr('data-group');
            var myEndInput   = endInputs.filter('[data-group="' + group + '"]');
			var newStartDate = $(this).datepicker("getDate");

            if (newStartDate > myEndInput.datepicker("getDate")) {
				myEndInput.datepicker("setDate", newStartDate);
			}
		},
		beforeShow: function() {
			$(this).attr("disabled", true);
		}
	});
	endInputs.datepicker({
		onClose: function() {
			$(this).attr("disabled", false);
		},
		beforeShow: function() {
			$(this).attr("disabled", true);

            var group  		 = $(this).attr('data-group');
            var myStartInput = startInputs.filter('[data-group="' + group + '"]');

			$(this).datepicker("option", "minDate", myStartInput.datepicker("getDate"));
		}
	});

});

