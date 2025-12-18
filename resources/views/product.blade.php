<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
    integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/components/contacts/contact-1/assets/css/contact-1.css">


<!-- website icon -->
    <link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">

<title>SiliconCove - Products</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="{{ asset('css/styles.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('includes.navbar');


<div class="container">
    <div class="row">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Product</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container mt-3">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<div class="container py-5">
    <h2 class="text-center mb-4">Our Products</h2>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach ($products as $product)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                        alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ $product->description }}</p>
                        <p class="text-muted">Stock: <strong>{{ $product->stock }}</strong></p> <!-- Display stock -->

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">${{ number_format($product->price, 2) }}</span>

                            <button class="btn btn-outline-primary add-to-cart-btn" data-bs-toggle="modal"
                                data-bs-target="#addToCartModal" data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}" data-price="{{ $product->price }}"
                                data-stock="{{ $product->stock }}"
                                data-image="{{ asset('storage/' . $product->image) }}">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Add to Cart Modal -->
<div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToCartModalLabel">Add to Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img id="modalProductImage" src="" class="img-fluid mb-3" style="max-height: 200px;">
                </div>
                <h5 id="modalProductName"></h5>
                <p id="modalProductPrice" class="text-muted"></p>
                <p id="modalProductStock" class="text-danger"></p> <!-- Stock Info -->

                <form id="addToCartForm" method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" id="modalProductId">

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="modalQuantity" class="form-control"
                            value="1" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- JavaScript to Handle Modal Data -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("addToCartModal");
        const productImage = document.getElementById("modalProductImage");
        const productName = document.getElementById("modalProductName");
        const productPrice = document.getElementById("modalProductPrice");
        const productStock = document.getElementById("modalProductStock");
        const productId = document.getElementById("modalProductId");
        const quantityInput = document.getElementById("modalQuantity");
        const addToCartButton = document.querySelector("#addToCartForm button[type='submit']");

        document.querySelectorAll(".add-to-cart-btn").forEach(button => {
            button.addEventListener("click", function() {
                const stock = parseInt(this.getAttribute("data-stock"));

                productImage.src = this.getAttribute("data-image");
                productName.textContent = this.getAttribute("data-name");
                productPrice.textContent = "Price: $" + parseFloat(this.getAttribute(
                    "data-price")).toFixed(2);
                productStock.textContent = "Stock: " + stock;
                productId.value = this.getAttribute("data-id");

                // Reset quantity to 1 on open
                quantityInput.value = 1;

                // Handle stock logic
                if (stock === 0) {
                    quantityInput.disabled = true;
                    addToCartButton.disabled = true;
                    quantityInput.value = 0;
                } else {
                    quantityInput.disabled = false;
                    addToCartButton.disabled = false;

                    quantityInput.addEventListener("input", function() {
                        const enteredQty = parseInt(this.value) || 0;

                        if (enteredQty > stock || enteredQty <= 0) {
                            addToCartButton.disabled = true;
                            this.classList.add("is-invalid");
                        } else {
                            addToCartButton.disabled = false;
                            this.classList.remove("is-invalid");
                        }
                    });
                }
            });
        });
    });
</script>

<script>
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
</script>

@include('includes.footer');

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
</script>
