<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="{{ route('index') }}">
            <i class="fas fa-store me-2"></i> Silicon Cove <!-- flag -->
        </a>

        <!-- Mobile Toggle Button (JS-based) -->
        <button class="navbar-toggler" type="button" id="customNavbarToggler" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <div class="d-flex flex-column flex-md-row w-100 justify-content-between align-items-center">
                <!-- Left Menu -->
                <ul class="navbar-nav text-center text-md-start">
                    <li class="nav-item"><a class="nav-link" href="{{ route('index') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('product') }}">Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
                </ul>

                <!-- Cart & User Profile -->
                <div class="d-flex align-items-center mt-3 mt-md-0">
                    <!-- Cart -->
                    <a class="btn btn-success btn-sm me-2 d-none" href="{{ route('cart.index') }}">
                        <i class="fa fa-shopping-cart"></i> Cart
                        <span class="badge badge-light cart-count">
                            {{ auth()->check() ? \App\Models\Cart::where('user_id', auth()->id())->distinct('product_id')->count() : 0 }}
                        </span>
                    </a>

                    @if(Auth::check())
                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center"
                            type="button" id="userDropdown" data-bs-toggle="dropdown">

                            <!-- Profile Image -->
                            <img src="{{ Auth::user()->profile_picture
                            ? asset('storage/profile_images/' . Auth::user()->profile_picture)
                            : asset('images/default-profile.jpg') }}"
                                class="rounded-circle me-2" width="30" height="30" alt="Profile">
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('account.index') }}">My Account</a>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                                    Logout
                                </button>
                            </li>
                        </ul>
                    </div>

                    @else
                        <!-- check if admin -->
                        @if(Auth::guard('admin')->check())
                        <a class="btn btn-outline-light btn-sm me-2" href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-user-shield"></i> Admin
                        </a>
                        @else
                        <!-- Login & Sign Up Buttons -->
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">Log In</a>
                        <a href="{{ route('signup') }}" class="btn btn-primary btn-sm">Sign Up</a> ||
                        <a href="{{ route('admin.login') }}" class="btn btn-outline-danger btn-sm me-2">
                            <i class="bi bi-shield-lock"></i> Admin
                        </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</nav>


<!-- Logout Confirmation Modal -->
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

<!-- Bootstrap JS (Ensure it's included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Custom JS toggle for mobile navbar
    document.addEventListener('DOMContentLoaded', function() {
        var toggler = document.getElementById('customNavbarToggler');
        var navbarContent = document.getElementById('navbarContent');

        if (toggler && navbarContent) {
            toggler.addEventListener('click', function() {
                navbarContent.classList.toggle('show');
                // Update aria-expanded for accessibility
                var expanded = toggler.getAttribute('aria-expanded') === 'true';
                toggler.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            });

            // Optional: close navbar when a nav-link is clicked (for single page apps)
            navbarContent.querySelectorAll('.nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768 && navbarContent.classList.contains('show')) {
                        navbarContent.classList.remove('show');
                        toggler.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        }
    });
</script>
