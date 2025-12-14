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
@if(session('success'))
    document.addEventListener("DOMContentLoaded", function () {
        new bootstrap.Modal(document.getElementById('adminLoginSuccessModal')).show();
    });
@endif

@if($errors->has('login'))
    document.addEventListener("DOMContentLoaded", function () {
        new bootstrap.Modal(document.getElementById('adminLoginErrorModal')).show();
    });
@endif