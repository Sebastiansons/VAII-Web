$(document).ready(function () {
    const role = GetCookieValue('role');
    if (role === 'Customer') {
        loadCartItems();

        $('#orderButton').click(function () {
            if (confirm('Do you really want to order these items? They will be sent to the address you have in your profile. If you want to change it, go change it in your account profile.')) {
                $.ajax({
                    url: '../back-end/Cart/order_items.php',
                    type: 'POST',
                    success: function (response) {
                        if (response.status === 'success') {
                            alert('Order placed successfully.');
                            loadCartItems();
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
    } else {
        alert('Only customers can view the cart.');
        window.location.href = '../index.html';
    }
});

function loadCartItems() {
    $.ajax({
        url: '../back-end/Cart/get_cart.php',
        type: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const cartItems = response.data;
                let itemsHtml = '';
                cartItems.forEach(item => {
                    const firstImage = item.image ? item.image.split(',')[0] : 'default.jpg';
                    const imagePath = firstImage.startsWith('../../') ? firstImage.substring(6) : firstImage;
                    itemsHtml += `
                        <div class="card mb-3">
                            <img src="../${imagePath}" class="card-img-top" alt="${item.name}" style="width: 200px; height: auto;">
                            <div class="card-body">
                                <h5 class="card-title">${item.name}</h5>
                                <p class="card-text">${item.description}</p>
                                <p class="card-text"><strong>Price:</strong> ${item.price}&#8364;</p>
                                <button class="removeItemButton btn btn-danger" data-id="${item.cart_id}">Remove</button>
                            </div>
                        </div>
                    `;
                });
                $('#cartItems').html(itemsHtml);

                $('.removeItemButton').click(function () {
                    const cartId = $(this).data('id');
                    removeCartItem(cartId);
                });
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

function removeCartItem(cartId) {
    $.ajax({
        url: '../back-end/Cart/remove_item.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ cart_id: cartId }),
        success: function (response) {
            if (response.status === 'success') {
                alert('Item removed successfully.');
                loadCartItems();
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