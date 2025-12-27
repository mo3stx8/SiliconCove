// Toggle password visibility
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
            // Show success modal if login successful
            @if (session('success'))
                var successModal = document.getElementById('adminLoginSuccessModal');
                if (successModal) {
                    var bsSuccessModal = new bootstrap.Modal(successModal);
                    bsSuccessModal.show();
                }
            @endif

            // Show error modal if login failed
            @if (session('error'))
                var errorModal = document.getElementById('adminLoginErrorModal');
                if (errorModal) {
                    var bsErrorModal = new bootstrap.Modal(errorModal);
                    bsErrorModal.show();
                }
            @endif
        });