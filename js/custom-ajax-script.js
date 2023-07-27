jQuery(document).ready(function($) {

    $(".delete-contact").on("click", function(e) {
        e.preventDefault();
        var contactId = $(this).data("contact-id");
        if (confirm("Are you sure you want to delete this contact?")) {
            $.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: {
                    action: "delete_contact",
                    security: ajax_object.delete_contact_nonce,
                    id: contactId,
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(errorThrown) {
                    console.error(errorThrown);
                },
            });
        }
    });
});
