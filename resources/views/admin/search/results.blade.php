<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silicon Cove - Search</title>
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
                <h1 class="h3">Search Result</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
            <!-- Search Results -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Results for "{{ $query }}"</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @if($users->isEmpty() && $products->isEmpty() && $orders->isEmpty())
                        <li class="list-group-item">No results found.</li>
                    @endif
                    @foreach($users as $user)
                        <li class="list-group-item">
                            <strong>User:</strong> {{ $user->name }} ({{ $user->email }})
                        </li>
                    @endforeach
                    @foreach($products as $product)
                        <li class="list-group-item">
                            <strong>Product:</strong> {{ $product->name }} - ${{ $product->price }}
                        </li>
                    @endforeach
                    @foreach($orders as $order)
                        <li class="list-group-item">
                            <strong>Order #{{ $order->id }}:</strong> User ID {{ $order->user_id }} - Total: ${{ $order->total_amount }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    <!-- END NAVBAR -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>

</html>
