<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- website icon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
</head>

<body>

    @include('admin.includes.sidebar')

    <!-- NAVBAR -->
    <section id="content">
        @include('admin.includes.navbar')

        <div class="container mt-4">
            <!-- Title & Breadcrumb -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3">View Orders</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Manage Orders</li>
                    <li class="breadcrumb-item active">View Orders</li>
                </ol>
            </div>

            @if(session('success'))
            <div id="successMessage" class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div id="errorMessage" class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            @include('components.product-info-modal')
            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Orders</h3>
                </div>
                <div class="card-body">
                    @php
                    $actions = [
                        [
                            'inline' => function ($row) {
                                $rowJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                $orderId = $row['order_id'] ?? null;

                                $viewBtn = '<button class="btn btn-primary btn-sm me-1" onclick="showProductInfoModal(' . $rowJson . ')">
                                    <i class="fa fa-eye"></i> View
                                </button>';

                                $deleteBtn = '<button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteOrderModal"
                                    onclick="setDeleteOrder(' . $orderId . ')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>';

                                return '<div class="btn-group">' . $viewBtn . $deleteBtn . '</div>';
                            },
                        ],
                    ];
                    @endphp

                    <x-data-table
                        :headers="[
                        'id' => '#',
                        'order_no' => 'Order ID',
                        'name' => 'Customer',
                        'created_at' => 'Order Date',
                        'total_amount' => 'Total',
                        'status' => 'Status',
                    ]"

                        :rows="$orders->through(function($order, $index) use ($orders) {
                            return [
                                'id' => $orders->firstItem() + $index,
                                'order_no' => $order->order_no,
                                'order_id' => $order->id,
                                'name' => $order->user->name ?? '(in person)',
                                'created_at' => $order->created_at->format('Y-m-d'),
                                'total_amount' => '$' . number_format($order->total_amount, 2),
                                'status' => view('partials.order-status', ['status' => $order->status])->render(),
                                'payment_method' => ucfirst($order->payment_method),
                                'product' => $order->product,
                                'proof_of_payment' => $order->proof_of_payment,
                                'quantity' => $order->quantity,
                                'address' => $order->user->address ?? '',
                            ];
                        })"

                        :actions="$actions"
                        route="{{ route('admin.view-orders') }}" />
                </div>
            </div>
        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteOrderModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this order? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteOrderForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to Set Form Action -->
    <script>
        function setDeleteOrder(id) {
            const deleteForm = document.getElementById('deleteOrderForm');
            deleteForm.action = "/admin/orders/" + id + "/delete"; // Updated to match the new route
        };

        document.addEventListener("DOMContentLoaded", function() {
            let alertBox = document.getElementById("successMessage");

            if (alertBox) {
                setTimeout(function() {
                    alertBox.style.transition = "opacity 1s ease-out";
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 1000); // Remove from DOM after fade out
                }, 2000); // Show for 2 seconds before fading
            }
        });
    </script>

    <!-- Include jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                "paging": false,
                "searching": false,
                "ordering": true,
                "info": false,
            });
        });
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>

</html>
