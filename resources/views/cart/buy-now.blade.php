@extends('layouts.layout')
<!-- website icon -->
<link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">
@push('styles')
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    {{-- <link rel="stylesheet" href="{{ asset('css/buy-now.css') }}"> --}}
@endpush

@section('title')
    Silicon Cove - Checkout
@endsection
@section('content')
    <div class="container checkout-container" style="margin-top: 70px;">
        <!-- 🛑 Back to Cart Button -->
        <a href="{{ route('cart.index') }}" class="btn btn-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Back to Cart
        </a>

        <h2 class="my-4 text-center">Checkout</h2>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row justify-content-center">
            @php
                $grandTotal = $cartItems->sum(function ($item) {
                    return $item->quantity * $item->product->price;
                });
            @endphp

            <!-- 📍 Shipping Address -->
            <div class="col-md-6 section-spacing">
                <h4 class="mb-3 text-center">📍 Delivery Address</h4>
                <div class="address-card">
                    @if ($address)
                        <div class="address-details">
                            <p><strong>Address:</strong> {{ $address->address }}</p>
                            <p><strong>Area:</strong> {{ $address->area }}</p>
                            <p><strong>Region:</strong> {{ $address->region }}</p>
                            {{-- <p><strong>Zip Code:</strong> {{ $address->zip_code }}</p> --}}
                        </div>
                        <div class="address-actions">
                            <a href="{{ route('account.addresses') }}" class="btn btn-primary">Edit Address</a>
                            {{-- <a href="{{ route('account.addresses') }}" class="btn btn-info">View Address</a> --}}
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <strong>⚠ Please fill in your address first.</strong>
                            <span class="arrow">⬇</span>
                        </div>
                        <a href="{{ route('account.addresses') }}" class="btn btn-danger">Fill Address</a>
                    @endif
                </div>
            </div>

            <!-- 💳 Payment Section -->
            <div class="col-md-6 section-spacing">
                <h4 class="mb-3 text-center">💳 Payment Method</h4>
                <div class="payment-card">
                    <form action="{{ route('order.place') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="grand_total" value="{{ $grandTotal }}">
                        <!-- for instant user order -->
                        @if ($cartItems[0]->instant_order)
                            <input type="hidden" name="instant_order" value="{{ $cartItems[0]->instant_order }}">
                        @endif

                        @foreach ($cartItems as $item)
                            <input type="checkbox" name="selected_cart_items[]" value="{{ $item->id }}" class="d-none"
                                checked>
                            <!-- for instant user order -->
                            @if ($cartItems[0]->instant_order)
                                <input type="checkbox" name="selected_product_quantities[]" value="{{ $item->quantity }}"
                                    class="d-none" checked>
                            @endif
                        @endforeach
                        <div class="form-check mb-3">
                            <input class="form-check-input payment-method" type="radio" name="payment_method"
                                value="cod" checked id="codPayment">
                            <label class="form-check-label">Cash on Delivery (COD)</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input payment-method" type="radio" name="payment_method"
                                value="Kuraimi Bank USD" id="gcashPayment">
                            <label class="form-check-label">Kuraimi Bank USD (QR Code)</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input payment-method" type="radio" name="payment_method"
                                value="Kuraimi Bank SR" id="gcashPayment2">
                            <label class="form-check-label">Kuraimi Bank SR (QR Code)</label>
                        </div>

                        <!-- GCash Payment Section -->
                        <div id="gcashSection" class="d-none">
                            <div class="text-center mb-3">
                                <img src="{{ asset('images/USD.jpeg') }}" alt="Account QR Code" class="img-fluid"
                                    width="200" height="200">
                                <p class="mt-2">Account Number: 3144073718</p>
                                <p>Amount to Pay: ${{ number_format($grandTotal, 2) }}</p>
                            </div>

                            <div class="mb-3">
                                <label for="proofOfPayment" class="form-label">Upload Proof of Payment</label>
                                <input type="file" class="form-control" id="proofOfPayment" name="proof_of_payment"
                                    accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Preview</label>
                                <div id="imagePreview" class="text-center">
                                    <img src="" alt="Preview" class="img-fluid d-none" style="max-height: 200px;">
                                </div>
                            </div>
                        </div>

                        <!-- GCash Payment Section 2 -->
                        <div id="gcashSection2" class="d-none">
                            <div class="text-center mb-3">
                                <img src="{{ asset('images/SR.jpeg') }}" alt="Account QR Code" class="img-fluid"
                                    width="200" height="200">
                                <p class="mt-2">Account Number: 3144048716</p>
                                <p>Amount to Pay: ${{ number_format($grandTotal, 2) }}</p>
                            </div>

                            <div class="mb-3">
                                <label for="proofOfPayment2" class="form-label">Upload Proof of Payment</label>
                                <input type="file" class="form-control" id="proofOfPayment2" name="proof_of_payment2"
                                    accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Preview</label>
                                <div id="imagePreview2" class="text-center">
                                    <img src="" alt="Preview" class="img-fluid d-none"
                                        style="max-height: 200px;">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success w-100" {{ !$address ? 'disabled' : '' }}>
                                Confirm Order
                            </button>
                            @if (!$address)
                                <small class="text-danger mt-2 d-block">Please add an address to proceed</small>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="order-summary">
            <h4 class="mt-4 text-center">🛍 Order Summary</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach ($cartItems as $item)
                            @php
                                $totalPrice = $item->quantity * $item->product->price;
                                $grandTotal += $totalPrice;
                            @endphp
                            <tr>
                                <td><img src="{{ asset('storage/' . $item->product->image) }}" class="img-thumbnail"
                                        width="50"></td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($totalPrice, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                            <td><strong>${{ number_format($grandTotal, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
                    // Toggle GCash payment section
                    $('.payment-method').change(function() {
                        if ($('#gcashPayment').is(':checked')) {
                            $('#gcashSection').removeClass('d-none');
                            $('#proofOfPayment').prop('required', true); // Make proof_of_payment required
                        } else {
                            $('#gcashSection').addClass('d-none');
                            $('#proofOfPayment').prop('required', false); // Remove required attribute
                        }
                    });

                    // Handle image preview
                    $('#proofOfPayment').change(function() {
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                $('#imagePreview img').attr('src', e.target.result).removeClass('d-none');
                            }
                            reader.readAsDataURL(file);
                        }
                    });

                    // Handle image preview for the second GCash
                    $('#proofOfPayment2').change(function() {
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                $('#imagePreview2 img').attr('src', e.target.result).removeClass('d-none');
                            }
                            reader.readAsDataURL(file);
                        }
                    });

                    // Toggle GCash payment section 2
                    $('.payment-method').change(function() {
                        if ($('#gcashPayment2').is(':checked')) {
                            $('#gcashSection2').removeClass('d-none');
                            $('#proofOfPayment2').prop('required', true); // Make proof_of_payment2 required
                        } else {
                            $('#gcashSection2').addClass('d-none');
                            $('#proofOfPayment2').prop('required', false); // Remove required attribute
                        }
                    });
                });
    </script>
@endpush
