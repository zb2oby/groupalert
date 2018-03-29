function updatePrompt(appUrl, notificationSave) {

    var text = $("#GA-setMsg-form").find('#GA-setMsg').val()

    //Update Message
    $("#GA-setMsg").on("change keyup paste", function() {
        var currentVal = $(this).val();
        if(currentVal === text) {
            return; //check to prevent multiple simultaneous triggers
        }

        text = currentVal;

        $.ajax({
            url: appUrl + 'ajax/settings.php',
            type: 'GET',
            dataType: 'html',
            data: 'texte='+text,
        })
            .done(function() {
                //console.log("success");
            })
            .fail(function() {
                //console.log("error");
            })
            .always(function() {
                //console.log("complete");
            });

    });

    $("#GA-setMsg").on("blur", function() {
        OC.Notification.showTemporary(t('settings', notificationSave), {timeout: 2});
    });

    //Update value of checkbox and update button content
    $("#GA-setDisplay").on("change", function(){
        var checkedVal;
        var GATranslate;
        if ($(this).prop('checked')) {
            checkedVal = 'true';
            GATranslate = $('#GA-l10n-disable').text();
            $('#GA-labelActiveDisplay').html(GATranslate);
        }else {
            checkedVal = 'false';
            GATranslate = $('#GA-l10n-enable').text();
            $('#GA-labelActiveDisplay').html(GATranslate);
        }

        $.ajax({
            url: appUrl + 'ajax/settings.php',
            type: 'GET',
            dataType: 'html',
            data: 'checked='+checkedVal,
        })
            .done(function(response) {
                OC.Notification.showTemporary(t('settings', notificationSave), {timeout: 2});
                //console.log(response);
            })
            .fail(function() {
                //console.log("error");
            })
            .always(function() {
                //console.log("complete");
            });
    });


    //update value of select folders
    $('#GA-folder-form').on('change', function(){
       var selectedVal = '/'+$('#GA-folder option:selected').val();
        $.ajax({
            url: appUrl + 'ajax/settings.php',
            type: 'GET',
            dataType: 'html',
            data: 'folder='+selectedVal,
        })
            .done(function() {
                OC.Notification.showTemporary(t('settings', notificationSave), {timeout: 2});
                //console.log("success");
            })
            .fail(function() {
                //console.log("error");
            })
            .always(function() {
                //console.log("complete");
            });
    });

}




$(document).ready(function () {

    var appUrl = $('#GA-appUrl').val();
    var notificationSave = $('#GA-l10n-notification-save').text();

    //hydrate form fields with current values and display button content
    $.getJSON(appUrl + 'lib/settings.json', function(data) {
        $('#GA-setMsg').val(data.texte);
        $('#GA-folder').val(data.folder.split('/')[1]);
        var GASetDisplay = $('#GA-setDisplay');
        $(GASetDisplay).val(data.checked);
        var GATranslate;
        if (data.checked === 'true') {
            GATranslate = $('#GA-l10n-disable').text();
            $('#GA-labelActiveDisplay').html(GATranslate);
            $(GASetDisplay).prop('checked', true);
        }else {
            GATranslate = $('#GA-l10n-enable').text();
            $('#GA-labelActiveDisplay').html(GATranslate);
        }
    });


    //initialize select group field
    var $GATargetGroups = $('#GA-setGroups');
    OC.Settings.setupGroupsSelect($GATargetGroups);

    //update selected groups
    $GATargetGroups.change(function(ev) {
       var groups = ev.val || [];

        $.ajax({
            url: appUrl + 'ajax/settings.php',
            type: 'GET',
            dataType: 'html',
            data: 'groups='+groups
        })
            .done(function(response) {
                OC.Notification.showTemporary(t('settings', notificationSave), {timeout: 2});
                //console.log(appUrl);
            })
            .fail(function() {
                //console.log("error");
            })
            .always(function() {
                //console.log("complete");
            });
    });


    $('#GA-preview').click(function(){
        $.getJSON('/apps/groupalert/lib/settings.json', function(data) {
            $('#GA-setMsg-form').append('<div class="GA-message" id="GA-message-preview" style="display: block;">\n' +
                '\t<div class="GA-close" id="GA-close-preview">X</div>\n' +
                '\t<div class="GA-message-content">'+data.texte+'</div>\n' +
                '</div>');
        });


    });
    $(document).on('click', '#GA-close-preview', function() {
        $('.GA-message').remove();
    });

    updatePrompt(appUrl, notificationSave);

});