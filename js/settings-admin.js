function updatePrompt(appUrl) {

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
                //console.log(response);
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

    var appUrl = $('#GA-appUrl').val()

    //hydrate form fields with current values and display button content
    $.getJSON(appUrl + 'lib/settings.json', function(data) {
        $('#GA-setMsg').val(data.texte);
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
                //console.log(appUrl);
            })
            .fail(function() {
                //console.log("error");
            })
            .always(function() {
                //console.log("complete");
            });
    });

    updatePrompt(appUrl);

});