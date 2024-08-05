<?php
// Template for displaying a list of customers

global $wpdb;
$table_name = $wpdb->prefix . 'customers';

// Fetch all customers
$customers = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

if ($customers):
    ?>
    <div class="customer-list">
        <h2>Customer List</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Notes</th>
                    <th>Date Added</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo esc_html($customer->name); ?></td>
                        <td><?php echo esc_html($customer->email); ?></td>
                        <td><?php echo esc_html($customer->phone); ?></td>
                        <td><?php echo esc_html($customer->notes); ?></td>
                        <td><?php echo esc_html($customer->created_at); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
else:
    echo '<p>No customers found.</p>';
endif;
?>
