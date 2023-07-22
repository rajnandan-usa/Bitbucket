jQuery(document).ready(function ($) {
    $('#contact-form').submit(function (event) {
        event.preventDefault();
        var form = $(this);
        var formData = form.serialize();

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: formData + '&action=save_contact&security=' + ajax_object.security,
            dataType: 'json',
            success: function (response) {
                $('#contact-form-message').text(response.data.message);
                if (response.success) {
                    form.trigger('reset');
                }
            },
            error: function () {
                $('#contact-form-message').text('Error saving contact. Please try again later.');
            }
        });
    });
});
