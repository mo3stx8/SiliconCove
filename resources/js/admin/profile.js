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