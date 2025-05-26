<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Orders</title>
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
                <h1 class="h3">Pending Orders</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Manage Orders</li>
                    <li class="breadcrumb-item active">Pending Orders</li>
                </ol>
            </div>

            @if (session('success'))
                <div id="successMessage" class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div id="errorMessage" class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Order Status Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h5 class="card-title">New Orders</h5>
                            <h2 class="mb-0">{{ $newOrdersCount }}</h2>
                            <small>Awaiting approval</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Approved Orders</h5>
                            <h2 class="mb-0">{{ $aprovedOrdersCount }}</h2>
                            <small>Processing</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-dark">
                        <div class="card-body">
                            <h5 class="card-title">Ready to Ship</h5>
                            <h2 class="mb-0">{{ $readyToShipOrdersCount }}</h2>
                            <small>Packaged orders</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Shipped</h5>
                            <h2 class="mb-0">{{ $shippedOrdersCount }}</h2>
                            <small>In transit</small>
                        </div>
                    </div>
                </div>
            </div>

            @include('components.product-info-modal')
            <!-- Pending Orders Table -->
            <div class="card pending-orders">
                <div class="card-header bg-warning text-dark">
                    <h3 class="card-title">Pending Orders</h3>
                </div>
                <div class="card-body">
                    @php
                        $rows = $orders->through(function ($order, $index) use ($orders) {
                            return [
                                'id' => $orders->firstItem() + $index,
                                'order_no' => $order->order_no,
                                'name' => $order->user->name,
                                'created_at' => $order->created_at->format('Y-m-d'),
                                'total_amount' => '$' . number_format($order->total_amount, 2),
                                'raw_status' => $order->status, // Add this line to include raw status
                                'status' => view('partials.order-status', ['status' => $order->status])->render(),
                                'waiting_time' => $order->created_at->diffForHumans(),
                                'payment_method' => ucfirst($order->payment_method),
                                'order_id' => $order->id,
                                'product' => $order->product,
                                'proof_of_payment' => $order->proof_of_payment,
                                'quantity' => $order->quantity,
                                'address' => $order->user->address,
                            ];
                        });

                        $actions = [
                            [
                                'view' => null,
                                'inline' => function ($row) {
                                    $orderNo = $row['order_no'];
                                    $status = $row['raw_status'];
                                    $orderId = $row['order_id'];

                                    $viewBtn = '<button class="btn btn-primary btn-sm me-1" onclick="showProductInfoModal(' . htmlspecialchars(json_encode($row)) . ')">
                                        <i class="fa fa-eye"></i> View
                                    </button>';

                                    $approveBtn = '';
                                    if ($status === 'pending') {
                                        $approveBtn = '<button class="btn btn-info btn-sm me-1" data-bs-toggle="modal" data-bs-target="#approveOrderModal" onclick="setApproveOrder(\'' . $orderId . '\')">
                                            <i class="fa fa-check-circle"></i> Approve
                                        </button>';
                                    } else {
                                        $approveBtn = '<button class="btn btn-secondary btn-sm me-1 text-muted opacity-50" disabled>
                                            <i class="fa fa-check-circle"></i> Approve
                                        </button>';
                                    }

                                    $completeBtn = '';
                                    if ($status === 'in progress') {
                                        $completeBtn = '<button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#completeOrderModal" onclick="setCompleteOrder(\'' . $orderId . '\')">
                                            <i class="fa fa-check"></i> Complete
                                        </button>';
                                    } else {
                                        $completeBtn = '<button class="btn btn-secondary btn-sm text-muted opacity-50" disabled>
                                            <i class="fa fa-check"></i> Complete
                                        </button>';
                                    }

                                    return '<div class="btn-group">' . $viewBtn . $approveBtn . $completeBtn . '</div>';
                                },
                            ],
                        ];
                    @endphp

                    <x-data-table :headers="[
                        'id' => '#',
                        'order_no' => 'Order ID',
                        'name' => 'Customer',
                        'created_at' => 'Order Date',
                        'total_amount' => 'Total',
                        'status' => 'Status',
                        'waiting_time' => 'Waiting Time',
                    ]" :rows="$rows" :actions="$actions"
                        route="{{ route('admin.pending-orders') }}" />

                </div>
            </div>

            <!-- Recent Order Activity -->
            <!-- <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Recent Order Activity</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa fa-check-circle text-success me-2"></i>
                                Order #ORD-012 was approved by Admin
                            </div>
                            <span class="text-muted small">10 minutes ago</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa fa-truck text-primary me-2"></i>
                                Order #ORD-008 was marked as shipped
                            </div>
                            <span class="text-muted small">1 hour ago</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa fa-plus-circle text-warning me-2"></i>
                                New order #ORD-015 received
                            </div>
                            <span class="text-muted small">2 hours ago</span>
                        </li>
                    </ul>
                </div>
            </div> -->

        </div>
    </section>

    <!-- Approve Order Modal -->
    <div class="modal fade" id="approveOrderModal" tabindex="-1" aria-labelledby="approveOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="approveOrderModalLabel">Approve Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this order? This will move it to the processing stage.</p>
                    <form id="approveOrderForm">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="submitApproveForm()" class="btn btn-info">Approve Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Order Modal -->
    <div class="modal fade" id="completeOrderModal" tabindex="-1" aria-labelledby="completeOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="completeOrderModalLabel">Mark as Complete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to mark this order as complete?</p>
                    <form id="completeOrderForm">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="completeOrderBtn" onclick="submitCompleteForm()" class="btn btn-success">
                        Complete Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to Set Form Action -->
    <script>
        let currentOrderId = null;

        function setApproveOrder(orderId) {
            currentOrderId = orderId;
        }

        function submitApproveForm() {
            if (!currentOrderId) return;

            const form = document.getElementById('approveOrderForm');
            const formData = new FormData(form);

            fetch(`/admin/orders/${currentOrderId}/approve`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#approveOrderModal').modal('hide');
                    // Add Bootstrap alert
                    const alertHtml = `
                        <div id="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    document.querySelector('.pending-orders').insertAdjacentHTML('afterbegin', alertHtml);

                    // Reload page after delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    const alertHtml = `
                        <div id="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    document.querySelector('.pending-orders').insertAdjacentHTML('afterbegin', alertHtml);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const alertHtml = `
                    <div id="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error approving order
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                document.querySelector('.pending-orders').insertAdjacentHTML('afterbegin', alertHtml);
            });
        }

        let currentCompleteOrderId = null;

        function setCompleteOrder(orderId) {
            currentCompleteOrderId = orderId;
        }

        function submitCompleteForm() {
            if (!currentCompleteOrderId) return;

            const form = document.getElementById('completeOrderForm');
            const submitButton = document.getElementById('completeOrderBtn');
            const formData = new FormData(form);

            // Update button to muted state while processing
            submitButton.disabled = true;
            submitButton.classList.remove('btn-success');
            submitButton.classList.add('btn-secondary', 'text-muted', 'opacity-75');
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            fetch(`/admin/orders/${currentCompleteOrderId}/complete`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#completeOrderModal').modal('hide');
                    // Add success alert
                    const alertHtml = `
                        <div id="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    document.querySelector('.pending-orders').insertAdjacentHTML('afterbegin', alertHtml);

                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Re-enable button if there's an error
                    submitButton.disabled = false;
                    submitButton.classList.remove('btn-secondary', 'text-muted', 'opacity-75');
                    submitButton.classList.add('btn-success');
                    submitButton.innerHTML = 'Complete Order';
                    // Show error alert
                    const alertHtml = `
                        <div id="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    document.querySelector('.pending-orders').insertAdjacentHTML('afterbegin', alertHtml);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Restore button state
                submitButton.disabled = false;
                submitButton.classList.remove('btn-secondary', 'text-muted', 'opacity-75');
                submitButton.classList.add('btn-success');
                submitButton.innerHTML = 'Complete Order';
            });
        }

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
