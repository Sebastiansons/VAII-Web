document.addEventListener('DOMContentLoaded', function () {
    function LoadUserProfile() {
        CheckSessionID();

        $.ajax({
            url: '../back-end/get_profile.php',
            type: 'POST',
            success: function (response) {
                if (response.status === 'success') {
                    $('#username').val(response.data.username);
                    $('#email').val(response.data.email);
                    $('#role').val(response.data.role);
                    $('#street').val(response.data.street);
                    $('#house_number').val(response.data.house_number);
                    $('#city').val(response.data.city);
                    $('#postal_code').val(response.data.postal_code);

                    UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                } else if (response.status === 'expired') {
                    alert('SessionID has expired. Please log in again.');
                    Logout();
                } else {
                    alert('Failed to load user profile.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('An error occurred: ' + textStatus);
            }
        });
    }

    var form = document.getElementById('profileForm');
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        let formData = new FormData(this);

        CheckSessionID();

        let street = document.getElementById('street').value.trim();
        let houseNumber = document.getElementById('house_number').value.trim();
        let city = document.getElementById('city').value.trim();
        let postalCode = document.getElementById('postal_code').value.trim();

        if (!street || !houseNumber || !city || !postalCode) {
            alert('Please fill in all fields.');
            return;
        }

        if (street.length < 5 || city.length < 5) {
            alert('Street, city must be at least 5 characters long.');
            return;
        }

        if (houseNumber.length < 3) {
            alert('HouseNumber must be at least 3 characters long.');
            return;
        }

        if (postalCode.length !== 5 || !/^\d{5}$/.test(postalCode)) {
            alert('Invalid postal code for Slovakia. It should be a 5-digit number.');
            return;
        }

        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status === 'success') {
                    alert(response.message);
                    UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                } else {
                    alert(response.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('An error occurred: ' + textStatus);
                console.log(jqXHR.responseText);
            }
        });
    });
    LoadUserProfile();
});