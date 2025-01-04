$(document).ready(function () {
    if (!IsSessionValid()) {
        alert("Session has expired! Please log in again.");
        window.location.href = "../../../VAII-Web/Web/pages/login.html";
    }

    function LoadUserProfile() {
        console.log(GetCookieValue('sessionID'));
        $.ajax({
            url: '../back-end/get_profile.php',
            type: 'POST',
            data: { session_id: GetCookieValue('sessionID') },
            success: function (response) {
                if (response.success) {
                    $('#username').val(response.data.username);
                    $('#email').val(response.data.email);
                    $('#role').val(response.data.role);
                    $('#street').val(response.data.street);
                    $('#house_number').val(response.data.house_number);
                    $('#city').val(response.data.city);
                    $('#postal_code').val(response.data.postal_code);
                   // $('#country').val(response.data.country);
                } else {
                    alert('Failed to load user profile.');
                }
            },
            error: function (xhr, status, error) {
                alert('An error occurred while loading the user profile. Please try again.');
            }
        });
    }

    LoadUserProfile();
});