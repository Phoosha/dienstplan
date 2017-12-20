window.$ = window.jQuery = require('jquery');
require('jquery-ui/themes/base/core.css');
require('jquery-ui/themes/base/datepicker.css');
require('jquery-ui/themes/base/theme.css');
require('jquery-ui/ui/widgets/datepicker');


/****************************************************************
 * Setup datepicker
 ****************************************************************/

var de = require('jquery-ui/ui/i18n/datepicker-de.js');

$.datepicker.setDefaults(de);

$.fn.filterSameFieldsetAs = function (element) {
	var commonAncestor = $(element).closest('fieldset').get(0);
	return this.filter(function () {
		return $(this).closest('fieldset').get(0) === commonAncestor;
    });
};

$(function() {

    var startDates = $('input.start-date');
    var endDates = $('input.end-date');
    var startTimes = $('select.start-time');
    var endTimes   = $('select.end-time');

    startDates.datepicker({
        onClose: function () {
            var myEndDate = endDates.filterSameFieldsetAs(this)
            var newDate = $(this).datepicker("getDate");

            if (newDate > myEndDate.datepicker("getDate")) {
                myEndDate.datepicker("setDate", newDate);
            }

            updateEndTime(this);
            $(this).attr("disabled", false);
        },
        beforeShow: function () {
            $(this).attr("disabled", true);
        }
    });
    endDates.datepicker({
        onClose: function () {
            updateEndTime(this);
            $(this).attr("disabled", false);
        },
        beforeShow: function () {
            $(this).attr("disabled", true);

            var myStartInput = startDates.filterSameFieldsetAs(this);

            $(this).datepicker("option", "minDate", myStartInput.datepicker("getDate"));
        }
    });


/****************************************************************
 * Pimp the time dropdowns
 ****************************************************************/

    // only start/end time on the same day is of interest
    function sameDay(element) {
        var myStartDate = startDates.filterSameFieldsetAs(element);
        var myEndDate   = endDates.filterSameFieldsetAs(element);

        return myStartDate.datepicker("getDate").getTime() === myEndDate.datepicker("getDate").getTime();
    }

    // update the end time such that it is always before the start time
    function updateEndTime(element) {
        if (! sameDay(element))
            return;

        var myEndDate    = endDates.filterSameFieldsetAs(element);
        var myStartTime  = startTimes.filterSameFieldsetAs(element);
        var myEndTime    = endTimes.filterSameFieldsetAs(element);
        var startTimeVal = myStartTime.val();

        // ensure the end time is always before the start time
        if (startTimeVal >= myEndTime.val()) {
            var nextOption = myStartTime.children('option:selected').first().next();

            if (nextOption.length > 0) {
                // select the next option
                myEndTime.val(nextOption.val());
            } else {
                // or the next day if the selected option was the last
                var date = myEndDate.datepicker("getDate");
                date.setDate(date.getDate() + 1);
                myEndDate.datepicker('setDate', date);
                myEndTime.val(myStartTime.children('option:first-child').val());
            }
        }
    }

    startTimes.on('input', function () {
        updateEndTime(this);
    });

    // hide all end times before the start time
    endTimes.on('focusin', function () {
        // ensure we show all options since we might not hide any later
        var allOptions = $(this).children('option').show();

        if (!sameDay(this))
            return;

        var myStartTime = startTimes.filterSameFieldsetAs(this);
        var startTimeVal = myStartTime.val();

        allOptions.filter(function () {
            return startTimeVal >= $(this).val();
        }).hide();
    });

});

