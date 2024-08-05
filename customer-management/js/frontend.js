jQuery(document).ready(function($) {
    function loadCustomers(page = 1, search = '') {
        $.ajax({
            url: cm_ajax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'cm_fetch_active_customers',
                nonce: cm_ajax.nonce,
                paged: page,
                search: search
            },
            success: function(response) {
                if (response.success) {
                    var customers = response.data.customers;
                    var total_pages = response.data.total_pages;
                    var current_page = response.data.current_page;

                    var customerTable = $('#cm-customer-list tbody');
                    customerTable.empty();

                    if (customers.length > 0) {
                        customers.forEach(function(customer) {
                            customerTable.append('<tr><td>' + customer.name + '</td><td>' + customer.email + '</td><td>' + customer.phone + '</td><td>' + customer.notes + '</td><td>' + customer.created_at + '</td></tr>');
                        });
                    } else {
                        customerTable.append('<tr><td colspan="5">No customers found.</td></tr>');
                    }

                    // Pagination
                    var pagination = $('#cm-pagination');
                    pagination.empty();

                    if (total_pages > 1) {
                        for (var i = 1; i <= total_pages; i++) {
                            pagination.append('<a href="#" class="page-numbers' + (i === current_page ? ' current' : '') + '">' + i + '</a>');
                        }
                    }
                }
            }
        });
    }

    loadCustomers(); // Initial load

    // Handle pagination click
    $(document).on('click', '#cm-pagination a.page-numbers', function(e) {
        e.preventDefault();
        var page = $(this).text();
        loadCustomers(page);
    });

    // Handle search
    $('#cm-search-form').on('submit', function(e) {
        e.preventDefault();
        var search = $('#cm-search-input').val();
        loadCustomers(1, search);
    });
});
