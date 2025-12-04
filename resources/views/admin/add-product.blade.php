<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Products</title>
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
                <h1 class="h3">Add Product</h1>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Add Product</li>
                </ol>

            </div>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.add-product.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <!-- restock level -->
                        <div class="mb-3">
                            <label class="form-label">Restock Level</label>
                            <input type="number" name="restock_level" class="form-control" required>
                        </div>

                        <!-- category selections -->
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="" disabled selected>Select Category</option>
                                    <option value="Processors">Processors</option>
                                    <option value="Motherboards">Motherboards</option>
                                    <option value="Graphics Cards">Graphics Cards</option>
                                    <option value="Memory & Storage">Memory & Storage</option>
                                    <option value="Power & Cooling">Power & Cooling</option>
                                    <option value="Peripherals & Accessories">Peripherals & Accessories</option>
                                    <option value="Cases & Builds">Cases & Builds</option>
                                    <option value="Mod Zone">Mod Zone</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="image" class="form-control" id="imageInput">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image Preview</label>
                            <img id="imagePreview" src="#" alt="Image Preview" class="img-fluid" style="max-height: 200px; display: none;">
                        </div>

                        <script>
                            document.getElementById('imageInput').addEventListener('change', function(event) {
                                const [file] = event.target.files;
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        document.getElementById('imagePreview').src = e.target.result;
                                        document.getElementById('imagePreview').style.display = 'block';
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });
                        </script>

                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>

</html>
