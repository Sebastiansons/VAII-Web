document.addEventListener('DOMContentLoaded', function () {
    LoadFilterCategories();
});

function LoadFilterCategories() {
    $.ajax({
        url: `../back-end/Category/get_categories_filter.php`,
        type: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                if (response.sessionId != null) {
                    UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                }
                const select = document.getElementById('category-select');
                select.innerHTML = '';
                response.data.forEach((category, index) => {
                    const option = document.createElement('option');
                    option.value = category.CategoryID;
                    option.textContent = category.Name;
                    select.appendChild(option);

                    if (index === 0) {
                        select.value = category.CategoryID;
                    }
                });

                GetItem();
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

function GetItem() {
    const urlParams = new URLSearchParams(window.location.search);
    const productID = urlParams.get('productID');

    if (productID > 0) {
        $.ajax({
            url: `../back-end/Product/get_product.php?productID=${productID}`,
            type: 'GET',
            success: function (response) {
                if (response.sessionId != null) {
                    UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                }
                if (response.status === 'success') {
                    document.getElementById('itemID').value = response.data.ItemID;
                    document.getElementById('category-select').value = response.data.CategoryID;
                    document.getElementById('name').value = response.data.Name;
                    document.getElementById('description').value = response.data.Description;
                    document.getElementById('price').value = response.data.Price;
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
        $('#itemID').val(0);
    }
}

$(document).ready(function () {
    $('#editForm').on('submit', function (event) {
        event.preventDefault();

        const itemID = $('#itemID').val().trim();
        const categoryID = $('#category-select').val().trim();
        const name = $('#name').val().trim();
        const description = $('#description').val().trim();
        const price = $('#price').val().trim();
        const images = $('#images')[0].files;

        if (!categoryID || !name || !description || !price) {
            alert('Please fill in all required fields.');
            return;
        }

        if (images.length < 1 || images.length > 3) {
            alert('Please upload between 1 and 3 images.');
            return;
        }

        let totalSize = 0;
        for (let i = 0; i < images.length; i++) {
            const file = images[i];
            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                alert('Only JPG and PNG images are allowed.');
                return;
            }
            totalSize += file.size;
        }

        if (totalSize > 5 * 1024 * 1024) {
            alert('Total image size must not exceed 5MB.');
            return;
        }

        const formData = new FormData();
        formData.append('ItemID', itemID);
        formData.append('CategoryID', categoryID);
        formData.append('Name', name);
        formData.append('Description', description);
        formData.append('Price', price);
        for (let i = 0; i < images.length; i++) {
            formData.append('images[]', images[i]);
        }

        $.ajax({
            url: '../back-end/Product/update_product.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status === 'success') {
                    if (response.sessionId != null) {
                        UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                    }
                    alert('Item saved successfully');
                    window.location.href = "../pages/shop.html?categoryID=" + $('#category-select').val().trim();
                } else {
                    alert('Failed to save item: ' + response.error);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('An error occurred: ' + textStatus);
                console.log(jqXHR.responseText);
            }
        });
    });
});