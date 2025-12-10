<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Process Refunds</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- website icon -->
    <link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    @include('admin.includes.sidebar')

    <!-- NAVBAR -->
    <section id="content">
        @include('admin.includes.navbar')

        <div class="container mt-4">
            <!-- Title & Breadcrumb -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3">Process Refunds</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Manage Transactions</li>
                    <li class="breadcrumb-item active">Process Refunds</li>
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

            <!-- Refund Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Refunds</h5>
                            <h2 class="mb-0">${{ number_format($totalRefunds, 2) }}</h2>
                            <small>Current month</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h5 class="card-title">Pending Refunds</h5>
                            <h2 class="mb-0">{{ $pendingRefunds }}</h2>
                            <small>Awaiting processing</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Processed Refunds</h5>
                            <h2 class="mb-0">{{ $processedRefunds }}</h2>
                            <small>Current month</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Refund Rate</h5>
                            <h2 class="mb-0">{{ number_format($refundRate, 1) }}%</h2>
                            <small>of total orders</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Refunds Table -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h3 class="card-title">Pending Refund Requests</h3>
                </div>
                <div class="card-body">
                @php
                        $rows = $orders->through(function ($order, $index) use ($orders) {
                            return [
                                'id' => $orders->firstItem() + $index,
                                'order_no' => $order->order_no,
                                'name' => $order->user->name,
                                'created_at' => $order->created_at->format('Y-m-d'),
                                'updated_at' => $order->updated_at->format('Y-m-d'),
                                'total_amount' => '$' . number_format($order->total_amount, 2),
                                'raw_status' => $order->status, // Add this line to include raw status
                                'status' => view('partials.order-status', ['status' => $order->status])->render(),
                                'payment_method' => ucfirst($order->payment_method),
                                'order_id' => $order->id,
                                'reason' => '',
                                'refund_id' => $order->refund_no,
                                'refund_requested_date' => $order->refund_requested_date
                                    ? \Carbon\Carbon::parse($order->refund_requested_date)->format('Y-m-d')
                                    : 'N/A',
                                'product' => $order->product,
                                'refund_reason' => $order->refund_reason,
                            ];
                        });

                        $actions = [
                            [
                                'view' => null,
                                'inline' => function ($row) {
                                    $rowJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                    $viewBtn = '<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewRefundModal" onclick="viewRefund(' . $rowJson . ')">
                                        <i class="fa fa-eye"></i> View
                                    </button>';

                                    $approveDenyBtn = '';
                                    if ($row['raw_status'] === 'refund_requested') {
                                        $approveDenyBtn = '<button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveRefundModal" onclick="approveRefund(' . $row['order_id'] . ')">
                                                <i class="fa fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#denyRefundModal" onclick="denyRefund(' . $row['order_id'] . ')">
                                                <i class="fa fa-times"></i> Deny
                                            </button>';
                                    } else {
                                        $approveDenyBtn = '<button class="btn btn-secondary btn-sm text-muted opacity-50" disabled>
                                                <i class="fa fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-secondary btn-sm text-muted opacity-50" disabled>
                                                <i class="fa fa-times"></i> Deny
                                            </button>';
                                    }

                                    return '<div class="btn-group">' . $viewBtn . $approveDenyBtn . '</div>';
                                }
                            ]
                        ];
                    @endphp

                    <x-data-table
                        :headers="[
                            'refund_id' => 'Refund ID',
                            'order_no' => 'Order ID',
                            'name' => 'Customer',
                            'created_at' => 'Date Requested',
                            'total_amount' => 'Amount',
                        ]"
                        :rows="$rows"
                        :actions="$actions"
                        route="{{ route('admin.process-refunds') }}"
                        search-key="pending_search"
                        entries-key="pending_entries"
                        page-key="pending_page"
                    />
                </div>
            </div>


            <!-- Recent Processed Refunds -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">Recent Processed Refunds</h3>
                </div>
                <div class="card-body">
                    @php
                        $processedRows = $processedOrders->through(function ($order, $index) use ($processedOrders) {
                            return [
                                'id' => $processedOrders->firstItem() + $index,
                                'refund_id' => $order->refund_no,
                                'order_no' => $order->order_no,
                                'name' => $order->user->name,
                                'created_at' => $order->created_at->format('Y-m-d'),
                                'updated_at' => $order->updated_at->format('Y-m-d'),
                                'total_amount' => '$' . number_format($order->total_amount, 2),
                                'raw_status' => $order->status,
                                'status' => view('partials.order-status', ['status' => $order->status])->render(),
                                'payment_method' => ucfirst($order->payment_method),
                                'order_id' => $order->id,
                                'refund_requested_date' => $order->refund_requested_date
                                    ? \Carbon\Carbon::parse($order->refund_requested_date)->format('Y-m-d')
                                    : 'N/A',
                                'product' => $order->product,
                                'refund_reason' => $order->refund_reason,
                            ];
                        });

                        $processedActions = [
                            [
                                'view' => null,
                                'inline' => function ($row) {
                                    $rowJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                    return '<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewRefundModal" onclick="viewRefund(' . $rowJson . ')">
                                        <i class="fa fa-eye"></i> View
                                    </button>';
                                }
                            ]
                        ];
                    @endphp

                    <x-data-table
                        :headers="[
                            'refund_id' => 'Refund ID',
                            'order_no' => 'Order ID',
                            'name' => 'Customer',
                            'created_at' => 'Date Requested',
                            'updated_at' => 'Date Processed',
                            'total_amount' => 'Amount',
                            'status' => 'Status'
                        ]"
                        :rows="$processedRows"
                        :actions="$processedActions"
                        route="{{ route('admin.process-refunds') }}"
                        search-key="processed_search"
                        entries-key="processed_entries"
                        page-key="processed_page"
                    />
                </div>
            </div>

        </div>
    </section>

    <!-- View Refund Modal -->
    <div class="modal fade" id="viewRefundModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Refund Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Order Number:</th>
                                    <td id="orderNumber"></td>
                                </tr>
                                <tr>
                                    <th>Product Name:</th>
                                    <td id="productName"></td>
                                </tr>
                                <tr>
                                    <th>Customer Name:</th>
                                    <td id="customerName"></td>
                                </tr>
                                <tr>
                                    <th>Order Date:</th>
                                    <td id="orderDate"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Refund Status:</th>
                                    <td id="refundStatus"></td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td id="totalAmount"></td>
                                </tr>
                                <tr>
                                    <th>Request Date:</th>
                                    <td id="requestDate"></td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td id="paymentMethod"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="bg-light">Refund Reason:</th>
                                    <td id="refundReason" class="text-wrap"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Refund Modal -->
    <div class="modal fade" id="approveRefundModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="approveRefundForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Approve Refund</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to approve this refund?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="submitRefundAction('approve')">Approve</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Deny Refund Modal -->
    <div class="modal fade" id="denyRefundModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="denyRefundForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Deny Refund</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to deny this refund?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" onclick="submitRefundAction('deny')">Deny</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#pendingRefundsTable').DataTable();
            $('#processedRefundsTable').DataTable();
        });

        function viewRefund(order) {
            console.log(order);
            // Update modal content
            $('#orderNumber').text(order.order_no);
            $('#customerName').text(order.name);
            $('#productName').text(order.product.name);
            $('#orderDate').text(order.created_at);
            $('#paymentMethod').text(order.payment_method.toUpperCase());

            // Format status with badge
            const statusClass = {
                'refund_requested': 'bg-warning',
                'refunded': 'bg-success',
                'refund_rejected': 'bg-danger'
            }[order.raw_status] || 'bg-secondary';

            $('#refundStatus').html(`<span class="badge ${statusClass}">${order.raw_status.replace('_', ' ').toUpperCase()}</span>`);
            $('#totalAmount').text(order.total_amount);
            $('#requestDate').text(order.refund_requested_date);
            $('#refundReason').text(order.refund_reason || 'No reason provided');

            // Show modal
            $('#viewRefundModal').modal('show');
        }

        let currentOrderId = null;

        function approveRefund(orderId) {
            currentOrderId = orderId;
        }

        function denyRefund(orderId) {
            currentOrderId = orderId;
        }

        function submitRefundAction(action) {
            if (!currentOrderId) return;

            const url = action === 'approve'
                ? `/admin/orders/${currentOrderId}/approve-refund`
                : `/admin/orders/${currentOrderId}/deny-refund`;

            $.ajax({
                url: url,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        // Close modal
                        $(`#${action}RefundModal`).modal('hide');

                        // Reload page to reflect changes
                        window.location.reload();
                    } else {
                        alert(response.message || 'Error processing refund');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    alert('Error processing refund');
                }
            });
        }
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
