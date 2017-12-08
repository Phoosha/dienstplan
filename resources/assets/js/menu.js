$(function () {

    $('#menuLink').on('click', function(e) {
        e.preventDefault();

        $('#layout').toggleClass('active');
        $('#menu').toggleClass('active');
        $('#menuLink').toggleClass('active');
    });

});
