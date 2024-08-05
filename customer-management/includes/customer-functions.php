<?php

// Function to create the customer table
function cm_create_customer_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customers';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(15) NOT NULL,
        notes text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Function to add a customer to the database
function cm_add_customer($name, $email, $phone, $notes) {
    global $wpdb;

    // Check if the email already exists in the WordPress users
    if (email_exists($email)) {
        return new WP_Error('email_exists', 'This email address is already in use.');
    }

    // Create WordPress user
    $user_id = wp_create_user($email, $phone, $email);
    if (is_wp_error($user_id)) {
        return $user_id; // Return error if user creation failed
    }

    // Set the user role to contributor
    wp_update_user(array('ID' => $user_id, 'role' => 'contributor'));

    // Add customer to custom table
    $table_name = $wpdb->prefix . 'customers';
    $wpdb->insert($table_name, array(
        'name' => sanitize_text_field($name),
        'email' => sanitize_email($email),
        'phone' => sanitize_text_field($phone),
        'notes' => sanitize_textarea_field($notes),
        'created_at' => current_time('mysql')
    ));

    return $wpdb->insert_id; // Return the ID of the newly added customer
}

// Function to retrieve all customers from the database
function cm_get_customers() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customers';
    return $wpdb->get_results("SELECT * FROM $table_name");
}

// Function to get a customer by ID
function cm_get_customer($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customers';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
}

// Function to update a customer in the database
function cm_update_customer($id, $name, $email, $phone, $notes) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customers';

    $wpdb->update(
        $table_name,
        array(
            'name' => sanitize_text_field($name),
            'email' => sanitize_email($email),
            'phone' => sanitize_text_field($phone),
            'notes' => sanitize_textarea_field($notes),
        ),
        array('id' => $id)
    );
}

// Function to delete a customer from the database
function cm_delete_customer($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customers';
    $wpdb->delete($table_name, array('id' => $id));
}
?>
