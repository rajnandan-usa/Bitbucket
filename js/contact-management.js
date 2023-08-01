jQuery(document).ready(function ($) {
    $.validator.addMethod('phone_msg', function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, ''); 
        return this.optional(element) || phone_number.match(/^\d{10}$/); 
    }, 'Please enter a valid 10-digit phone number.');

    $('#contact-form').validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            phone_number: {
                required: true,
                phone_msg: true 
            }
        },
        messages: {
            first_name: {
                required: 'Please enter your first name.'
            },
            last_name: {
                required: 'Please enter your last name.'
            },
            email: {
                required: 'Please enter your email address.',
                email: 'Please enter a valid email address.'
            },
            phone_number: {
                required: 'Please enter your phone number.',
                phone_msg: 'Please enter a valid 10-digit phone number.'
            }
        },
        submitHandler: function (form) {
            var formData = $(form).serialize();

            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: formData + '&action=save_contact&security=' + ajax_object.security,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#success-message').text('Form submitted successfully.');
                        form.reset();
                    } else {
                        $('#success-message').text('Error saving contact. Please try again later.');
                    }
                },
                error: function () {
                    $('#success-message').text('Error saving contact. Please try again later.');
                }
            });
        }
    });
});
