<?php
require_once('../../../wp-load.php');

// AJAX action for saving contact
add_action('wp_ajax_save_contact', 'save_contact');
add_action('wp_ajax_nopriv_save_contact', 'save_contact');

// // AJAX action for deleting contact
add_action('wp_ajax_delete_contact', 'delete_contact');
add_action('wp_ajax_nopriv_delete_contact', 'delete_contact');

function save_contact()
{
    if (class_exists('Contact_Management_Plugin')) {
        $contact_management_plugin = new Contact_Management_Plugin();
        $contact_management_plugin->save_contact();
    }

    wp_die();
}

function delete_contact()
{

   if (!wp_doing_ajax()) {
    wp_send_json_error(array('message' => 'Invalid request.'), 400);
    return;
}

$nonce = isset($_POST['security']) ? sanitize_text_field($_POST['security']) : '';
if (!wp_verify_nonce($nonce, 'delete_contact_nonce')) {
    wp_send_json_error(array('message' => 'Invalid security nonce.'), 403);
    return;
}

$contact_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
if ($contact_id === 0) {
    wp_send_json_error(array('message' => 'Invalid contact ID.'), 400);
    return;
}

global $wpdb;
$table_name = $wpdb->prefix . 'contacts';
$deleted = $wpdb->delete($table_name, array('id' => $contact_id), array('%d'));

if ($deleted !== false) {
    wp_send_json_success(array('message' => 'Contact deleted successfully.'), 200);
} else {
    wp_send_json_error(array('message' => 'Failed to delete the contact.'), 500);
}

wp_die();
}
