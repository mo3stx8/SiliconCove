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

            @if (session('success'))
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
                            <h2 class="mb-0">
                                {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}%</h2>
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
                                <option value="cod" {{ request('paymentMethod') == 'cod' ? 'selected' : '' }}>COD
                                    (CASH ON DELIVERY)</option>
                                <option value="Kuraimi Bank USD"
                                    {{ request('paymentMethod') == 'Kuraimi Bank USD' ? 'selected' : '' }}>Kuraimi Bank
                                    USD
                                </option>
                                <option value="Kuraimi Bank SR"
                                    {{ request('paymentMethod') == 'Kuraimi Bank SR' ? 'selected' : '' }}>Kuraimi Bank
                                    SR
                                </option>
                                <option value="cash" {{ request('paymentMethod') == 'cash' ? 'selected' : '' }}>CASH
                                </option>
                                {{-- <option value="gcash" {{ request('paymentMethod') == 'gcash' ? 'selected' : '' }}>GCASH --}}
                                </option>
                            </select>
                        </div>
                        {{-- <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
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

            <!-- Payment Method Distribution (Full Width) -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Payment Method Distribution</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div id="paymentMethodChart" style="max-width: 500px; width: 100%;"></div>
                    </div>
                </div>
            </div>

            <!-- Top Customers and Products Row -->
            <div class="row g-4">
                <!-- Top Customers -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-users me-2"></i>Top Customers
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @forelse($topCustomers as $customer)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $customer->user->name ?? '' }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-shopping-bag me-1"></i>
                                                {{ $customer->purchase_count }} orders
                                            </small>
                                        </div>
                                        <div>
                                            <span class="badge bg-primary rounded-pill me-2">
                                                ${{ number_format($customer->total_spent, 2) }}
                                            </span>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#viewCustomerModal{{ $customer->user->id ?? '' }}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @include('components.customer-modal', ['customer' => $customer])
                                @empty
                                    <div class="text-center text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        No customer data available
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h3 class="card-title">
                                <i class="fas fa-box me-2"></i>Top Products
                            </h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @forelse($topProducts as $product)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $product->product?->name }}</strong>
                                            <div class="text-muted small">
                                                Sold: {{ $product->total_quantity }} units
                                                <span class="mx-1">•</span>
                                                Orders: {{ $product->order_count }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-success rounded-pill me-2">
                                                ${{ number_format($product->total_revenue, 2) }}
                                            </span>
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#viewProductModal{{ $product->product_id }}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </li>
                                    @include('components.product-modal', ['product' => $product])
                                @empty
                                    <li class="list-group-item text-center">No product data available</li>
                                @endforelse
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
    <!-- tiny inline config (keeps Blade values available to module) -->
    <script>
        window.salesConfig = {
            startDate: {!! json_encode($startDate->format('m/d/Y')) !!},
            endDate: {!! json_encode($endDate->format('m/d/Y')) !!},
            monthlySales: {!! json_encode($monthlySales) !!},
            paymentDistribution: {!! json_encode($paymentDistribution) !!}
        };
    </script>

    <!-- Vite-managed module (moved inline JS lives here) -->
    @vite('resources/js/admin/analytics.js')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>

</html>
