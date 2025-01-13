document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registrationForm');
    const username = document.getElementById('username');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');

    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        let valid = true;

        // Username validation
        const usernameValue = username.value.trim();
        if (usernameValue === '' || usernameValue.length > 30 || /[^a-zA-Z0-9]/.test(usernameValue)) {
            alert('Username must be between 1 and 30 characters and contain no special characters.');
            valid = false;
        }

        // Email validation
        const emailValue = email.value.trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailValue)) {
            alert('Please enter a valid email address.');
            valid = false;
        }

        // Password match validation
        if (password.value !== confirmPassword.value) {
            alert('Passwords do not match.');
            valid = false;
        }

        if (valid) {
            // Send AJAX request
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function (response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        window.location.href = "../pages/login.html";
                    } else {
                        alert(response.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('An error occurred: ' + textStatus);
                }
            });
        }
    });
});