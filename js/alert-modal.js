$(document).ready(function () {

    //add HTML to files view
    $('#app-content-files').append('<div class="GA-message" style="display: block;">\n' +
        '\t<div class="GA-close">X</div>\n' +
        '\t<div class="GA-message-content"></div>\n' +
        '</div>');

    //By default hide HTML
    $('.GA-message').hide();

    //hydrate HTML
    $.getJSON('/owncloud/apps/groupalert/lib/settings.json', function(data) {

        if (data.checked == 'true') {
            $('.GA-message-content').html(data.texte);
            $('.GA-message').show();
        }
    });

    $('.GA-close').click(function() {
        $('.GA-message').hide();
    });

});