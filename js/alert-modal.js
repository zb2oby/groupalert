function getTargetDir() {
    var targetDir = '/';
    var currentUrl = window.location.href;
    try {
        var targetDir = currentUrl.split('?')[1].split('&')[0];
        if (targetDir.split('/')[0] === 'dir=') {
            targetDir = targetDir.split('=')[1];
        }
    }
    catch(err) {

    }
    return targetDir;
}

function showMessage() {
    var targetDir = decodeURIComponent(getTargetDir());

    $.post(OC.generateUrl('/apps/groupalert/display/message'), {targetDir: targetDir}, function (response) {
        if (response.display === 'true') {
            $('.GA-message-content').html(response.text);
            $('.GA-message').fadeIn(450, function(){

            });
            $('#cboxOverlay').show();
            $('#cboxOverlay').css({
                'opacity': '0.4',
                'cursor': 'pointer',
                'visibility': 'visible',
                'display' : 'block'
            });
        }
    });
}

$(document).ready(function () {

    //add HTML to files view
    $('#app-content-files').append('<div class="GA-message" style="display: block;">\n' +
        '\t<div class="GA-close"><img class="svg" src="../../../core/img/actions/close.svg" alt="x"></div>\n' +
        '\t<div class="GA-message-content"></div>\n' +
        '</div>');

    //By default hide HTML
    $('.GA-message').hide();

    //display message on document ready
    showMessage();
    //display on click on filelist dir & breadcrumb
    $(document).on('click', 'a.name, .crumb', showMessage);

    $('.GA-close').click(function() {
        $('.GA-message').fadeOut(450, function(){

        });

        $('#cboxOverlay').fadeOut(450, function(){
        });
    });

});