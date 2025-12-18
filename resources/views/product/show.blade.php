@extends('layouts.layout')
<!-- website icon -->
<link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">

@section('title')
    SiliconCove
@endsection

@section('content')
    <div class="container py-5"> <!-- Added padding top and bottom -->
        <div class="row align-items-center">
            <!-- Product Image -->
            <div class="col-md-6 d-flex justify-content-center">
                <div class="p-3 w-100">
                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded shadow-lg"
                        alt="{{ $product->name }}" style="width: 100%; height: 400px; object-fit: cover;">
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <div class="card shadow-sm p-4">
                    <h2 class="fw-bold">{{ $product->name }}</h2>
                    <p class="text-muted">{{ $product->description }}</p>
                    <h4 class="text-danger fw-bold">${{ number_format($product->price, 2) }}</h4>

                    <!-- Stock Status -->
                    <p class="mt-3">
                        <strong>Stock: </strong>
                        <span class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $product->stock > 0 ? $product->stock . ' available' : 'Out of stock' }}
                        </span>
                    </p>

                    <!-- Quantity Input -->
                    <div class="mb-3">
                        <label for="quantity" class="form-label fw-bold">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" value="1"
                            min="1" max="{{ $product->stock }}">
                    </div>

                    <!-- Add to Cart Button -->
                    @if ($product->stock > 0)
                        <a href="{{ route('cart.add', $product->id) }}" class="btn btn-lg btn-success w-100">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </a>
                    @else
                        <button class="btn btn-lg btn-secondary w-100" disabled>Out of Stock</button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-5 text-center"> <!-- Added margin-top for spacing -->
            <a href="{{ route('index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Back to Products
            </a>
        </div>
    </div>
@endsection
