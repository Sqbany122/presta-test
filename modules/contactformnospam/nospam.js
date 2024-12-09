$(function() {
    $('#submitMessage').click(function(e) {
        $('form.contact-form-box').append('<input type="hidden" value="true" name="contactformnospan" />');
    });
});
