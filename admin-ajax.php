<?php
require_once('../../../wp-load.php');

add_action('wp_ajax_contact_management_save_contact', 'contact_management_save_contact');
add_action('wp_ajax_nopriv_contact_management_save_contact', 'contact_management_save_contact');

add_action('wp_ajax_contact_management_delete_contact', 'contact_management_delete_contact');
add_action('wp_ajax_nopriv_contact_management_delete_contact', 'contact_management_delete_contact');

function contact_management_save_contact()
{
    if (class_exists('Contact_Management_Plugin')) {
        $contact_management_plugin = new Contact_Management_Plugin();
        $contact_management_plugin->save_contact();
    }

    wp_die();
}

function contact_management_delete_contact()
{
    if (class_exists('Contact_Management_Plugin')) {
        $contact_management_plugin = new Contact_Management_Plugin();
        $contact_management_plugin->delete_contact();
    }

    wp_die();
}
