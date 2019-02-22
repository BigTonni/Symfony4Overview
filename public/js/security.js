$(document).ready(function() {
    let valEmail = $.trim($("#registration_form_email").val());
    if (valEmail.length > 0) {
        $("#registration_form_username").val(valEmail);
    }
    //Clone text
    $("#registration_form_email").on('keypress change', function() {
        $("#registration_form_username").val($(this).val());
    });
})