<?php
require_once('../../../wp-load.php');


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
