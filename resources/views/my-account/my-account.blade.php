<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Account</title>

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/account.css') }}" rel="stylesheet">
    <!-- website icon -->
    <link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">
</head>

<body>

    <div class="container-fluid">
        @if (session('error'))
            <div class="alert alert-danger mt-3">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">

            @include('my-account.includes.sidebar')

            <!-- Orders Table -->
            <div class="col-lg-9">
                <div class="main-container">
                    <form method="GET" action="{{ route('account.index') }}" class="d-flex justify-content-end pb-3">
                        <label class="text-muted me-2" for="order-sort">Sort Orders</label>
                        <select name="status" class="form-select w-auto" id="order-sort" onchange="this.form.submit()">
                            <option value=""
                                {{ request('status') === null || request('status') === '' ? 'selected' : '' }}>All
                            </option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="in progress" {{ request('status') === 'in progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>
                                Delivered</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded
                            </option>
                            <option value="refund_requested"
                                {{ request('status') === 'refund_requested' ? 'selected' : '' }}>Refund Requested
                            </option>
                            <option value="refund_rejected"
                                {{ request('status') === 'refund_rejected' ? 'selected' : '' }}>Refund Rejected
                            </option>
                        </select>
                    </form>

                    @include('components.product-info-modal')

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order #</th>
                                    <th>Refund No</th>
                                    <th>Date Purchased</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-start">
                                @foreach ($orders as $order)
                                    <tr>
                                        <td><a href="#" class="text-decoration-none">{{ $order->order_no }}</a>
                                        </td>
                                        <td>
                                            @if ($order->refund_no)
                                                <a href="#"
                                                    class="text-decoration-none">{{ $order->refund_no }}</a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        <td>{{ $order->created_at->format('F j, Y') }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'approved' => 'success',
                                                    'pending' => 'warning text-dark',
                                                    'in progress' => 'info',
                                                    'delivered' => 'primary',
                                                    'rejected' => 'danger',
                                                    'cancelled' => 'danger',
                                                    'refunded' => 'dark',
                                                    'refund_requested' => 'warning',
                                                    'refund_rejected' => 'danger',
                                                ];
                                                $statusLabel = ucwords(str_replace('_', ' ', $order->status));
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <div class="btn-group gap-1">
                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#productInfoModal"
                                                    onclick='showProductInfoModal(@json($order))'>
                                                    <i class="fa fa-eye"></i> View
                                                </button>

                                                @if ($order->status === 'pending')
                                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#cancelOrderModal"
                                                        onclick="setCancelOrder('{{ $order->id }}')">
                                                        <i class="fa-solid fa-ban"></i> Cancel
                                                    </button>
                                                @endif

                                                @php
                                                    $restrictedRefundStatuses = [
                                                        'refund_requested',
                                                        'refunded',
                                                        'refund_rejected',
                                                    ];
                                                    $canRequestRefund =
                                                        $order->status === 'delivered' &&
                                                        !in_array($order->status, $restrictedRefundStatuses);
                                                @endphp

                                                <button
                                                    class="btn btn-sm {{ $canRequestRefund ? 'btn-warning' : 'btn-secondary text-muted' }}"
                                                    {{ $canRequestRefund ? '' : 'disabled' }} data-bs-toggle="modal"
                                                    data-bs-target="#refundRequestModal"
                                                    onclick='showRefundModal(@json($order))'>
                                                    <i class="fa fa-undo"></i> Request Refund
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> <!-- End Main Container -->
            </div> <!-- End Orders Table -->

        </div>
    </div>

    <!-- Refund Request Modal -->
    <div class="modal fade" id="refundRequestModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark sticky-top">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fa fa-undo me-2"></i>
                        Request Refund
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="refundRequestForm" action="{{ route('orders.request-refund') }}" method="POST"
                    class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="order_id" id="refundOrderId">
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="card mb-3 border-secondary">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Order Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="d-flex justify-content-between">
                                            <strong>Order #:</strong>
                                            <span id="refundOrderNo"></span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <strong>Date Purchased:</strong>
                                            <span id="refundOrderDate"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex justify-content-between">
                                            <strong>Total Amount:</strong>
                                            <span id="refundOrderAmount"></span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <strong>Payment Method:</strong>
                                            <span id="refundPaymentMethod"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Refund Reason -->
                        <div class="mb-3">
                            <label for="refundReason" class="form-label fw-bold">Reason for Refund</label>
                            <textarea class="form-control" id="refundReason" name="refund_reason" rows="4" required minlength="10"
                                placeholder="Please provide a detailed explanation for your refund request..."></textarea>
                            <div class="invalid-feedback">
                                Please provide a detailed reason (minimum 10 characters).
                            </div>
                        </div>

                        <div class="alert alert-info shadow-sm">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>Please note:</strong>
                            </div>
                            <ul class="mb-0 ps-4">
                                <li>Refund requests are subject to review and approval</li>
                                <li>Processing may take 3–5 business days</li>
                                <li>You will be notified once your request is processed</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer sticky-bottom">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fa fa-paper-plane me-1"></i>
                            Submit Refund Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-ban me-2"></i>
                        Cancel Order
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this order?</p>

                    <textarea id="cancelReason" class="form-control" rows="3" placeholder="Optional reason for cancellation"></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button class="btn btn-danger" onclick="submitCancelOrder()">
                        Yes, Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showRefundModal(order) {
            console.log(order);
            document.getElementById('refundOrderId').value = order.id;
            document.getElementById('refundOrderNo').textContent = order.order_no;
            document.getElementById('refundOrderDate').textContent = new Date(order.created_at).toLocaleDateString();
            document.getElementById('refundOrderAmount').textContent = '$' + parseFloat(order.total_amount).toLocaleString(
                undefined, {
                    minimumFractionDigits: 2
                });
            document.getElementById('refundPaymentMethod').textContent = order.payment_method.toUpperCase();
        }

        // Handle form validation
        (function() {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })();

        // Handle success flash message
        @if (session('success'))
            alert("{{ session('success') }}");
        @endif
    </script>
    <script>
        let currentCancelOrderId = null;

        function setCancelOrder(orderId) {
            currentCancelOrderId = orderId;
        }

        function submitCancelOrder() {
            if (!currentCancelOrderId) return;

            fetch(`/orders/${currentCancelOrderId}/cancel`, {
                    method: "PUT",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        reason: document.getElementById('cancelReason').value
                    })
                })
                .then(() => window.location.reload());
        }
    </script>


</body>

</html>
