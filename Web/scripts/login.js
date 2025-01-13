document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    const usernameOrEmail = document.getElementById('username_or_email');
    const password = document.getElementById('password');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        let valid = true;

        const usernameOrEmailValue = usernameOrEmail.value.trim();
        if (usernameOrEmailValue === '') {
            alert('Username or Email must not be empty.');
            valid = false;
        }

        if (password.value.trim() === '') {
            alert('Password must not be empty.');
            valid = false;
        }

        if (valid) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function (response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        SetCookies(response);
                    } else {
                        alert(response.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('An error occurred: ' + textStatus);
                    console.log(jqXHR.responseText);
                }
            });
        }
    });
});