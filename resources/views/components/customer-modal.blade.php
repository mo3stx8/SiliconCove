<!-- website icon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
<div class="modal fade" id="viewCustomerModal{{ $customer->user->id ?? '' }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mx-auto mb-3" style="width: 150px; height: 150px; overflow: hidden; border-radius: 50%;">
                    <img src="{{ ($customer->user->profile_picture ?? '')
                        ? asset('storage/profile_images/' . $customer->user->profile_picture)
                        : asset('images/default-profile.jpg') }}"
                        class="w-100 h-100" style="object-fit: cover;" alt="Profile Picture">
                </div>
                <div class="row">
                    <div class="col-12">
                        <h4 class="text-center mb-3">{{ $customer->user->name ?? '' }}</h4>
                        <div class="card mb-3">
                            <div class="card-body">
                                <p><i class="fas fa-envelope me-2"></i>{{ $customer->user->email ?? '' }}</p>
                                <p><i class="fas fa-phone me-2"></i>{{ $customer->user->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-shopping-cart me-2"></i>Total Orders:</span>
                                    <strong>{{ $customer->purchase_count }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span><i class="fas fa-coins me-2"></i>Total Spent:</span>
                                    <strong class="text-primary">${{ number_format($customer->total_spent, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
