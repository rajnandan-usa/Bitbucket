jQuery(document).ready(function ($) {
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
                required: true
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
                required: 'Please enter your phone number.'
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
                    $('#contact-form-message').text(response.message);
                    if (response.success) {
                        form.reset();
                    }
                },
                error: function () {
                    $('#contact-form-message').text('Error saving contact. Please try again later.');
                }
            });
        }
    });

    // // Handle contact delete via AJAX.
    // jQuery(document).ready(function($) {
    //     $(".delete-contact").on("click", function(e) {
    //         e.preventDefault();
    //         console.log('JavaScript file is loaded and working.');
    //         if (confirm("Are you sure you want to delete this contact?")) {
    //             var data = new URLSearchParams();
    //             data.append('action', 'delete_contact');
    //             data.append('security', ajax_object.delete_contact_nonce);
    //             data.append('id', contactId);
    //             fetch(ajax_object.ajax_url, {
    //                 method: "POST",
    //                 body: data,
    //                 headers: {
    //                     'Content-Type': 'application/x-www-form-urlencoded',
    //                 },
    //             })
    //             .then(response => {
    //                 if (!response.ok) {
    //                     throw new Error('Network response was not ok');
    //                 }
    //                 return response.json();
    //             })
    //             .then(data => {
    //                 console.log(data);
    //                 if (data.success) {
    //                     // Reload the page to update the contact list
    //                     location.reload();
    //                 } else {
    //                     alert('Failed to delete the contact: ' + data.message);
    //                 }
    //             })
    //             .catch(error => {
    //                 console.error('Error:', error);
    //             });
    //         }
    //     });
    // });
    
    
});
