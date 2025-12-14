<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Orders</title>
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
                <h1 class="h3">Completed Orders</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Manage Orders</li>
                    <li class="breadcrumb-item active">Completed Orders</li>
                </ol>
            </div>

            @if (session('success'))
                <div id="successMessage" class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @include('components.product-info-modal')

            <!-- Completed Orders Table -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">Completed Orders</h3>
                </div>
                <div class="card-body">
                    @php
                        $rows = $orders->through(function ($order, $index) use ($orders) {
                            return [
                                'id' => $orders->firstItem() + $index,
                                'order_no' => $order->order_no,
                                'name' => $order->user->name ?? '(Admin)',
                                'created_at' => $order->created_at->format('Y-m-d'),
                                'completed_at' => $order->updated_at->format('Y-m-d'),
                                'total_amount' => '$' . number_format($order->total_amount, 2),
                                'payment_method' => strtoupper($order->payment_method),
                                'product' => $order->product, // Pass product data
                                'proof_of_payment' => $order->proof_of_payment,
                                'quantity' => $order->quantity,
                                'address' => $order->user->address ?? '',
                            ];
                        });

                        $actions = [
                            [
                                'inline' => function ($row) {
                                    $viewProductBtn =
                                        '<button class="btn btn-primary btn-sm me-1" onclick="showProductInfoModal(' .
                                        htmlspecialchars(json_encode($row)) .
                                        ')">
                                                        <i class="fa fa-eye"></i> View
                                                    </button>';

                                    $invoiceBtn =
                                        '<a href="' .
                                        route('admin.orders.invoice', $row['order_no']) .
                                        '" class="btn btn-secondary btn-sm invoice-btn" onclick="handleInvoiceClick(event, this)">
                                                        <i class="fa fa-file-pdf invoice-icon"></i>
                                                        <span class="invoice-text">Invoice</span>
                                                        <span class="loading-spinner d-none">
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        </span>
                                                    </a>';

                                    return '<div class="btn-group">' . $viewProductBtn . $invoiceBtn . '</div>';
                                },
                            ],
                        ];
                    @endphp

                    <x-data-table :headers="[
                        'id' => '#',
                        'order_no' => 'Order ID',
                        'name' => 'Customer',
                        'created_at' => 'Order Date',
                        'completed_at' => 'Completion Date',
                        'total_amount' => 'Total',
                        'payment_method' => 'Payment Method',
                    ]" :rows="$rows" :actions="$actions"
                        route="{{ route('admin.completed-orders') }}" />
                </div>
            </div>

            <!-- Monthly Orders Chart -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Completed Orders - Monthly Statistics</h3>
                </div>
                <div class="card-body">
                    <div id="completedOrdersChart" style="height: 300px;"></div>
                </div>
            </div>

        </div>
    </section>

    <!-- Include jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Initialize DataTable -->
    @vite('resources/js/admin/completed-orders.js')

    <!-- ApexCharts JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Chart Initialization -->
    {{-- @vite('resources/js/admin/completed-orders-chart.js') --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const monthlyData = <?php echo json_encode($monthlyCompletedOrders ?? []); ?>;
            const allMonths = [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ];

            const year = Object.keys(monthlyData)[0]?.split(" ")[1] || new Date().getFullYear();
            const chartData = allMonths.map(month => monthlyData[`${month} ${year}`] ?? 0);
            const options = {
                series: [{
                    name: 'Completed Orders',
                    data: chartData // Use the prepared data for the chart
                }],
                chart: {
                    height: 300,
                    type: 'line',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: allMonths, // Use all months for the x-axis
                },
                colors: ['#198754'] // Match success green color
            };

            const chart = new ApexCharts(document.querySelector("#completedOrdersChart"), options);
            chart.render();
        });
    </script>

    <!-- Add this script before the closing body tag -->
    @vite(['resources/js/admin/completed-orders-invoice.js'])

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>

</html>
