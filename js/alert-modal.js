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

    $.ajax({
        url: OC.getRootPath() + '/apps/groupalert/lib/settings.json',
        type: 'GET',
        dataType: 'json',
        cache: false,
    })
        .done(function(data) {
            if (data.checked === 'true' && targetDir === data.folder) {
                $('.GA-message-content').html(data.texte);
                $('.GA-message').fadeIn("slow", function(){

                });
                $('#cboxOverlay').show();
                $('#cboxOverlay').css({
                    'opacity': '0.4',
                    'cursor': 'pointer',
                    'visibility': 'visible',
                    'display' : 'block'
                });
            }

        })
        .fail(function() {
            //console.log("error");
        })
        .always(function() {
            //console.log("complete");
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

    //display on document ready
    showMessage();
    //display on click on filelist dir & breadcrumb
    $(document).on('click', 'a.name, .crumb', showMessage);

    $('.GA-close').click(function() {
        $('.GA-message').hide();
        $('#cboxOverlay').hide();
    });

});