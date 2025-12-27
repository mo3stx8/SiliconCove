@extends('layouts.layout')

@section('title', 'Login')
<!-- website icon -->
<link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">
@section('content')

    <!-- Breadcrumb -->
    <div class="row">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Login</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Login Form -->
    <div class="row justify-content-center">
        <div class="col-md-6">

            <!-- Login & Sign Up Toggle Buttons -->
            <div class="d-flex justify-content-center mb-3">
                {{-- <a href="{{ route('login') }}" class="btn btn-primary me-2">Log In</a> --}}
                {{-- <a href="{{ route('signup') }}" class="btn btn-outline-primary">Sign Up</a> --}}
            </div>
            <div class="card">
                <div class="card-header text-center">Login to Your Account</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('password')">
                                    <span><i class="fa fa-eye"></i></span>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('password.request') }}">Forgot your password?</a>
                        </div>

                        {{-- <a href="{{ route('password.request') }}">Forgot your password?</a> --}}

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                Don't have an account? <a href="{{ route('signup') }}">Sign up</a>
            </div>

            {{-- socail login  --}}
            <div class="text-center mt-3">
                <div class="d-flex justify-content-center gap-3">
                    {{-- google login --}}
                    <a href="{{ route('google.login') }}" class="btn btn-light rounded-circle shadow-sm p-2">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="20" alt="Google Logo">
                    </a>

                    {{-- github login --}}
                    <a href="{{ route('github.login') }}" class="btn btn-light rounded-circle shadow-sm p-2">
                        <img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" width="20" alt="GitHub Logo">
                    </a>

                    {{-- facebook login --}}
                    <a href="{{ route('facebook.login') }}" class="btn btn-light rounded-circle shadow-sm p-2">
                        <img src="https://www.facebook.com/images/fb_icon_325x325.png" width="20" alt="Facebook Logo">
                    </a>
                </div>

                <small class="text-muted d-block mt-2">
                    We won't post anything without your permission.
                </small>
            </div>

        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="statusSuccessModal" tabindex="-1" aria-labelledby="statusSuccessModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-lg-4">
                    <h4 class="text-success mt-3">Success!</h4>
                    <p class="mt-3" id="successMessage"data-message="{{ session('success') }}">Login successful.</p>
                    <button type="button" class="btn btn-sm mt-3 btn-success" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="statusErrorsModal" tabindex="-1" aria-labelledby="statusErrorsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-lg-4">
                    <h4 class="text-danger mt-3">Error!</h4>
                    <p class="mt-3" id="errorMessage"
                        data-message="{{ session('error') ?? ($errors->first('email') ?? ($errors->first('password') ?? '')) }}">
                        Invalid email or password.</p>
                    <button type="button" class="btn btn-sm mt-3 btn-danger" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function togglePassword(id) {
            let input = document.getElementById(id);
            let icon = input.nextElementSibling.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // Wait for DOM to load
        document.addEventListener("DOMContentLoaded", function() {
            // Show success modal if login successful or message exists
            @if (session('success'))
                var successModal = document.getElementById('statusSuccessModal');
                if (successModal) {
                    var bsSuccessModal = new bootstrap.Modal(successModal);
                    var successMessage = document.getElementById('successMessage');
                    successMessage.innerText = "{{ session('success') }}";
                    bsSuccessModal.show();
                }
            @endif

            // Show error modal if login failed
            @if (session('error') || $errors->any())
                var errorModal = document.getElementById('statusErrorsModal');
                if (errorModal) {
                    var bsErrorModal = new bootstrap.Modal(errorModal);
                    var errorMessage = document.getElementById('errorMessage');

                    @if (session('error'))
                        errorMessage.innerText = "{{ session('error') }}";
                    @elseif ($errors->has('email'))
                        errorMessage.innerText = "{{ $errors->first('email') }}";
                    @elseif ($errors->has('password'))
                        errorMessage.innerText = "{{ $errors->first('password') }}";
                    @else
                        errorMessage.innerText = "Login failed. Please try again.";
                    @endif

                    bsErrorModal.show();
                }
            @endif
        });
    </script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    {{-- @vite(['resources/js/login.js']) --}}
@endsection
