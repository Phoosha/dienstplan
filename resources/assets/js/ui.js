
/****************************************************************
 * Make shift slots selectable
 ****************************************************************/

$.fn.classList = function() {
	if (this.length > 0) {
		return this.attr('class').split(/\s+/);
	}
}

$.fn.shiftID = function() {
	var classes = this.classList()
	if (classes) {
		for (var i = 0; i < classes.length; i++) {
			if (classes[i].indexOf('shift-id-') === 0) {
				return classes[i];
			}
		}
	}
}

$.fn.selectShift = function() {
	var shiftSlots = this;
	
	// filter shifts with multiple selections
	shiftSlots = shiftSlots.filter(function() {
		return shiftSlots.not($(this)).filter('td.' + $(this).shiftID()).length === 0;
	});
	
	return shiftSlots.each(function() {
		var shiftSlot = $(this);
		var checkbox = shiftSlot.find('input[type="checkbox"]');
		var doSelect = ! shiftSlot.hasClass('selected');
		
		checkbox.prop('checked', doSelect);
		shiftSlot.toggleClass('selected', doSelect);
		
		// allow only one checkbox to be selected per group (=shift)
		$('input[name="' + checkbox.attr('name') + '"]').not(checkbox).prop('checked', false);
		$('td.' + shiftSlot.shiftID()).not(shiftSlot).toggleClass('selected', false);
	});
}

$(function () {

    // Hide the checkboxes with javascript
    $('input.shift-slot-select').hide();

    // Update the state in case user did selections without js loaded
    $('td.selectable').has('input[type="checkbox"]:checked').selectShift();

    // Update checkbox and cell on click and hover
    $('td.shift-slot').on('click', function() {
        if ($(this).is('.selectable')) {
            $(this).selectShift();
        } else {
            window.alert("Diese Schicht ist gesperrt!");
        }
    }).filter('td.selectable').on('mouseenter', function() {
        $(this).addClass('hovered-slot');
    }).on('mouseleave', function() {
        $(this).removeClass('hovered-slot');
    });

    // Do not change selection when clicking links
    $('td.shift-slot a').on('click', function(event) {
        event.stopPropagation();
    }).on('mouseenter', function() {
        $(this).parentsUntil('tr').addClass('dehover');
    }).on('mouseleave', function() {
        $(this).parents().removeClass('dehover');
    });

});


/****************************************************************
 * Allow hiding of past days
 ****************************************************************/
$(function () {

    $('tr.hideable').hide();
    $('#hider-show').show().on('click', function () {
        $('tr.hideable').show();
        $(this).hide();
        $('#hider-hide').show();
    });
    $('#hider-hide').on('click', function () {
        $('tr.hideable').hide();
        $('tr.hideable td.selected').selectShift();
        $(this).hide();
        $('#hider-show').show();
    });

});
