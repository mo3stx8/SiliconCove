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
    <link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">
</head>

<body>

    @include('admin.includes.sidebar')

    <!-- NAVBAR -->
    <section id="content">
        @include('admin.includes.navbar')

        <div class="container mt-4">
            <!-- Title & Breadcrumb -->
            <div
                class="pending-orders-page-heading d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <h1 class="h3 mb-0">Pending Orders</h1>
                <ol class="breadcrumb mb-0">
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
            <div class="row g-3 mb-4 pending-orders-summary">
                <div class="col-6 col-md-3">
                    <div class="card bg-warning text-dark h-100 pending-orders-summary-card">
                        <div class="card-body">
                            <h5 class="card-title">New Orders</h5>
                            <h2 class="mb-0">{{ $newOrdersCount }}</h2>
                            <small>Awaiting approval</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card bg-primary text-white h-100 pending-orders-summary-card">
                        <div class="card-body">
                            <h5 class="card-title">Approved Orders</h5>
                            <h2 class="mb-0">{{ $aprovedOrdersCount }}</h2>
                            <small>Processing</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card bg-info text-dark h-100 pending-orders-summary-card">
                        <div class="card-body">
                            <h5 class="card-title">Ready to Ship</h5>
                            <h2 class="mb-0">{{ $readyToShipOrdersCount }}</h2>
                            <small>Packaged orders</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card bg-success text-white h-100 pending-orders-summary-card">
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
                                    $orderId = e($row['order_id']);

                                    $viewBtn =
                                        '<button type="button" class="btn btn-primary btn-sm" onclick="showProductInfoModal(' .
                                        htmlspecialchars(json_encode($row)) .
                                        ')">
                                        <i class="fa fa-eye"></i> View
                                    </button>';

                                    $approveBtn = '';
                                    if ($status === 'pending') {
                                        $approveBtn =
                                            '<button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#approveOrderModal" data-order-action="approve" data-order-id="' .
                                            $orderId .
                                            '" onclick="setApproveOrder(this.dataset.orderId)">
                                            <i class="fa fa-check-circle"></i> Approve
                                        </button>';
                                    } else {
                                        $approveBtn = '<button type="button" class="btn btn-secondary btn-sm text-muted opacity-50" disabled>
                                            <i class="fa fa-check-circle"></i> Approve
                                        </button>';
                                    }

                                    $processBtn = '';
                                    if ($status === 'approved') {
                                        $processBtn =
                                            '
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#processOrderModal" data-order-action="process" data-order-id="' .
                                            $orderId .
                                            '" onclick="setProcessOrder(this.dataset.orderId)">
                                            <i class="fa-solid fa-gear"></i> Process
                                        </button>';
                                    } else {
                                        $processBtn = '<button type="button" class="btn btn-secondary btn-sm text-muted opacity-50" disabled>
                                            <i class="fa-solid fa-gear"></i> Process
                                        </button>';
                                    }

                                    $rejectBtn = '';
                                    if ($status === 'pending') {
                                        $rejectBtn =
                                            '
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectOrderModal" data-order-action="reject" data-order-id="' .
                                            $orderId .
                                            '" onclick="setRejectOrder(this.dataset.orderId)">
                                            <i class="fa-solid fa-xmark-circle"></i> Reject
                                        </button>';
                                    } else {
                                        $rejectBtn = '<button type="button" class="btn btn-secondary btn-sm text-muted opacity-50" disabled>
                                            <i class="fa-solid fa-xmark-circle"></i> Reject
                                        </button>';
                                    }

                                    $completeBtn = '';
                                    if ($status === 'in progress') {
                                        $completeBtn =
                                            '<button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#completeOrderModal" data-order-action="complete" data-order-id="' .
                                            $orderId .
                                            '" onclick="setCompleteOrder(this.dataset.orderId)">
                                            <i class="fa fa-check"></i> Complete
                                        </button>';
                                    } else {
                                        $completeBtn = '<button type="button" class="btn btn-secondary btn-sm text-muted opacity-50" disabled>
                                            <i class="fa fa-check"></i> Complete
                                        </button>';
                                    }

                                    return '<div class="order-action-buttons">' .
                                        $viewBtn .
                                        $approveBtn .
                                        $rejectBtn .
                                        $processBtn .
                                        $completeBtn .
                                        '</div>';
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
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Recent Order Activity</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($recentActivities as $activity)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fa {{ $activity->icon }} me-2"></i>
                                    {{ $activity->description }}
                                </div>
                                <span class="text-muted small">
                                    {{ $activity->created_at->diffForHumans() }}
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center">
                                No recent activity
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Approve Order Modal -->
    <div class="modal fade" id="approveOrderModal" tabindex="-1" aria-labelledby="approveOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="approveOrderModalLabel">Approve Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this order? This will move it to the processing stage.</p>
                    <form id="approveOrderForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="submitApproveForm()" class="btn btn-warning">Approve Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Order Modal -->
    <div class="modal fade" id="rejectOrderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectOrderForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <p>Are you sure you want to reject this order?</p>
                        <textarea id="rejectReason" name="reason" class="form-control" placeholder="Optional reason for rejection"></textarea>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitRejectForm()">
                        Reject Order
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Process Order Modal -->
    <div class="modal fade" id="processOrderModal" tabindex="-1" aria-labelledby="processOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-dark">
                    <h5 class="modal-title" id="processOrderModalLabel">Process Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to process this order? This will move it to the in progress stage.</p>
                    <form id="processOrderForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="processOrderBtn" onclick="submitProcessForm()" class="btn btn-info">
                        Process Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Order Modal -->
    <div class="modal fade" id="completeOrderModal" tabindex="-1" aria-labelledby="completeOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="completeOrderModalLabel">Mark as Complete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to mark this order as complete?</p>
                    <form id="completeOrderForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="completeOrderBtn" onclick="submitCompleteForm()"
                        class="btn btn-success">
                        Complete Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to Set Form Action -->
    <script>
        window.csrfToken = "{{ csrf_token() }}";
        window.pendingOrderActionUrls = {
            approve: @json(route('admin.orders.approve', ['id' => '__ORDER_ID__'])),
            reject: @json(route('admin.orders.reject', ['id' => '__ORDER_ID__'])),
            process: @json(route('admin.orders.process', ['id' => '__ORDER_ID__'])),
            complete: @json(route('admin.orders.complete', ['id' => '__ORDER_ID__'])),
        };

        (function () {
            const config = {
                approve: {
                    formId: 'approveOrderForm',
                    modalId: 'approveOrderModal',
                },
                reject: {
                    formId: 'rejectOrderForm',
                    modalId: 'rejectOrderModal',
                },
                process: {
                    formId: 'processOrderForm',
                    modalId: 'processOrderModal',
                },
                complete: {
                    formId: 'completeOrderForm',
                    modalId: 'completeOrderModal',
                },
            };

            function buildOrderActionUrl(action, orderId) {
                return window.pendingOrderActionUrls[action].replace('__ORDER_ID__', encodeURIComponent(orderId));
            }

            function storeOrderAction(action, orderId) {
                if (!config[action] || !orderId) return;

                const form = document.getElementById(config[action].formId);
                const modal = document.getElementById(config[action].modalId);
                const actionUrl = buildOrderActionUrl(action, orderId);

                if (form) {
                    form.action = actionUrl;
                    form.dataset.orderId = orderId;
                }

                if (modal) {
                    modal.dataset.orderId = orderId;
                }
            }

            function submitOrderAction(action) {
                const actionConfig = config[action];
                const form = actionConfig ? document.getElementById(actionConfig.formId) : null;
                const modal = actionConfig ? document.getElementById(actionConfig.modalId) : null;
                const orderId = form?.dataset.orderId || modal?.dataset.orderId;

                if (!form || !orderId) {
                    alert('Please select an order first.');
                    return;
                }

                form.action = buildOrderActionUrl(action, orderId);

                if (!window.fetch) {
                    form.submit();
                    return;
                }

                fetch(form.action, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(Object.fromEntries(new FormData(form))),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            window.location.reload();
                            return;
                        }

                        alert(data.message || 'Unable to update order.');
                    })
                    .catch(() => {
                        alert('Unable to update order.');
                    });
            }

            window.setApproveOrder = function (orderId) {
                storeOrderAction('approve', orderId);
            };
            window.setRejectOrder = function (orderId) {
                storeOrderAction('reject', orderId);
            };
            window.setProcessOrder = function (orderId) {
                storeOrderAction('process', orderId);
            };
            window.setCompleteOrder = function (orderId) {
                storeOrderAction('complete', orderId);
            };

            window.submitApproveForm = function () {
                submitOrderAction('approve');
            };
            window.submitRejectForm = function () {
                submitOrderAction('reject');
            };
            window.submitProcessForm = function () {
                submitOrderAction('process');
            };
            window.submitCompleteForm = function () {
                submitOrderAction('complete');
            };

            document.addEventListener('show.bs.modal', function (event) {
                const action = event.relatedTarget?.dataset?.orderAction;
                const orderId = event.relatedTarget?.dataset?.orderId;
                storeOrderAction(action, orderId);
            });
        })();
    </script>

    <!-- Include jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Pending order js -->
    @vite('resources/js/admin/pending-orders.js')

    <!-- Initialize DataTable -->
    <!-- Uncomment below script if you want to customize DataTable initialization  and to use inline js-->
    {{-- <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                "paging": false,
                "searching": false,
                "ordering": true,
                "info": false,
            });
        });
    </script> --}}
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>

</html>


<!-- icons -->
{{-- <i class="fa-solid fa-plus text-warning"></i>
<i class="fa fa-circle-plus text-warning"></i>
<i class="fa-solid fa-bag-shopping"></i>
<i class="fa-solid fa-receipt"></i>
<i class="fa-solid fa-truck"></i>
<i class="fa-solid fa-truck-fast"></i>
<i class="fa-solid fa-truck-moving"></i>
<i class="fa-solid fa-truck-ramp-box"></i>
<i class="fa-solid fa-box"></i>
<i class="fa-solid fa-dolly"></i> 
<i class="fa-solid fa-square-plus"></i>
<i class="fa-solid fa-gear text-info"></i> --}}
