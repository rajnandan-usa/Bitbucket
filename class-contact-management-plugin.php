<?php
class Contact_Management_Plugin
{
    //Private Property
    private $table_name;

    //Constructor
    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'contacts';

        add_action('admin_menu', array($this, 'register_admin_menu'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_save_contact', array($this, 'save_contact'));
        add_action('wp_ajax_nopriv_save_contact', array($this, 'save_contact'));
        add_shortcode('contact_form', array($this, 'contact_form_shortcode'));
       
        
    }
    //For activating plugin and creating table in database

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

    //for deavtivating plugin and drop table from database
    public static function deactivate()
{
    delete_option('contact_management_plugin_settings');
    global $wpdb;
    $table_name = $wpdb->prefix . 'contacts';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

//for adding new in admin sidebar menu

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
        'contact-management-scripts',
        plugin_dir_url(__FILE__) . 'js/contact-management.js',
        array('jquery'),
        '1.0',
        true
    );
    wp_localize_script('contact-management-scripts', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('save_contact_nonce')
    ));
}

      //admin page data list
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact) : ?>
                        <tr>
                            <td><?php echo $contact->id; ?></td>
                            <td><?php echo $contact->email; ?></td>
                            <td><?php echo $contact->first_name; ?></td>
                            <td><?php echo $contact->last_name; ?></td>
                            <td><?php echo $contact->phone_number; ?></td>
                            <td><?php echo $contact->address; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    //data insert 
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
            wp_send_json_success(array('message' => 'Contact saved successfully.'));
        } else {
            wp_send_json_error(array('message' => 'Error saving contact. Please try again later.'));
        }
    } else {
        wp_send_json_error(array('message' => 'Invalid security token.'));
    }
}

 //shortcode for frontend form
    public function contact_form_shortcode()
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/contact_form.php';
        return ob_get_clean();
    }
}

add_shortcode('contact_form', 'contact_form_shortcode');
