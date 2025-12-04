<div class="modal fade" id="viewProductModal{{ $product->product_id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="{{ asset('storage/' . $product->product?->image) }}"
                            class="img-fluid rounded"
                            alt="{{ $product->product?->name }}">
                    </div>
                    <div class="col-md-8">
                        <h4>{{ $product->product?->name }}</h4>
                        <p class="text-muted">- {{ $product->product?->description }}</p>
                        <div class="row mt-3">
                            <div class="col-6">
                                <h6>Price:</h6>
                                <p class="text-success">${{ number_format($product->product?->price, 2) }}</p>
                            </div>
                            <div class="col-6">
                                <h6>Current Stock:</h6>
                                <p>{{ $product->product?->stock }} units</p>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <h6>Total Sales:</h6>
                                <p class="text-primary">${{ number_format($product->total_revenue, 2) }}</p>
                            </div>
                            <div class="col-6">
                                <h6>Units Sold:</h6>
                                <p>{{ $product->total_quantity }} units</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
