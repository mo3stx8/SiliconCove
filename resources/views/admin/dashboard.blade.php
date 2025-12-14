<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silicon Cove - Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Chart.js for Data Visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h1 class="h3">Dashboard</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>

            <!-- Info Cards -->
            <div class="row">
                <!-- Total Users -->
                <div class="col-6 col-md-2 mb-3" style="cursor: pointer;"
                    onclick="window.location='{{ route('admin.all-users') }}'">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body">
                            <h2 class="card-title">{{ $totalUsers }}</h2>
                            <p class="card-text">Total Users</p>
                            <i class="fa fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Products -->
                <div class="col-6 col-md-2 mb-3" style="cursor: pointer;"
                    onclick="window.location='{{ route('admin.view-products') }}'">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body">
                            <h2 class="card-title">{{ $totalProducts }}</h2>
                            <p class="card-text">Total Products</p>
                            <i class="fa fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Sales -->
                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body">
                            <h2 class="card-title">${{ number_format($totalSales, 2) }}</h2>
                            <p class="card-text">Total Sales</p>
                            <i class="fa fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>

                <!-- Order Request -->
                <div class="col-6 col-md-2 mb-3">
                    <div class="card text-white bg-warning h-100" style="cursor:pointer;"
                        onclick="window.location='{{ route('admin.pending-orders') }}'">
                        <div class="card-body">
                            <h2 class="card-title">{{ $pendingOrders }}</h2>
                            <p class="card-text">Pending Orders</p>
                            <i class="fa fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="col-6 col-md-2 mb-3">
                    <div class="card text-white bg-danger h-100" style="cursor:pointer;"
                        onclick="scrollToDailySalesDetail()">
                        <div class="card-body">
                            <h2 class="card-title">{{ $lowStockProducts->count() }}</h2>
                            <p class="card-text">Low Stock Items</p>
                            <i class="fa fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mt-4">
                <!-- Sales Charts -->
                <div class="col-md-8">
                    <div class="card h-100">
                        <div
                            class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <div>
                                <h5 class="card-title mb-3">Sales Report</h5>
                                <div class="btn-group btn-group-sm mt-2 mt-md-0">
                                    <button class="btn btn-outline-primary"
                                        onclick="updateChart('daily')">Daily</button>
                                    <button class="btn btn-outline-primary"
                                        onclick="updateChart('monthly')">Monthly</button>
                                    <button class="btn btn-outline-primary"
                                        onclick="updateChart('yearly')">Yearly</button>
                                    <button class="btn btn-outline-secondary"
                                        onclick="showComparisonModal()">Compare</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Profit Chart + View Analytics Button -->
                <div class="col-md-4 d-flex flex-column">
                    <div class="card flex-grow-1 mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Profit Overview</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="profitChart"></canvas>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mb-3">
                        <a href="{{ route('admin.analytics') }}" class="btn btn-outline-primary btn-lg w-80">
                            <i class="fa fa-chart-line me-2"></i>
                            View Sales Analytics
                        </a>
                    </div>
                </div>
            </div>

            <!-- Daily Sales Detail Table -->
            <div class="row mt-4" id="dailySalesDetailRow">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Daily Sales Detail
                                ({{ \Carbon\Carbon::today()->format('Y-m-d') }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Order No</th>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                            <th>Payment</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($dailyOrders as $order)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</td>
                                                <td>{{ $order->order_no }}</td>
                                                <td>{{ $order->product ? $order->product->name : 'N/A' }}</td>
                                                <td>{{ $order->quantity }}</td>
                                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                                <td>{{ ucfirst($order->payment_method) }}</td>
                                                <td>{{ ucfirst($order->status) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No sales for today.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparison Modal -->
            <div class="modal fade" id="comparisonModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Sales Comparison</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs mb-3" id="comparisonTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="compare-daily-tab" data-bs-toggle="tab"
                                        data-bs-target="#compare-daily" type="button" role="tab">Daily</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="compare-monthly-tab" data-bs-toggle="tab"
                                        data-bs-target="#compare-monthly" type="button"
                                        role="tab">Monthly</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="compare-yearly-tab" data-bs-toggle="tab"
                                        data-bs-target="#compare-yearly" type="button"
                                        role="tab">Yearly</button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="compare-daily" role="tabpanel">
                                    <canvas id="compareDailyChart"></canvas>
                                </div>
                                <div class="tab-pane fade" id="compare-monthly" role="tabpanel">
                                    <canvas id="compareMonthlyChart"></canvas>
                                </div>
                                <div class="tab-pane fade" id="compare-yearly" role="tabpanel">
                                    <canvas id="compareYearlyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables Row -->
            <div class="row mt-4">
                <!-- Low Stock Table -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Low Stock Items</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="table-responsive flex-grow-1">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Current Stock</th>
                                            <th>Restock Level</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lowStockProducts as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->stock }}</td>
                                                <td>{{ $product->restock_level }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary"
                                                        onclick="showRestockModal({{ $product->id }})">
                                                        Restock
                                                    </button>
                                                </td>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Transactions</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="table-responsive flex-grow-1">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentTransactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->order_no }}</td>
                                                <td>{{ $transaction->product->name }}</td>
                                                <td>${{ number_format($transaction->product->price, 2) }}</td>
                                                <td>${{ number_format($transaction->total_amount) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock History Table -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Stock History</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Product</th>
                                            <th>Type</th>
                                            <th>Qty Before</th>
                                            <th>Qty After</th>
                                            <th>Price Before</th>
                                            <th>Price After</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stockHistory as $history)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($history->created_at)->format('Y-m-d H:i') }}
                                                </td>
                                                <td>{{ $history->product ? $history->product->name : 'N/A' }}</td>
                                                <td>{{ ucfirst($history->type) }}</td>
                                                <td>{{ $history->quantity_before }}</td>
                                                <td>{{ $history->quantity_after }}</td>
                                                <td>${{ number_format($history->purchase_price_before, 2) }}</td>
                                                <td>${{ number_format($history->purchase_price_after, 2) }}</td>
                                                <td>{{ $history->notes }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if ($stockHistory->isEmpty())
                                    <div class="text-center text-muted">No stock history records found.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- MAIN -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/admin.js') }}"></script>

    <!-- Add this right before the closing body tag -->
    <!-- Restock Modal -->
    <div class="modal fade" id="restockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Restock Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="restockForm" action="{{ route('admin.restock-product') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="product_id" id="restockProductId">
                        <div class="mb-3">
                            <label class="form-label">Quantity to Add</label>
                            <input type="number" name="quantity" class="form-control" required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Purchase Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input placeholder="leave empty same price before ..." type="number"
                                    name="purchase_price" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm Restock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    {{-- @vite(['resources/js/admin/dashboard.js']) --}}
    <script>
        // Pass PHP data to JS
        window.salesData = {
            daily: @json($dailySales),
            monthly: @json($monthlySales),
            yearly: @json($yearlySales)
        };
        window.profitData = {
            daily: @json($dailyProfit),
            monthly: @json($monthlyProfit),
            yearly: []
        };

        // Initialize Charts
        const salesChart = new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Sales',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        const profitChart = new Chart(document.getElementById('profitChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Profit',
                    data: [],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Function to update charts
        function updateCharts(period) {
            const salesData = window.salesData[period];
            const profitData = window.profitData[period];

            // Update sales chart
            salesChart.data.labels = salesData.map(item => {
                if (period === 'daily') return item.hour;
                if (period === 'monthly') return getMonthName(item.month);
                return item.year;
            });
            salesChart.data.datasets[0].data = salesData.map(item => item.total);
            salesChart.update();

            // Update profit chart
            profitChart.data.labels = profitData.map(item => {
                if (period === 'daily') return item.hour;
                if (period === 'monthly') return getMonthName(item.month);
                return item.year;
            });
            profitChart.data.datasets[0].data = profitData.map(item => item.profit);
            profitChart.update();
        }

        // Add this alias so inline onclick="updateChart('daily')" works
        function updateChart(period) {
            updateCharts(period);
        }

        // Initialize with daily data
        updateCharts('daily');

        // Period buttons click handlers
        document.querySelectorAll('[onclick^="updateChart"]').forEach(button => {
            button.addEventListener('click', function() {
                const period = this.getAttribute('onclick').match(/'(.+)'/)[1];
                updateCharts(period);
            });
        });

        // Restock modal functionality
        window.showRestockModal = function(productId) {
            document.getElementById('restockProductId').value = productId;
            new bootstrap.Modal(document.getElementById('restockModal')).show();
        };



        function getMonthName(monthNumber) {
            return new Date(2000, monthNumber - 1).toLocaleString('default', {
                month: 'long'
            });
        }

        // Comparison Modal logic
        let compareDailyChart, compareMonthlyChart, compareYearlyChart;

        function showComparisonModal() {
            // Prepare comparison data (for demo, compare today vs yesterday, this month vs last month, this year vs last year)
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(today.getDate() - 1);

            const thisMonth = today.getMonth() + 1;
            const lastMonth = thisMonth === 1 ? 12 : thisMonth - 1;
            const thisYear = today.getFullYear();
            const lastYear = thisMonth === 1 ? thisYear - 1 : thisYear;

            // Daily comparison
            const dailyLabels = ['Yesterday', 'Today'];
            const dailyTotals = [
                @php
                    $yesterday = \Carbon\Carbon::yesterday()->toDateString();
                    $yesterdayTotal = \App\Models\Order::where('status', 'delivered')->whereDate('created_at', $yesterday)->sum('total_amount');
                    echo $yesterdayTotal;
                @endphp,
                @php
                    $today = \Carbon\Carbon::today()->toDateString();
                    $todayTotal = \App\Models\Order::where('status', 'delivered')->whereDate('created_at', $today)->sum('total_amount');
                    echo $todayTotal;
                @endphp
            ];

            // Monthly comparison
            const monthlyLabels = ['Last Month', 'This Month'];
            const monthlyTotals = [
                @php
                    $lastMonth = \Carbon\Carbon::now()->subMonth()->month;
                    $lastMonthYear = \Carbon\Carbon::now()->subMonth()->year;
                    $lastMonthTotal = \App\Models\Order::where('status', 'delivered')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $lastMonthYear)->sum('total_amount');
                    echo $lastMonthTotal;
                @endphp,
                @php
                    $thisMonth = \Carbon\Carbon::now()->month;
                    $thisYear = \Carbon\Carbon::now()->year;
                    $thisMonthTotal = \App\Models\Order::where('status', 'delivered')->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->sum('total_amount');
                    echo $thisMonthTotal;
                @endphp
            ];

            // Yearly comparison
            const yearlyLabels = ['Last Year', 'This Year'];
            const yearlyTotals = [
                @php
                    $lastYear = \Carbon\Carbon::now()->subYear()->year;
                    $lastYearTotal = \App\Models\Order::where('status', 'delivered')->whereYear('created_at', $lastYear)->sum('total_amount');
                    echo $lastYearTotal;
                @endphp,
                @php
                    $thisYear = \Carbon\Carbon::now()->year;
                    $thisYearTotal = \App\Models\Order::where('status', 'delivered')->whereYear('created_at', $thisYear)->sum('total_amount');
                    echo $thisYearTotal;
                @endphp
            ];

            // Destroy previous charts if exist
            if (compareDailyChart) compareDailyChart.destroy();
            if (compareMonthlyChart) compareMonthlyChart.destroy();
            if (compareYearlyChart) compareYearlyChart.destroy();

            // Render comparison charts
            compareDailyChart = new Chart(document.getElementById('compareDailyChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'Sales',
                        data: dailyTotals,
                        backgroundColor: ['#6c757d', '#0d6efd']
                    }]
                }
            });

            compareMonthlyChart = new Chart(document.getElementById('compareMonthlyChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Sales',
                        data: monthlyTotals,
                        backgroundColor: ['#6c757d', '#0d6efd']
                    }]
                }
            });

            compareYearlyChart = new Chart(document.getElementById('compareYearlyChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: yearlyLabels,
                    datasets: [{
                        label: 'Sales',
                        data: yearlyTotals,
                        backgroundColor: ['#6c757d', '#0d6efd']
                    }]
                }
            });

            // Show modal
            new bootstrap.Modal(document.getElementById('comparisonModal')).show();
        }

        function scrollToDailySalesDetail() {
            const detailRow = document.getElementById('dailySalesDetailRow');
            if (detailRow) {
                detailRow.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    </script>
</body>

</html>
