{{-- Sidebar  --}}
<div class="col-lg-3 mb-4">

    {{-- Back to Home Button  --}}
    <div class="mb-3">
        <a href="{{ route('index') }}" class="btn btn-outline-primary w-100">
            <i class="fa fa-arrow-left"></i> Back to Home
        </a>
    </div>

    @php
        $user = auth()->user();
    @endphp

    {{-- User Profile Card  --}}
    <div class="card text-center shadow-sm">
        <div class="card-body">

            {{-- Profile Picture  --}}
            <img src="{{ $user->profile_picture
                ? asset('storage/profile_images/' . $user->profile_picture)
                : asset('images/default-profile.jpg') }}"
                alt="User Avatar" class="rounded-circle mb-3" width="80">

            <h5 class="mb-1">{{ $user->name }}</h5>
            <small class="text-muted">
                Joined {{ $user->created_at->format('F d, Y') }}
            </small>
        </div>
    </div>

    {{-- Navigation Links  --}}
    <div class="mt-3">
        <nav class="list-group">

            {{-- Orders  --}}
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                {{ request()->routeIs('account.index') ? 'active' : '' }}"
                href="{{ route('account.index') }}">
                <i class="fa fa-shopping-bag"></i> Orders List
                <span class="badge bg-secondary">{{ $user->orders->count() }}</span>
            </a>

            {{-- Saved Cart  --}}
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                {{ request()->routeIs('cart.index') ? 'active' : '' }}"
                href="{{ route('cart.index') }}">
                <i class="fa fa-shopping-cart"></i> Saved Cart
                <span class="badge bg-secondary">
                    {{ \App\Models\Cart::where('user_id', $user->id)->count() }}
                </span>
            </a>

            {{-- Profile Settings  --}}
            <a class="list-group-item list-group-item-action
                {{ request()->routeIs('account.profileSettings') ? 'active' : '' }}"
                href="{{ route('account.profileSettings') }}">
                <i class="fa fa-user"></i> Profile Settings
            </a>

            {{-- Addresses  --}}
            <a class="list-group-item list-group-item-action
                {{ request()->routeIs('account.addresses') ? 'active' : '' }}"
                href="{{ route('account.addresses') }}">
                <i class="fa fa-map-marker"></i> Addresses
            </a>

            {{-- Change Password  --}}
            <a class="list-group-item list-group-item-action
                {{ request()->routeIs('account.changePassword') ? 'active' : '' }}"
                href="#">
                <i class="fa fa-lock"></i> Change Password
            </a>

            {{-- unlink google --}}
            @if($user->google_id)
            <form action="{{ route('google.unlink') }}" method="POST">
                @csrf
                <button type="submit" class="list-group-item list-group-item-action text-start text-warning">
                    <i class="fa fa-unlink"></i> Unlink Google Account
                </button>
            </form>
            @else
            {{-- relink google account --}}
            <a class="list-group-item list-group-item-action text-start text-success"
                href="{{ route('google.login') }}">
                <i class="fa fa-link"></i> Link Google Account
            </a>
            @endif

            {{-- unlink github --}}
            @if($user->github_id)
            <form action="{{ route('github.unlink') }}" method="POST">
                @csrf
                <button type="submit" class="list-group-item list-group-item-action text-start text-warning">
                    <i class="fa fa-unlink"></i> Unlink GitHub Account
                </button>
            </form>
            @else
            {{-- relink github account --}}
            <a class="list-group-item list-group-item-action text-start text-success"
                href="{{ route('github.login') }}">
                <i class="fa fa-link"></i> Link GitHub Account
            </a>
            @endif

            {{-- unlink facebook --}}
            {{-- @if($user->facebook_id)
            <form action="{{ route('facebook.unlink') }}" method="POST">
                @csrf
                <button type="submit" class="list-group-item list-group-item-action text-start text-warning">
                    <i class="fa fa-unlink"></i> Unlink Facebook Account
                </button>
            </form>
            @else --}}
            {{-- relink facebook account --}}
            {{-- <a class="list-group-item list-group-item-action text-start text-success"
                href="{{ route('facebook.login') }}">
                <i class="fa fa-link"></i> Link Facebook Account
            </a>
            @endif --}}

            {{-- Logout  --}}
            <button type="button" class="list-group-item list-group-item-action text-start text-danger"
                data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                <i class="fa fa-sign-out"></i> Logout
            </button>

            
        </nav>
    </div>
</div>
{{-- End of Sidebar  --}}

{{-- Logout Confirmation Modal  --}}
<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutConfirmLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fa fa-exclamation-circle text-warning" style="font-size: 2rem;"></i>
                <p class="mt-3">Are you sure you want to logout?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
