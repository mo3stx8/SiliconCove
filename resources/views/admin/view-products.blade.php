<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- website icon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
</head>

<body>
    @include('admin.includes.sidebar')

    <!-- Main Content -->
    <section id="content">
        @include('admin.includes.navbar')

        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3">All Products</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">All Products</li>
                </ol>
            </div>

            @if(session('success'))
            <div id="successMessage" class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <!-- Products Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Product List</h3>
                </div>
                <div class="card-body">
                    <x-data-table
                        :headers="[
                            'id' => '#',
                            'image' => 'Image',
                            'name' => 'Product Name',
                            'description' => 'Description',
                            'price' => 'Price',
                            'stock' => 'Stock'
                        ]"
                        :rows="$rows"
                        :actions="$actions"
                        route="{{ route('admin.view-products') }}"
                    />
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editProductForm" method="POST" action="{{ route('products.update', ':id') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editProductId" name="id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editProductName" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="editProductName" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editProductDescription" class="form-label">Description</label>
                                    <input type="text" class="form-control" id="editProductDescription" name="description" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editProductPrice" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="editProductPrice" name="price" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editProductStock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="editProductStock" name="stock" required>
                                </div>
                                <!-- restock level -->
                                <div class="mb-3">
                                    <label for="editProductRestockLevel" class="form-label">Restock Level</label>
                                    <input type="number" class="form-control" id="editProductRestockLevel" name="restock_level" required>
                                </div>
                                <!-- category selections -->
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select" id="editProductCategory" required>
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
                                    <label for="editProductImage" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="editProductImage" name="image">
                                    <img id="editProductImagePreview" src="" alt="Product Image" width="100" class="mt-2">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- JavaScript for Editing Product -->
            <script>
                function editProduct(id, name, description, price, stock, imageUrl, category, restockLevel) {
                    document.getElementById("editProductId").value = id;
                    document.getElementById("editProductName").value = name;
                    document.getElementById("editProductDescription").value = description;
                    document.getElementById("editProductPrice").value = price;
                    document.getElementById("editProductStock").value = stock;
                    document.getElementById("editProductRestockLevel").value = restockLevel;
                    document.getElementById("editProductCategory").value = category;
                    document.getElementById("editProductImagePreview").src = imageUrl;
                }
            </script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.getElementById("editProductForm").addEventListener("submit", function(event) {
                        let productId = document.getElementById("editProductId").value;
                        let formAction = "{{ route('products.update', ':id') }}".replace(":id", productId);
                        this.action = formAction;
                    });
                });
            </script>


        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteProductModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product?
                </div>
                <div class="modal-footer">
                    <form id="deleteProductForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function setDeleteProduct(productId) {
            document.getElementById('deleteProductForm').action = "/admin/all-products/" + productId;
        }


        document.addEventListener("DOMContentLoaded", function() {
            let alertBox = document.getElementById("successMessage");
            if (alertBox) {
                setTimeout(function() {
                    alertBox.style.transition = "opacity 1s ease-out";
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 1000);
                }, 2000);
            }
        });
    </script>

<!-- Include jQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <!-- Initialize DataTable -->
<script>
    $(document).ready(function () {
        $('#usersTable').DataTable({
            "paging": true, // Enable pagination
            "searching": true, // Enable search filter
            "ordering": true, // Enable column sorting
            "info": true, // Show table info
            "lengthMenu": [5, 10, 25, 50], // Define page length options
        });
    });
</script>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>

</html>
