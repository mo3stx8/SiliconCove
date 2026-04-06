@extends('layouts.layout')

@section('title', 'SiliconCove - Products')
@section('content_container_class', 'product-page pt-4 px-0')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
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

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach ($products as $product)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                            alt="{{ $product->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            {{-- <p class="card-text">{{ $product->description }}</p> --}}
                            <p class="text-muted">Stock: <strong>{{ $product->stock }}</strong></p>

                            <div class=" justify-content-between align-items-center">
                                <span class="h5 mb-0">${{ number_format($product->price, 2) }}</span><br>

                                <button class="btn btn-outline-primary add-to-cart-btn" data-bs-toggle="modal"
                                    data-bs-target="#addToCartModal" data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}" data-price="{{ $product->price }}"
                                    data-stock="{{ $product->stock }}"
                                    data-description="{{ $product->description }}"
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

    <div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addToCartModalLabel">Add to Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img id="modalProductImage" src="" class="img-fluid mb-3" style="max-height: 200px;"
                            alt="Product Img">
                    </div>
                    <h5 id="modalProductName"></h5>
                    <p id="modalProductPrice" class="text-muted"></p>
                    <p id="modalProductDescription" class="text-muted"></p>
                    <p id="modalProductStock" class="text-muted"></p>

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

    <div class="mt-5 d-flex justify-content-center" style="margin-bottom: 15px">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const productImage = document.getElementById("modalProductImage");
            const productName = document.getElementById("modalProductName");
            const productDescription = document.getElementById("modalProductDescription");
            const productPrice = document.getElementById("modalProductPrice");
            const productStock = document.getElementById("modalProductStock");
            const productId = document.getElementById("modalProductId");
            const quantityInput = document.getElementById("modalQuantity");
            const addToCartButton = document.querySelector("#addToCartForm button[type='submit']");

            document.querySelectorAll(".add-to-cart-btn").forEach(button => {
                button.addEventListener("click", function() {
                    const stock = parseInt(this.getAttribute("data-stock"), 10);

                    productImage.src = this.getAttribute("data-image");
                    productName.textContent = this.getAttribute("data-name");
                    productPrice.textContent = "Price: $" + parseFloat(this.getAttribute("data-price"))
                        .toFixed(2);
                    productDescription.textContent = this.dataset.description || '';
                    productStock.textContent = "Stock: " + stock;
                    productId.value = this.getAttribute("data-id");

                    quantityInput.value = stock === 0 ? 0 : 1;
                    quantityInput.disabled = stock === 0;
                    addToCartButton.disabled = stock === 0;
                    quantityInput.classList.remove("is-invalid");
                    quantityInput.max = stock > 0 ? stock : "";

                    quantityInput.oninput = function() {
                        const enteredQty = parseInt(this.value, 10) || 0;

                        if (enteredQty > stock || enteredQty <= 0) {
                            addToCartButton.disabled = true;
                            this.classList.add("is-invalid");
                        } else {
                            addToCartButton.disabled = false;
                            this.classList.remove("is-invalid");
                        }
                    };
                });
            });
        });
    </script>
@endsection
