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

    var dutyMode = true;
    if ($('#create-post').empty())
        dutyMode = false;


    var startDates = $('input.start-date');
    var endDates   = $('input.end-date');
    var startTimes = $('select.start-time');
    var endTimes   = $('select.end-time');

    var minDate = $('input#min-date').val();
    var maxDate = $('input#max-date').val();

    startDates.datepicker({
        onClose: function () {
            var myEndInput = endDates.filterSameFieldsetAs(this);
            var newDate = $(this).datepicker("getDate");
            console.log(myEndInput.val());

            if (myEndInput.val() !== "nie" && newDate > myEndInput.datepicker("getDate")) {
                myEndInput.datepicker("setDate", newDate);
            }

            if (dutyMode)
                updateEndTime(this);

            $(this).attr("disabled", false);
        },
        beforeShow: function () {
            $(this).attr("disabled", true);
        },
        minDate: minDate,
        maxDate: maxDate
    });
    endDates.datepicker({
        onClose: function () {
            if (dutyMode)
                updateEndTime(this);
            else if (this.value === "")
                this.value = "nie";

            $(this).attr("disabled", false);
        },
        onSelect: function (date, picker) {
            if (! dutyMode && date === picker.lastVal)
                this.value = "nie";
        },
        beforeShow: function () {
            $(this).attr("disabled", true);

            var myStartInput = startDates.filterSameFieldsetAs(this);
            var myStartDate  = myStartInput.datepicker("getDate");

            if (! dutyMode)
                myStartDate.setDate(myStartDate.getDate() + 1);

            $(this).datepicker("option", "minDate", myStartDate);
        },
        maxDate: maxDate
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

