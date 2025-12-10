<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- website icon -->
    <link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">
</head>

<body>

    @include('admin.includes.sidebar')

    <!-- NAVBAR -->
    <section id="content">
        @include('admin.includes.navbar')

        <div class="container py-4">
            <h2 class="mb-4">Admin Profile</h2>
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                @csrf
                <div class="col-md-4 text-center">
                    <div class="mb-3">
                        {{-- @php dd($admin->profile_picture); @endphp --}}
                        <img src="{{ $admin->profile_picture ? asset('storage/admin_profile_images/' . $admin->profile_picture) : 'https://images.unsplash.com/photo-1517841905240-472988babdf9?ixid=MnwxMjA3fDB8MHxzZWFyc2h8NHx8cGVvcGxlfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60' }}"
                            class="rounded-circle img-thumbnail" width="150" height="150" alt="Profile Picture">
                    </div>
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Change Profile Picture</label>
                        <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                        @error('profile_picture')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- Separator -->
                <div class="col-md-1 d-none d-md-flex align-items-center justify-content-center">
                    <div style="width:2px; height:100px; background:#dee2e6;"></div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" class="form-control" required>
                        @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $admin->username) }}" class="form-control" required>
                        @error('username')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password <span class="text-muted small">(leave blank to keep current)</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fa fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                <i class="fa fa-eye" id="togglePasswordConfirmIcon"></i>
                            </button>
                        </div>
                        <div class="text-danger small" id="passwordMatchError" style="display:none;">
                            Password and Confirm New Password do not match.
                        </div>
                        @error('password_confirmation')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary" id="updateProfileBtn">Update Profile</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        // Toggle show/hide password and confirm password
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('togglePassword');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            const toggleBtnConfirm = document.getElementById('togglePasswordConfirm');
            const toggleIconConfirm = document.getElementById('togglePasswordConfirmIcon');
            const passwordMatchError = document.getElementById('passwordMatchError');
            const form = document.querySelector('form');
            const updateBtn = document.getElementById('updateProfileBtn');

            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function () {
                    const type = passwordInput.type === 'password' ? 'text' : 'password';
                    passwordInput.type = type;
                    toggleIcon.classList.toggle('fa-eye');
                    toggleIcon.classList.toggle('fa-eye-slash');
                });
            }
            if (toggleBtnConfirm && passwordConfirmInput && toggleIconConfirm) {
                toggleBtnConfirm.addEventListener('click', function () {
                    const type = passwordConfirmInput.type === 'password' ? 'text' : 'password';
                    passwordConfirmInput.type = type;
                    toggleIconConfirm.classList.toggle('fa-eye');
                    toggleIconConfirm.classList.toggle('fa-eye-slash');
                });
            }

            // Password match validation
            function checkPasswordMatch() {
                if (passwordInput.value !== passwordConfirmInput.value) {
                    passwordMatchError.style.display = (passwordInput.value || passwordConfirmInput.value) ? 'block' : 'none';
                    updateBtn.disabled = true;
                } else {
                    passwordMatchError.style.display = 'none';
                    updateBtn.disabled = false;
                }
            }

            passwordInput.addEventListener('input', checkPasswordMatch);
            passwordConfirmInput.addEventListener('input', checkPasswordMatch);

            form.addEventListener('submit', function(e) {
                if (passwordInput.value != passwordConfirmInput.value) {
                    passwordMatchError.style.display = 'block';
                    passwordConfirmInput.focus();
                    updateBtn.disabled = true;
                    e.preventDefault();
                }
            });

            // Disable button on load if not matching
            checkPasswordMatch();
        });
    </script>
</body>

</html>
