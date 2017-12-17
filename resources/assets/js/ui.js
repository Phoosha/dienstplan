
/****************************************************************
 * Make shift slots selectable
 ****************************************************************/
$.fn.selectShift = function() {
	return this.each(function() {
		var shiftSlot = $(this);
		var radio = shiftSlot.find('input[type="radio"]');
		var doSelect = ! shiftSlot.hasClass('selected');
		
		radio.prop('checked', doSelect);
		shiftSlot.toggleClass('selected', doSelect);
		
		// allow only one slot to be selected per shift
		$('td[data-shift="' + shiftSlot.attr('data-shift') + '"]')
			.not(shiftSlot)
			.toggleClass('selected', false);
	});
};

$(function () {

    // Hide the radios with javascript
    $('input.shift-slot-select').hide();

    // Update the state in case user did selections without js loaded
    $('td.selectable').has('input[type="radio"]:checked').selectShift();

    // Update radio and cell on click and hover
    $('td.shift-slot').on('click', function () {
        if ($(this).is('.selectable')) {
            $(this).selectShift();
        } else {
            window.alert("Diese Schicht ist gesperrt!");
        }
    }).filter('td.selectable').on('mouseenter', function () {
        $(this).addClass('hovered-slot');
    }).on('mouseleave', function () {
        $(this).removeClass('hovered-slot');
    });

    // Do not change selection when clicking links
    $('td.shift-slot a').on('click', function () {
        event.stopPropagation();
    }).on('mouseenter', function () {
        $(this).parentsUntil('tr').addClass('dehover');
    }).on('mouseleave', function () {
        $(this).parents().removeClass('dehover');
    });

});


/****************************************************************
 * Allow hiding of past days
 ****************************************************************/
$(function () {

    var show = $('#hider-show');
    var hide = $('#hider-hide');
    var past = $('tr.past');

    if (show.hasClass('hidden')) {
        // Hide the past and show the element to redisplay it
        past.hide();
        show.removeClass('hidden');

        // Show / hide on click
        show.show().on('click', function () {
            past.show();
            $(this).addClass('hidden');
            hide.removeClass('hidden');
        });
        hide.on('click', function () {
            past.hide();
            //$('tr.past td.selected').selectShift();
            $(this).addClass('hidden');
            show.removeClass('hidden');
        });
    }

});
