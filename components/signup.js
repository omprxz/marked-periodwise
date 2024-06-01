$(document).ready(function () {
    $(".toggle-password").click(function () {
        var targetId = $(this).data("target");
        var passwordField = $("#" + targetId);
        var fieldType = passwordField.attr("type") === "password" ? "text" : "password";
        passwordField.attr("type", fieldType);
        $(this).find("i").toggleClass("fa-eye fa-eye-slash");
    });
});

$("#role").on("change", function() {
    if ($(this).val() == "student") {
        $('.student-dets-div').css('display', 'block');
        $('.student-dets-input').attr('required', true)
    } else {
        $('.student-dets-div').css('display', 'none');
        $('.student-dets-input').removeAttr('required')
    }
});
