<!-- Sidebar -->
<div class="col-lg-3 mb-4">
    <!-- Back to Home Button -->
    <div class="mb-3">
        <a href="{{ route('index') }}" class="btn btn-outline-primary w-100">
            <i class="fa fa-arrow-left"></i> Back to Home
        </a>
    </div>

    <!-- User Profile Card -->
    <div class="card text-center shadow-sm">
        <div class="card-body">
            <!-- Profile Picture -->
            <img src="{{ auth()->user()->profile_picture
                ? asset('storage/profile_images/' . auth()->user()->profile_picture)
                : asset('images/default-profile.jpg') }}"
                alt="User Avatar" class="rounded-circle mb-3" width="80">

            <h5 class="mb-1">{{ Auth::user()->name }}</h5>
            <small class="text-muted">Joined {{ Auth::user()->created_at->format('F d, Y') }}</small>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="mt-3">
        <nav class="list-group">
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->routeIs('account.index') ? 'active' : '' }}"
                href="{{ route('account.index') }}">
                <i class="fa fa-shopping-bag"></i> Orders List
                <span class="badge bg-secondary">{{ Auth::user()->orders->count() ?? 0 }}</span>
            </a>
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->routeIs('cart.index') ? 'active' : '' }}"
                href="{{ route('cart.index') }}">
                <i class="fa fa-shopping-cart"></i> Saved Cart
                <span
                    class="badge bg-secondary">{{ \App\Models\Cart::where('user_id', auth()->id())->count() ?? 0 }}</span>
            </a>
            <a class="list-group-item list-group-item-action {{ request()->routeIs('account.profileSettings') ? 'active' : '' }}"
                href="{{ route('account.profileSettings') }}">
                <i class="fa fa-user"></i> Profile Settings
            </a>
            <a class="list-group-item list-group-item-action {{ request()->routeIs('account.addresses') ? 'active' : '' }}"
                href="{{ route('account.addresses') }}">
                <i class="fa fa-map-marker"></i> Addresses
            </a>
        </nav>
    </div>
</div>
