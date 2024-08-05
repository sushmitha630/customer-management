<div class="cm-active-customers">
    <h2>Active Customers</h2>
    <form id="cm-search-form">
        <input type="text" id="cm-search-input" placeholder="Search customers..." />
        <button type="submit">Search</button>
    </form>

    <table id="cm-customer-list">
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
                <th>Date Added</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be populated here via AJAX -->
        </tbody>
    </table>

    <div id="cm-pagination">
        <!-- Pagination will be populated here via AJAX -->
    </div>
</div>
