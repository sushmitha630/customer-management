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
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>CR Number</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>status</th>

                </tr>
            </thead>

          
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo esc_html($customer->name); ?></td>
                        <td><?php echo esc_html($customer->email); ?></td>
                        <td><?php echo esc_html($customer->phone); ?></td>
                        <td><?php echo esc_html($customer->dob); ?></td>
                        <td><?php echo esc_html($customer->gender); ?></td>
                         <td><?php echo esc_html($customer->cr_number); ?></td>
                        <td><?php echo esc_html($customer->address); ?></td>
                        <td><?php echo esc_html($customer->city); ?></td>
                        <td><?php echo esc_html($customer->country); ?></td>
                        <td><?php echo esc_html($customer->status); ?></td>
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
