window.$ = window.jQuery = require('jquery/dist/jquery.slim');

$(function () {

    $('#menuLink').on('click', function(e) {
        e.preventDefault();

        $('#layout').toggleClass('active');
        $('#menu').toggleClass('active');
        $('#menuLink').toggleClass('active');
    });

});
