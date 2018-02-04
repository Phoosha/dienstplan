window.$ = window.jQuery = require('jquery/dist/jquery.slim');

$('.clickable').click(function (event) {
    if (! $(event.target).hasClass('clickme')) {
        $(this).parent().find('input.clickme')
            .prop('checked', function (i, val) {
                return ! val;
            });
    }
});