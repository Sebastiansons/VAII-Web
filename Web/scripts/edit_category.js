$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const categoryID = urlParams.get('categoryID');

    if (categoryID > 0) {
        $.ajax({
            url: `../back-end/Category/get_category.php?categoryID=${categoryID}`,
            type: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    if (response.sessionId != null) {
                        UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                    }
                    const categoryDetails = response.data;
                    $('#categoryID').val(categoryDetails.CategoryID);
                    $('#categoryName').val(categoryDetails.Name);
                    $('#categoryDescription').val(categoryDetails.Description);
                    $('#categoryIcon').val(categoryDetails.Icon);
                } else {
                    alert(response.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('An error occurred: ' + textStatus);
                console.log(jqXHR.responseText);
            }
        });
    } else {
        $('#categoryID').val(0);
    }

    $('#categoryForm').on('submit', function (event) {
        event.preventDefault();

        const categoryID = $('#categoryID').val().trim();
        const categoryName = $('#categoryName').val().trim();
        const categoryDescription = $('#categoryDescription').val().trim();
        const categoryIcon = $('#categoryIcon').val().trim();

        if (!categoryName || !categoryIcon) {
            alert('Please fill in all required fields.');
            return;
        }

        if (!categoryIcon.startsWith('bi-')) {
            alert('Icon must start with "bi-".');
            return;
        }

        $.ajax({
            url: '../back-end/Category/update_category.php',
            type: 'POST',
            data: JSON.stringify({
                categoryID: categoryID,
                categoryName: categoryName,
                categoryDescription: categoryDescription,
                categoryIcon: categoryIcon
            }),
            contentType: 'application/json',
            success: function (response) {
                console.log(response);
                if (response.status === 'success') {
                    if (response.sessionId != null) {
                        UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                    }
                    alert(response.message);
                    window.location.href = "../index.html";
                } else if (response.status === 'expired') {
                    alert('SessionID has expired. Please log in again.');
                    Logout();
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
});