<?php
// Get the path to the plugin directory

class Contact_Management_Plugin
{
    // Private Property
    private $table_name;

    // Constructor
    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'contacts';

        add_action('admin_menu', array($this, 'register_admin_menu'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('contact_form', array($this, 'contact_form_shortcode'));
        add_action('admin_notices', array($this, 'display_shortcode_notice'));
        add_action('wp_ajax_save_contact', array($this, 'save_contact'));
        add_action('wp_ajax_nopriv_save_contact', array($this, 'save_contact'));
        add_action('wp_ajax_delete_contact', array($this, 'delete_contact'));
        add_action('wp_ajax_nopriv_delete_contact', array($this, 'delete_contact'));
    }

    // For activating plugin and creating table in the database
    public static function activate()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contacts';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id int(16) NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL,
            first_name varchar(255) NOT NULL,
            last_name varchar(255) NOT NULL,
            phone_number varchar(50) NOT NULL,
            address varchar(500),
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    // For deactivating plugin and dropping the table from the database
    public static function deactivate()
    {
        delete_option('contact_management_plugin_settings');
        global $wpdb;
        $table_name = $wpdb->prefix . 'contacts';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    // For adding a new item to the admin sidebar menu
    public function register_admin_menu()
    {
        add_menu_page(
            'Contact Management',
            'Contact Management',
            'manage_options',
            'contact-management',
            array($this, 'admin_page'),
            'dashicons-businessman',
            25
        );
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('jquery');
 
        wp_enqueue_script(
            'jquery-validate',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js',
            array('jquery'),
            '1.19.3',
            true
        );
 
        wp_enqueue_script(
            'contact-management-scripts',
            plugin_dir_url(__FILE__) . 'js/contact-management.js',
            array('jquery', 'jquery-validate'),
            '1.0',
            true
        );

        wp_localize_script('contact-management-scripts', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('save_contact_nonce'),
            'delete_contact_nonce' => wp_create_nonce('delete_contact_nonce'),
        ));
    
        wp_enqueue_style(
            'contact-form-style',
            plugin_dir_url(__FILE__) . 'css/contact-management.css',
            array(),
            '1.0'
        );
    }
    


    public function display_shortcode_notice()
    {
        global $hook_suffix;
        if ('toplevel_page_contact-management' === $hook_suffix) {
            ?>
            <div class="notice notice-info is-dismissible">
                <p><strong>Shortcode:</strong> [contact_form] for implementation in frontend</p>
            </div>
            <?php
        }
    }

    // Admin page data list
    public function admin_page()
    {
        global $wpdb;
        $contacts = $wpdb->get_results("SELECT * FROM $this->table_name");
        ?>
        <div class="wrap">
            <h1>Contact List</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact) : ?>
                        <tr id="contact-<?php echo $contact->id; ?>">
                            <td><?php echo $contact->id; ?></td>
                            <td><?php echo $contact->email; ?></td>
                            <td><?php echo $contact->first_name; ?></td>
                            <td><?php echo $contact->last_name; ?></td>
                            <td><?php echo $contact->phone_number; ?></td>
                            <td><?php echo $contact->address; ?></td>
                            <td>
                                <a href="#" class="delete-contact" data-contact-id="<?php echo $contact->id; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <script>
    jQuery(document).ready(function($) {
        $(".delete-contact").on("click", function(e) {
            e.preventDefault();
            var contactId = $(this).data("contact-id");
           
            if (confirm("Are you sure you want to delete this contact?")) {
                var data = new URLSearchParams();
                data.append('action', 'delete_contact');
                data.append('security', '<?php echo wp_create_nonce('delete_contact_nonce'); ?>');
                data.append('id', contactId);
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: "POST",
                    body: data,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    if (data.success) {
                       
                        location.reload();
                    } else {
                        alert('Failed to delete the contact: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });
</script>

    <?php
    }

    // Data insert
    public function save_contact()
    {
        global $wpdb;

        if (check_ajax_referer('save_contact_nonce', 'security', false)) {
            $email = sanitize_email($_POST['email']);
            $first_name = sanitize_text_field($_POST['first_name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $phone_number = sanitize_text_field($_POST['phone_number']);
            $address = sanitize_text_field($_POST['address']);

            $result = $wpdb->insert(
                $this->table_name,
                array(
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'phone_number' => $phone_number,
                    'address' => $address,
                ),
                array('%s', '%s', '%s', '%s', '%s')
            );
            if ($result) {
                error_log('Contact saved successfully.');
                wp_send_json_success(array('message' => 'Contact saved successfully.'));
            } else {
                error_log('Error saving contact. Please try again later.');
                wp_send_json_error(array('message' => 'Error saving contact. Please try again later.'));
            }
        } else {
            error_log('Invalid security token.');
            wp_send_json_error(array('message' => 'Invalid security token.'));
        }
    }

    
    public function delete_contact()
    {
        error_log('Function delete_contact called.');
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
    }

    // Shortcode for frontend form
    public function contact_form_shortcode()
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/contact_form.php';
        return ob_get_clean();
    }

}

add_shortcode('contact_form', 'contact_form_shortcode');
