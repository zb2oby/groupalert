$(document).ready(function () {

    var notificationSave = $('#GA-l10n-notification-save').text();
    var notificationAdd = $('#GA-l10n-notification-add').text();
    var notificationDelete = $('#GA-l10n-notification-delete').text();
    var notificationEnable = $('#GA-l10n-notification-enabled').text();
    var notificationDisable = $('#GA-l10n-notification-disabled').text();
    var PromptSave = $('#GA-l10n-prompt-save').text();
    var PromptDelete = $('#GA-l10n-prompt-delete').text();
    var confirmButton = $('#GA-l10n-confirm').text();
    var requiredValue = $('#GA-l10n-required').text();
    var errorContextUpdate = $('#GA-l10n-errorContext-update').text();
    var errorContextCreate = $('#GA-l10n-errorContext-create').text();
    var errorShare = $('#GA-l10n-error-share').text();
    var errorExist = $('#GA-l10n-error-exist').text();

    //hide content by default
    $('.GA-content-values').hide();

    //hide enable/disable button by default
    $('.GA-buttons').hide();

    //hydrate previous message list
    $.ajax({
        url: OC.getRootPath() + '/apps/groupalert/lib/settings.json',
        type: 'GET',
        dataType: 'json',
        cache: false
    })
    .done(function(data){
        data.sort(function(a, b){
            return b['date'] - a['date'];
        });
        $.each(data, function(key, entry){
            var d = new Date(data[key].date*1000);
                $('#GA-selectList').append('<option value="'+data[key].id+'">'+data[key].title+' '+d.getDate() + '/' + (d.getMonth()+1) + '/' + d.getFullYear()+'</option>');
        })
    });

    //empty fields to add new message
    $('#GA-new').click(function(e) {
        $('#GA-setTitle').val('');
        $('#GA-setMsg').val('');
        $('#GA-folder').val('');
        $('.GA-buttons').show();
        $('.GA-delete').hide();
        $('#GA-selectList option[value="0"]').prop('selected', true);
        $('#add-new').val('add');
        var GATranslate = $('#GA-l10n-enable-button').text();
        $('#GA-labelActiveDisplay').html(GATranslate);
        var GASetDisplay = $('#GA-setDisplay');
        $(GASetDisplay).prop('checked', false);
        var $groups = $('#GA-setGroups');
        $groups.val('');
        OC.Settings.setupGroupsSelect($groups);
        $('.GA-content-values').show(700);

    });


    //display the preview message
    $('#GA-preview').click(function(){
        var msg = $('#GA-setMsg').val();
        $('#GA-setMsg-form').append('<div class="GA-message GA-preview" id="GA-message-preview" style="display: block;">\n' +
            '\t<div class="GA-close" id="GA-close-preview"><img class="svg" src="../../core/img/actions/close.svg" alt="x"></div>\n' +
            '\t<div class="GA-message-content">'+msg+'</div>\n' +
            '</div>');

        $('.GA-message').hide();
        $('.GA-preview').fadeIn(450, function(){

        });
        $('#cboxOverlay').show();
        $('#cboxOverlay').css({
            'opacity': '0.4',
            'cursor': 'pointer',
            'visibility': 'visible',
            'display' : 'block'
        });

    });

    //close preview message
    $(document).on('click', '#GA-close-preview', function() {
        $('.GA-preview').fadeOut(450, function(){
            $('.GA-preview').remove();
        });

        $('#cboxOverlay').fadeOut(450, function(){
        });
    });

    //initialize select group field
    var $GATargetGroups = $('#GA-setGroups');
    OC.Settings.setupGroupsSelect($GATargetGroups);




    //SAVE MESSAGE (create and update)
    $(document).on('click', '#GA-submit', function(e) {
        $('#GA-error-content').html('');
        var error = false;
        //retrieve values
        var form = $('#GA-setMsg-form');
        var groups = $(form).find('#GA-setGroups').val();
        var title = $(form).find('#GA-setTitle').val();
        var message = $(form).find('#GA-setMsg').val();
        var folder = $(form).find('#GA-folder').val();

        var idMsg = parseInt($('#GA-selectList').val());

        var sharedWith = $(form).find('#GA-folder option:selected').data('groups');


        if (typeof title === 'undefined' || title === '') {
            error = true;
            $(form).find('#GA-setTitle').addClass('error');
        }else {
            $(form).find('#GA-setTitle').removeClass('error');
        }
        if (typeof message === 'undefined' || message === '') {
            error = true;
            $(form).find('#GA-setMsg').addClass('error');
        }else {
            $(form).find('#GA-setMsg').removeClass('error');
        }


        if (typeof groups === 'undefined' || groups === '' || !groups) {
            error = true;
            $(form).find('.select2-choices').addClass('error');

        }else {
            $(form).find('.select2-choices').removeClass('error');
        }
        if (typeof folder === 'undefined' || folder === '' || !folder) {
            error =true;
            $(form).find('#GA-folder').addClass('error');
        }else {
            $(form).find('#GA-folder').removeClass('error');

        }
        if (error) {
           $('#GA-error-content').append('<div class="required-error">'+requiredValue+'</div>');
            return false;
        }else {
            $('.required-error').remove()
        }





        if ($(form).find('#GA-setDisplay').prop('checked')) {
            display = 'true';
        }else {
            display = 'false';
        }


        if (folder !== '/'){
            folder = '/'+folder;
            sharedWith = sharedWith.join('|');
        }

        //save new message
        if ($('#add-new').val() === 'add') {
            data = "sharedWith="+sharedWith+"&groups="+groups+"&title="+title+"&texte="+message+"&folder="+folder+"&checked="+display;

        }
        //update existing message
        else if (idMsg !== 0) {
            data = "id="+idMsg+"&sharedWith="+sharedWith+"&groups="+groups+"&title="+title+"&texte="+message+"&folder="+folder+"&checked="+display;
        }

        $.ajax({
            url: OC.getRootPath() + '/apps/groupalert/ajax/settings.php',
            type: 'GET',
            dataType: 'html',
            data: data,
            cache: false
        })
        .done(function(response){
            response = JSON.parse(response);

            if (response.type === 'create' && typeof response.error === 'undefined') {
                //hydrate delete button id value and show delete button
                $('#GA-delete').val(response.id);
                var dateMsg = response.date;
                $('.GA-delete').show();
                //hydrate select message list
                $('#GA-selectList').append('<option selected value="' + response.id + '">' + title + ' ' + dateMsg + '</option>');
                //remove hidden add input
                $('#add-new').val('');
                //display a message to notify if new created message is disabled
                if (display === 'false') {
                    $('#GA-prompt-content').html(PromptSave);
                    $('.GA-prompt').fadeIn(450, function () {

                    });
                    $('#cboxOverlay').show();
                    $('#cboxOverlay').css({
                        'opacity': '0.4',
                        'cursor': 'pointer',
                        'visibility': 'visible',
                        'display': 'block'
                    });
                }
                //notify success
                OC.Notification.showTemporary(t('settings', notificationAdd), {timeout: 2});
            }
            else if (response.type === 'update' && typeof response.error === 'undefined') {
                //modify title in select message list
                $('#GA-selectList').find('option[value="'+idMsg+'"]').html(title+' '+response.date);
                //notify success
                OC.Notification.showTemporary(t('settings', notificationSave), {timeout: 2});
            }
            else if(typeof response.error !== 'undefined') {
                var GATranslate = '';
                if (response.type === 'update') {
                    GATranslate = errorContextUpdate;
                }else if (response.type === 'create') {
                    GATranslate = errorContextCreate;
                }
                if (response.error === 'exist') {
                    GATranslate += ' '+errorExist;

                }
                if (response.error === 'share') {
                    GATranslate += ' '+errorShare;
                }
                $('#GA-error-content').html(GATranslate);
            }

        });
    });

    //SAVE WHEN ENABLING/DISABLING (if message has already been saved)
    $("#GA-setDisplay").on("change", function(){
        var idMsg = parseInt($('#GA-selectList option:selected').val());
        var checkedVal;
        if ($(this).prop('checked')) {
            checkedVal = 'true';
        }else {
            checkedVal = 'false';
        }

        if (typeof idMsg !== 'undefined' && idMsg !== 0){
            $.ajax({
                url: OC.getRootPath() + '/apps/groupalert/ajax/settings.php',
                type: 'GET',
                dataType: 'html',
                data: 'id='+idMsg+'&checked='+checkedVal,
                cache: false,
            })
            .done(function(response) {
                if (checkedVal === 'true') {
                    OC.Notification.showTemporary(t('settings', notificationEnable), {timeout: 2});
                }else {
                    OC.Notification.showTemporary(t('settings', notificationDisable), {timeout: 2});
                }

            })
            .fail(function() {

            })
            .always(function() {

            });
        }
    });

    //DELETE MESSAGE
    $('#GA-delete-button').on('click', function(e) {

        $('#GA-prompt-content').html(PromptDelete);
        $('#GA-prompt-content').append('<label class="label-button" id="confirm-delete">' + confirmButton + '</label>');
        $('.GA-prompt').fadeIn(450, function () {

        });
        $('#cboxOverlay').show();
        $('#cboxOverlay').css({
            'opacity': '0.4',
            'cursor': 'pointer',
            'visibility': 'visible',
            'display': 'block'
        });
    });

    $(document).on('click', '#confirm-delete', function(e) {
        $('#GA-prompt-content').html('');
        $('.GA-prompt').hide();
        $('#cboxOverlay').hide();
        var idMsg = parseInt($('#GA-delete-button').closest(".GA-delete").find("#GA-delete").val());
        if (idMsg != 0) {
            data = 'delete=delete&id='+idMsg;
        }else {
            return false;
        }
        $.ajax({
            url: OC.getRootPath() + '/apps/groupalert/ajax/settings.php',
            type: 'GET',
            dataType: 'html',
            data: data,
            cache: false
        })
        .done(function(response){
            $('#GA-selectList').find('option[value="'+idMsg+'"]').remove();
            $('#GA-selectList').find('option[value="0"]').prop('selected', true);
            $('.GA-content-values').hide(500);
            OC.Notification.showTemporary(t('settings', notificationDelete), {timeout: 2});

        });

    });

    //UPDATE ENABLE/DISABLE BUTTON
    $("#GA-setDisplay").on("change", function(){
        var checkedVal;
        var GATranslate;
        if ($(this).prop('checked')) {
            checkedVal = 'true';
            GATranslate = $('#GA-l10n-disable-button').text();
            $('#GA-labelActiveDisplay').html(GATranslate);
        }else {
            checkedVal = 'false';
            GATranslate = $('#GA-l10n-enable-button').text();
            $('#GA-labelActiveDisplay').html(GATranslate);
        }
    });


    //HYDRATE FIELDS AND ADAPT VIEW ON MESSAGE SELECTION
    $(document).on('change', '#GA-selectList',  function(e){
        //reset
        $('#add-new').val('');
        $('#GA-error-content').html('');
        $.each($('.error'), function(key, value){
           $(this).removeClass('error');
        });

        var idMsg = parseInt($(this).val());
        $.ajax({
            url: OC.getRootPath() + '/apps/groupalert/lib/settings.json',
            type: 'GET',
            dataType: 'json',
            cache: false
        })
        .done(function(data){
            $.each(data, function(key, entry){
                if(data[key].id === idMsg) {
                    //show disable/enable button
                    $('.GA-buttons').show();
                    $('#GA-delete').val(idMsg);
                    $('.GA-delete').show();
                    //hydrate basic fields
                    $('#GA-setTitle').val(data[key].title);
                    $('#GA-setMsg').val(data[key].texte);
                    if (data[key].folder !== '/'){
                        $('#GA-folder').val(data[key].folder.split('/')[1]);
                    }else {
                        $('#GA-folder').val(data[key].folder);
                    }
                    //change enable/disable button with data value
                    var GASetDisplay = $('#GA-setDisplay');
                    $(GASetDisplay).val(data[key].checked);
                    var GATranslate;
                    if (data[key].checked === 'true') {
                        GATranslate = $('#GA-l10n-disable-button').text();
                        $('#GA-labelActiveDisplay').html(GATranslate);
                        $(GASetDisplay).prop('checked', true);
                    }else {
                        GATranslate = $('#GA-l10n-enable-button').text();
                        $('#GA-labelActiveDisplay').html(GATranslate);
                    }


                    //hydrate groups field
                    //(replace is not really usefull anymore here. keep it for secure)
                    var groups = data[key].groups.replace(',','|');
                    var $groups = $('#GA-setGroups');
                    $groups.val(groups);
                    OC.Settings.setupGroupsSelect($groups);


                    //prepare preview
                    $('#GA-message-content').html(data[key].texte);

                    //display hydrated fields
                    $('.GA-content-values').show(700);

                }
            })
        });
    });

    //close prompt message
    $(document).on('click', '#GA-close-prompt', function() {
        $('#GA-prompt-content').html('');
        $('.GA-prompt').fadeOut(450, function(){
        });
        $('#cboxOverlay').fadeOut(450, function(){
        });
    });

});