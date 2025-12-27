$('#cartTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [5, 10, 25, 50],
            "language": {
                "search": "Search Cart:",
                "lengthMenu": "Show _MENU_ items per page"
            }
        });

        // Handle delete button click
        $('.delete-btn').click(function() {
            let itemId = $(this).data('id');
            let productName = $(this).data('name');

            // Update modal content
            $('#productName').text(productName);
            $('#confirmDeleteBtn').attr('href', '{{ route('cart.remove', ':id') }}'.replace(':id', itemId));
        });

        $('#selectAll').change(function() {
            let isChecked = $(this).prop('checked');
            $('.item-checkbox').each(function() {
                if (!$(this).prop('disabled')) {
                    $(this).prop('checked', isChecked);
                }
            });
            updateTotal();
        });

        $('.item-checkbox').change(function() {
            updateTotal();
        });

        // Update total price and grand total when checkboxes change
        function updateTotal() {
            let total = 0;
            let anyChecked = false;
            let selectedItems = [];

            $('.item-checkbox:checked').each(function() {
                let row = $(this).closest('tr');
                let quantity = parseInt(row.find('.quantity-input').val());
                let pricePerUnit = parseFloat(row.find('.quantity-input').data('price'));
                total += quantity * pricePerUnit; // Calculate total based on quantity and price per unit
                selectedItems.push(row.find('.quantity-input').data('id')); // Get item ID
                anyChecked = true;
            });

            $('#grandTotalAmount').text('$' + total.toFixed(2).toLocaleString()); // Update grand total display
            $('#selectedItemsInput').val(selectedItems.join(',')); // Store selected items

            if (anyChecked) {
                $('#grandTotalText').text('Buy Now:');
                $('#buyNowBtn').removeClass('d-none');
            } else {
                $('#grandTotalText').text('Grand Total:');
                $('#buyNowBtn').addClass('d-none');
            }
        }

        // Ensure selected items are updated before form submission
        $('#buyNowForm').on('submit', function() {
            let selectedItems = [];
            let quantities = [];

            $('.item-checkbox:checked').each(function() {
                let row = $(this).closest('tr');
                selectedItems.push(row.find('.quantity-input').data('id')); // Get item ID
                quantities.push(row.find('.quantity-input').val()); // Get quantity
            });

            $('#selectedItemsInput').val(selectedItems.join(',')); // Store selected items
            $('#quantitiesInput').val(quantities.join(',')); // Store quantities
        });

        // Function to check and update stock indicator color and quantity
        function checkStockIndicator() {
            $('.quantity-input').each(function() {
                let maxStock = parseInt($(this).data('stock'));
                let currentQuantity = parseInt($(this).val());
                let stockElement = $(this).closest('tr').find('td:nth-child(4) .stock-indicator');
                let checkbox = $(this).closest('tr').find('.item-checkbox');

                // Highlight stock in red if quantity exceeds stock
                if (currentQuantity > maxStock || maxStock <= 0) {
                    stockElement.removeClass('text-success').addClass('text-danger'); // Change to red
                    checkbox.prop('disabled', true).prop('checked', false); // Disable checkbox
                } else {
                    stockElement.removeClass('text-danger').addClass('text-success'); // Change back to green
                    checkbox.prop('disabled', false); // Enable checkbox
                }
            });
        }

        // Update total price and grand total when quantity changes
        $('.quantity-input').on('input', function() {
            let maxStock = parseInt($(this).data('stock'));
            let newQuantity = parseInt($(this).val());

            // Ensure quantity is within valid range and not null/empty
            if (!newQuantity || newQuantity < 1) {
                newQuantity = 1; // Set minimum quantity to 1
                $(this).val(newQuantity);
            } else if (newQuantity > maxStock) {
                newQuantity = maxStock; // Set quantity to max stock
                $(this).val(newQuantity);
            }

            // Check and update stock indicator
            checkStockIndicator();

            // Update total price for the row
            let pricePerUnit = parseFloat($(this).data('price'));
            let totalPrice = newQuantity * pricePerUnit;
            $(this).closest('tr').find('td:nth-child(7) strong').text('$' + totalPrice.toFixed(2));

            // Update grand total
            updateTotal();
        });

        function updateGrandTotal() {
            let grandTotal = 0;

            $('.quantity-input').each(function() {
                let quantity = parseInt($(this).val());
                let pricePerUnit = parseFloat($(this).data('price'));
                let maxStock = parseInt($(this).data('stock'));

                // Exclude rows where quantity exceeds stock
                if (quantity <= maxStock) {
                    grandTotal += quantity * pricePerUnit;
                }
            });

            $('#grandTotalAmount').text('$' + grandTotal.toFixed(2));
        }

        // Initial computation and stock check on page load
        checkStockIndicator();
        updateTotal();