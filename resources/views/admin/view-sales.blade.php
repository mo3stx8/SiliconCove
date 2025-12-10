<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sales</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Include DateRangePicker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3">View Sales</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Manage Transactions</li>
                    <li class="breadcrumb-item active">View Sales</li>
                </ol>
            </div>

            @if(session('success'))
            <div id="successMessage" class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <!-- Sales Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Sales</h5>
                            <h2 class="mb-0">${{ number_format($currentMonthSales, 2) }}</h2>
                            <small>Current month</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Orders</h5>
                            <h2 class="mb-0">{{ $monthlyOrderCount }}</h2>
                            <small>Current month</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Average Order</h5>
                            <h2 class="mb-0">${{ number_format($averageOrderValue, 2) }}</h2>
                            <small>Current month</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h5 class="card-title">Revenue Growth</h5>
                            <h2 class="mb-0">{{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}%</h2>
                            <small>vs last month</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Filter -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Filter Sales</h3>
                </div>
                <div class="card-body">
                    <form id="salesFilterForm" class="row g-3" method="GET">
                        <div class="col-md-4">
                            <label for="dateRange" class="form-label">Date Range</label>
                            <input type="text" class="form-control" id="dateRange" name="dateRange">
                        </div>
                        <div class="col-md-3">
                            <label for="paymentMethod" class="form-label">Payment Method</label>
                            <select class="form-select" id="paymentMethod" name="paymentMethod">
                                <option value="">All Methods</option>
                                <option value="cod" {{ request('paymentMethod') == 'cod' ? 'selected' : '' }}>COD (CASH ON DELIVERY)</option>
                                <option value="gcash" {{ request('paymentMethod') == 'gcash' ? 'selected' : '' }}>GCASH (QR CODE)</option>
                            </select>
                        </div>
                        {{-- <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div> --}}
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Sales Trend</h3>
                </div>
                <div class="card-body">
                    <div id="salesChart" style="height: 300px;"></div>
                </div>
            </div>

            <!-- Payment Method Distribution -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Payment Method Distribution</h3>
                        </div>
                        <div class="card-body">
                            <div id="paymentMethodChart" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Customers</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <br>

        </div>
    </section>

    <!-- Include jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Include DateRangePicker Dependencies -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- ApexCharts JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Initialize DataTable & DateRangePicker -->
    <script>
        $(document).ready(function() {
            // Initialize DateRangePicker with saved values or defaults
            const startDate = '{!! $startDate->format("m/d/Y") !!}';
            const endDate = '{!! $endDate->format("m/d/Y") !!}';

            $('#dateRange').daterangepicker({
                startDate: startDate,
                endDate: endDate,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            // Auto-submit form when date range changes
            $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
                $('#salesFilterForm').submit();
            });

            // Handle success message fade out
            let alertBox = document.getElementById("successMessage");
            if (alertBox) {
                setTimeout(function() {
                    alertBox.style.transition = "opacity 1s ease-out";
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 1000);
                }, 2000);
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Sales Chart Data
            var monthlySales = @json($monthlySales);
            var salesChartOptions = {
                series: [{
                    name: 'Sales',
                    data: Object.values(monthlySales)
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: Object.keys(monthlySales)
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "$" + val.toLocaleString();
                        }
                    }
                },
                colors: ['#0d6efd'],
                noData: {
                    text: 'No sales data available',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: {
                        fontSize: '16px'
                    }
                }
            };

            var salesChart = new ApexCharts(document.querySelector("#salesChart"), salesChartOptions);
            salesChart.render();

            // Payment Method Chart
            var paymentData = @json($paymentDistribution);
            var paymentMethodOptions = {
                series: Object.values(paymentData),
                labels: Object.keys(paymentData).map(method =>
                    method === 'cod' ? 'Cash on Delivery (COD) - ' + (paymentData[method] === 0 ? '0%' : ((paymentData[method] / Object.values(paymentData).reduce((a, b) => a + b, 0)) * 100).toFixed(1) + '%') :
                    'GCASH (QR) - ' + (paymentData[method] === 0 ? '0%' : ((paymentData[method] / Object.values(paymentData).reduce((a, b) => a + b, 0)) * 100).toFixed(1) + '%')
                ),
                chart: {
                    type: 'pie',
                    width: '100%'
                },
                colors: ['#0d6efd', '#198754'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var paymentMethodChart = new ApexCharts(document.querySelector("#paymentMethodChart"), paymentMethodOptions);
            paymentMethodChart.render();

            // Update top customers list
            const topCustomers = @json($topCustomers);
            const topCustomersHtml = topCustomers.length > 0 ?
                topCustomers.map(customer => `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${customer.user.name}</strong>
                            <div class="text-muted small">${customer.purchase_count} purchases</div>
                        </div>
                        <span class="badge bg-primary rounded-pill">$${Number(customer.total_spent).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    </li>
                `).join('') :
                `<li class="list-group-item text-center">No customer data available</li>`;

            document.querySelector('.list-group').innerHTML = topCustomersHtml;
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>

</html>
