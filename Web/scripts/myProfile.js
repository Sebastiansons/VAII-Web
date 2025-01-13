document.addEventListener('DOMContentLoaded', function () {
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

        if (street.length < 5 || houseNumber.length < 5 || city.length < 5 || postalCode.length < 5) {
            alert('All fields must be at least 5 characters long.');
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