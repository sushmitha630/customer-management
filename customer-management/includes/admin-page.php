<?php

function cm_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customers';

    // Check if we're editing a customer
    $edit_customer = false;
    if (isset($_GET['edit'])) {
        $edit_customer = cm_get_customer(intval($_GET['edit']));
    }

    // Handle form submission for adding/updating customer
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['cm_name'];
        $email = $_POST['cm_email'];
        $phone = $_POST['cm_phone'];
        $notes = $_POST['cm_notes'];

        if (isset($_POST['cm_id'])) {
            // Update customer
            $id = intval($_POST['cm_id']);
            cm_update_customer($id, $name, $email, $phone, $notes);
        } else {
            // Add new customer
            $result = cm_add_customer($name, $email, $phone, $notes);
            if (is_wp_error($result)) {
                echo '<div class="error"><p>' . $result->get_error_message() . '</p></div>';
            } else {
                echo "<script>location.href = location.pathname;</script>";
            }
        }
    }

    // Handle delete request
    if (isset($_GET['delete'])) {
        cm_delete_customer(intval($_GET['delete']));
        echo "<script>location.href = location.pathname;</script>";
    }

    // Pagination and Search
    $customers_per_page = 10;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $customers_per_page;

    $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $search_sql = !empty($search_query) ? $wpdb->prepare("WHERE name LIKE %s OR email LIKE %s OR phone LIKE %s", '%' . $wpdb->esc_like($search_query) . '%', '%' . $wpdb->esc_like($search_query) . '%', '%' . $wpdb->esc_like($search_query) . '%') : '';

    $total_customers = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $search_sql");
    $total_pages = ceil($total_customers / $customers_per_page);

    $customers = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name $search_sql ORDER BY created_at DESC LIMIT %d OFFSET %d", $customers_per_page, $offset));

    ?>
    <div class="wrap">
        <h1><?php echo $edit_customer ? 'Edit Customer' : 'Add New Customer'; ?></h1>
        <form method="post" action="">
            <input type="hidden" name="cm_id" value="<?php echo $edit_customer ? $edit_customer->id : ''; ?>">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Name</th>
                    <td><input type="text" name="cm_name" value="<?php echo $edit_customer ? esc_attr($edit_customer->name) : ''; ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Email</th>
                    <td><input type="email" name="cm_email" value="<?php echo $edit_customer ? esc_attr($edit_customer->email) : ''; ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Phone</th>
                    <td><input type="text" name="cm_phone" value="<?php echo $edit_customer ? esc_attr($edit_customer->phone) : ''; ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Notes</th>
                    <td><textarea name="cm_notes"><?php echo $edit_customer ? esc_textarea($edit_customer->notes) : ''; ?></textarea></td>
                </tr>
            </table>
            <?php submit_button($edit_customer ? 'Update Customer' : 'Add Customer'); ?>
        </form>

        <h2>Customer List</h2>
        <form method="get">
            <input type="hidden" name="page" value="customer-management" />
            <p class="search-box">
                <input type="search" name="s" value="<?php echo esc_attr($search_query); ?>" />
                <input type="submit" class="button" value="Search Customers" />
            </p>
        </form>

        <table class="widefat">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Notes</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($customers) {
                    foreach ($customers as $customer) {
                        echo "<tr>
                            <td>{$customer->name}</td>
                            <td>{$customer->email}</td>
                            <td>{$customer->phone}</td>
                            <td>{$customer->notes}</td>
                            <td>{$customer->created_at}</td>
                            <td>
                                <a href='?page=customer-management&edit={$customer->id}'>Edit</a> |
                                <a href='?page=customer-management&delete={$customer->id}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo '<tr><td colspan="6">No customers found.</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <div class="tablenav">
            <div class="tablenav-pages">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => $total_pages,
                    'current' => $current_page
                ));
                ?>
            </div>
        </div>
    </div>
    <?php
}
?>
