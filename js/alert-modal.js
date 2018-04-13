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

function getUserGroups() {

   var response =  $.ajax({
        url: OC.getRootPath() + '/apps/groupalert/ajax/settings.php',
        type: 'GET',
        dataType: 'html',
        data: 'getUserInfo=groups',
        cache: false,
        async: false
    });

    return JSON.parse(response.responseText);
}

function showMessage() {
    var targetDir = decodeURIComponent(getTargetDir());
    var groups = getUserGroups();

    $.ajax({
        url: OC.getRootPath() + '/apps/groupalert/lib/settings.json',
        type: 'GET',
        dataType: 'json',
        cache: false,
    })
        .done(function(data) {

            $.each(data, function(key, entry){
                $.each(groups, function(keySys, entrySys){
                    var groups = entry.groups.split('|');
                    $.each(groups, function(group, messageGroupe){
                        if(messageGroupe === entrySys && entry.checked === 'true' && targetDir === entry.folder) {
                            $('.GA-message-content').html(entry.texte);
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
                });
            });
        })
        .fail(function() {

        })
        .always(function() {

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