<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>All Users</title>

    <!-- website icon -->
    <link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">
</head>

<body>

    @include('admin.includes.sidebar')

    <!-- NAVBAR -->
    <section id="content">
        @include('admin.includes.navbar')

        <div class="container mt-4">
            <!-- Title & Breadcrumb -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3">All Users</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">All Users</li>
                </ol>
            </div>

            @if (session('success'))
                <div id="successMessage" class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User List</h3>
                </div>
                <div class="card-body">
                    <x-data-table :headers="[
                        'id' => '#',
                        'name' => 'Name',
                        'email' => 'Email',
                    ]" :rows="$rows" :actions="$actions"
                        route="{{ route('admin.all-users') }}" />
                </div>
            </div>

        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteUserForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- view User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewUserBody">
                    {{-- <img src="" alt="User Avatar" class="img-fluid rounded-circle mb-3" id="viewUserAvatar" style="width: 100px; height: 100px;"> --}}
                    <p><strong>Name:</strong> <span id="viewUserName"></span></p>
                    <p><strong>Email:</strong> <span id="viewUserEmail"></span></p>
                    <p><strong>Role:</strong> <span id="viewUserRole"></span></p>
                    <p><strong>Created At:</strong> <span id="viewUserCreatedAt"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setDeleteUser(userId) {
            document.getElementById("deleteUserForm").action =
                "/admin/all-users/" + userId;
        }

        document.addEventListener("DOMContentLoaded", function() {
            let alertBox = document.getElementById("successMessage");

            if (alertBox) {
                setTimeout(function() {
                    alertBox.style.transition = "opacity 1s ease-out";
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 1000); // Remove from DOM after fade out
                }, 2000); // Show for 2 seconds before fading
            }
        });

        function viewUserDetails(user) {
            // document.getElementById("viewUserAvatar").src = user.profile_picture || '/images/default-avatar.png';
            document.getElementById("viewUserName").innerText = user.name;
            document.getElementById("viewUserEmail").innerText = user.email;
            document.getElementById("viewUserRole").innerText = user.role;
            document.getElementById("viewUserCreatedAt").innerText =
                new Date(user.created_at).toLocaleDateString();

            const modal = new bootstrap.Modal(
                document.getElementById("viewUserModal")
            );
            modal.show();
        }
    </script>

    <!-- Include jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/admin.js') }}"></script>

    <!-- JavaScript to Set Form Action -->
    {{-- @vite(['resources/js/admin/all-users.js']) --}}
</body>

</html>
