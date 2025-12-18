@extends('layouts.layout')
<!-- website icon -->
    <link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">

@section('title')
    SiliconCove - cart
@endsection

@push('styles')
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .cart-icon:hover {
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .table-responsive {
                border: 0;
            }

            #cartTable {
                display: block;
            }

            #cartTable thead {
                display: none;
            }

            #cartTable tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
            }

            #cartTable tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem;
                border: none;
                border-bottom: 1px solid #dee2e6;
            }

            #cartTable tbody td:last-child {
                border-bottom: 0;
            }

            #cartTable tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                margin-right: 1rem;
            }

            .quantity-input {
                width: 100px;
            }

            #cartTable tfoot {
                display: block;
            }

            #cartTable tfoot tr {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                padding: 1rem;
            }

            #cartTable tfoot td {
                border: none;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: left;
                margin-bottom: 1rem;
            }

            .clear-cart-container {
                display: flex;
                justify-content: center;
                width: 100%;
                margin-top: 20px;
            }
        }
    </style>
@endpush

@include('includes.navbar');
@section('content')
    <div class="container">
        <div class="my-5 d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Your Cart</h2>
            <a href="{{ route('account.index') }}" class="text-decoration-none text-dark cart-icon">
                <i class="fa fa-shopping-bag fa-2x"></i>
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($cart->isEmpty())
            <div class="alert alert-warning text-center">
                <strong>Your cart is empty.</strong>
            </div>
        @else
            <div class="table-responsive">
                <table id="cartTable" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach ($cart as $item)
                            @php
                                $totalPrice = $item->quantity * $item->product->price;
                                $grandTotal += $totalPrice;
                                $isDisabled = $item->product->stock <= 0 || $item->quantity > $item->product->stock;
                            @endphp
                            <tr>
                                <td data-label="Select">
                                    <input type="checkbox" class="item-checkbox" data-price="{{ $totalPrice }}"
                                        {{ $isDisabled ? 'disabled' : '' }}>
                                </td>
                                <td data-label="Image">
                                    <img src="{{ asset('storage/' . $item->product->image) }}" class="img-thumbnail"
                                        width="50">
                                </td>
                                <td data-label="Product">{{ $item->product->name }}</td>
                                <td data-label="Stock">
                                    <strong class="stock-indicator text-success">
                                        {{ $item->product->stock }}
                                    </strong>
                                </td>
                                <td data-label="Price">${{ number_format($item->product->price, 2) }}</td>
                                <td data-label="Quantity">
                                    <input type="number" class="form-control quantity-input" data-id="{{ $item->id }}"
                                        data-price="{{ $item->product->price }}" data-stock="{{ $item->product->stock }}"
                                        value="{{ $item->quantity }}" min="1">
                                </td>
                                <td data-label="Total">
                                    <strong>${{ number_format($totalPrice, 2) }}</strong>
                                </td>
                                <td data-label="Action">
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $item->id }}"
                                        data-name="{{ $item->product->name }}" data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmModal">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td></td>
                            <td colspan="5" class="text-end"><strong id="grandTotalText">Grand Total:</strong></td>
                            <td><strong id="grandTotalAmount">${{ number_format($grandTotal, 2) }}</strong></td>
                            <!-- Cart Page -->
                            <td>
                                <form id="buyNowForm" action="{{ route('cart.buy-now') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="selected_items" id="selectedItemsInput">
                                    <input type="hidden" name="quantities" id="quantitiesInput">
                                    <!-- Add quantities input -->
                                    <button type="submit" id="buyNowBtn" class="btn btn-danger d-none">
                                        <i class="bi bi-cart-check"></i> Buy Now
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </tfoot>

                </table>
            </div>

            <div class="clear-cart-container">
                <button class="btn btn-warning mb-2" data-bs-toggle="modal" data-bs-target="#clearCartConfirmModal">
                    <i class="bi bi-x-circle"></i> Clear Cart
                </button>
            </div>
        @endif
    </div>

    <!-- Clear Cart Confirmation Modal -->
    <div class="modal fade" id="clearCartConfirmModal" tabindex="-1" aria-labelledby="clearCartLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clearCartLabel">Confirm Clear Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to clear your cart? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="{{ route('cart.clear') }}" class="btn btn-danger">Yes, Clear Cart</a>
                </div>
            </div>
        </div>
    </div>


    <!-- 🔴 Include Delete Modal Component -->
    @include('components.modal')

@endsection

@push('scripts')
    <script>
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
    </script>
@endpush
