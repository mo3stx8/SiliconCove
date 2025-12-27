@extends('layouts.admin')

@section('title', 'Admin Login')
<!-- website icon -->
<link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">
@section('content')

    <!-- Centered Container -->
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-dark text-white text-center py-3">
                    <h4 class="mb-0">Admin Login</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.login.submit') }}">
                        @csrf

                        <!-- Username Field -->
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username"
                                required>
                            <label for="username">Username</label>
                        </div>

                        <!-- Password Field with Toggle -->
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password" required>
                            <label for="password">Password</label>
                            <button type="button"
                                class="btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y me-2"
                                onclick="togglePassword('password')">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="btn btn-dark w-100 fw-bold py-2">Login</button>

                        <!-- Back to Home Button -->
                        <div class="text-center mt-3">
                            <a href="{{ route('index') }}" class="btn btn-outline-dark">
                                <i class="fas fa-home me-2"></i> Back to Home
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Success Modal -->
    <div class="modal fade" id="adminLoginSuccessModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <h4 class="text-success mb-3"><i class="fa fa-check-circle"></i> Success!</h4>
                    <p>Admin Logged out successful!</p>
                    <button type="button" class="btn btn-dark btn-sm" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ❌ Error Modal -->
    <div class="modal fade" id="adminLoginErrorModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <h4 class="text-danger mb-3"><i class="fa fa-times-circle"></i> Error!</h4>
                    <p>Invalid admin credentials.</p>
                    <button type="button" class="btn btn-dark btn-sm" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- @vite(['resources/js/admin/login.js']) --}}
    <script>
        // ✅ Password Toggle
        function togglePassword(id) {
            let input = document.getElementById(id);
            let icon = input.nextElementSibling.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // ✅ Show Modals if Session Data Exists
        @if (session('success'))
            document.addEventListener("DOMContentLoaded", function() {
                new bootstrap.Modal(document.getElementById('adminLoginSuccessModal')).show();
            });
        @endif

        @if ($errors->has('login'))
            document.addEventListener("DOMContentLoaded", function() {
                new bootstrap.Modal(document.getElementById('adminLoginErrorModal')).show();
            });
        @endif
    </script>
@endsection
