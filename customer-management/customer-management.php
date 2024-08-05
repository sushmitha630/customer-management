<?php
/*
Plugin Name: Customer Management
Description: A custom plugin to manage customers.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'includes/customer-functions.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';

// Activation Hook: Create custom table
function cm_activate_plugin() {
    cm_create_customer_table();
}
register_activation_hook(__FILE__, 'cm_activate_plugin');

// Deactivation Hook: Cleanup actions
function cm_deactivate_plugin() {
    // Optional: Drop custom tables if needed
}
register_deactivation_hook(__FILE__, 'cm_deactivate_plugin');

// Admin Menu
function cm_add_admin_menu() {
    add_menu_page('Customer Management', 'Customers', 'manage_options', 'customer-management', 'cm_admin_page', 'dashicons-admin-users', 6);
}
add_action('admin_menu', 'cm_add_admin_menu');

// Enqueue Admin Styles
function cm_enqueue_admin_styles() {
    wp_enqueue_style('cm-admin-style', plugin_dir_url(__FILE__) . 'css/admin-style.css');
}
add_action('admin_enqueue_scripts', 'cm_enqueue_admin_styles');

// Enqueue Frontend Scripts
function cm_enqueue_frontend_scripts() {
    wp_enqueue_script('cm-frontend', plugin_dir_url(__FILE__) . 'js/frontend.js', array('jquery'), null, true);
    wp_localize_script('cm-frontend', 'cm_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cm_ajax_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'cm_enqueue_frontend_scripts');

// Register Shortcodes
function cm_register_shortcodes() {
    add_shortcode('customer_list', 'cm_customer_list_shortcode');
    add_shortcode('active_customers', 'cm_active_customers_shortcode');
}
add_action('init', 'cm_register_shortcodes');

// Shortcode function to render customer list
function cm_customer_list_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/customer-list.php';
    return ob_get_clean();
}

// Shortcode function for active customers with AJAX pagination and search
function cm_active_customers_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/active-customers.php';
    return ob_get_clean();
}

// AJAX Handler for fetching active customers
function cm_fetch_active_customers() {
    check_ajax_referer('cm_ajax_nonce', 'nonce');

    $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $customers_per_page = 10;
    $offset = ($paged - 1) * $customers_per_page;

    global $wpdb;
    $table_name = $wpdb->prefix . 'customers';
    $search_sql = $wpdb->prepare("WHERE name LIKE %s OR email LIKE %s OR phone LIKE %s", '%' . $wpdb->esc_like($search_query) . '%', '%' . $wpdb->esc_like($search_query) . '%', '%' . $wpdb->esc_like($search_query) . '%');

    $total_customers = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $search_sql");
    $total_pages = ceil($total_customers / $customers_per_page);

    $customers = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name $search_sql ORDER BY created_at DESC LIMIT %d OFFSET %d", $customers_per_page, $offset));

    $response = array(
        'customers' => $customers,
        'total_pages' => $total_pages,
        'current_page' => $paged,
    );

    wp_send_json_success($response);
}
add_action('wp_ajax_cm_fetch_active_customers', 'cm_fetch_active_customers');
add_action('wp_ajax_nopriv_cm_fetch_active_customers', 'cm_fetch_active_customers');
?>

