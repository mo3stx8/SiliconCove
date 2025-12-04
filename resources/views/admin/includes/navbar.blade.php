<!-- NAVBAR -->
<nav>
    <i class='bx bx-menu toggle-sidebar'></i>
    <form action="#">
        <div class="form-group">
            <input type="text" placeholder="Search...">
            <i class='bx bx-search icon'></i>
        </div>
    </form>
    <span class="divider"></span>
    <div class="profile">
        <img src="{{ (auth()->user()->profile_picture ?? '') ? asset('storage/admin_profile_images/' . auth()->user()->profile_picture) : 'https://images.unsplash.com/photo-1517841905240-472988babdf9?ixid=MnwxMjA3fDB8MHxzZWFyc2h8NHx8cGVvcGxlfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60' }}" alt="">
        <ul class="profile-link">
            <li>
                <a href="{{ route('admin.profile') }}" class="text-decoration-none">
                    <i class='bx bxs-user-circle icon'></i> Profile
                </a>
            </li>
            <li>
                <!-- Logout Button (Triggers Modal) -->
                <button type="button" class="text-decoration-none border-0 bg-white" data-bs-toggle="modal" data-bs-target="#logoutModal" style="margin-left: 15px;">
                    <i class='bx bxs-log-out-circle text-danger'></i> <span class="text-danger">Logout</span>
                </button>
            </li>
        </ul>
    </div>
</nav>
<!-- NAVBAR -->

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
