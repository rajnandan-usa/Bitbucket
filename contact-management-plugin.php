<?php
/**
 * Plugin Name: Contact Management Plugin
 * Description: Simple WordPress plugin for contact management.
 * Version: 1.0
 * Author: Rajnandan Kushwaha
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'class-contact-management-plugin.php';

register_activation_hook(__FILE__, array('Contact_Management_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('Contact_Management_Plugin', 'deactivate'));

add_action('plugins_loaded', function () {
    new Contact_Management_Plugin();
});
