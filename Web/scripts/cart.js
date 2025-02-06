$(document).ready(function () {
    const role = GetCookieValue('role');
    if (role === 'Customer') {
        loadCartItems();

        $('#orderButton').click(function () {
            $.ajax({
                url: '../back-end/Cart/get_cart.php',
                type: 'GET',
                success: function (response) {
                    if (response.status === 'success') {
                        const address = response.address;
                        const cartItems = response.data;
                        if (address) {
                            if (confirm(`Do you really want to order these items? They will be sent to the following address:\n${address.street} ${address.house_number}, ${address.city}, ${address.postal_code}, ${address.country}. If you want to change it, go change it in your account profile.`)) {
                                placeOrder();
                            }
                        } else {
                            alert('Please set your address in your profile before placing an order.');
                        }
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
    } else {
        alert('Only customers can view the cart.');
        window.location.href = '../index.html';
    }
});

function placeOrder() {
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

function loadCartItems() {
    $.ajax({
        url: '../back-end/Cart/get_cart.php',
        type: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const cartItems = response.data;
                const address = response.address;
                let addressHtml = '';

                if (address) {
                    addressHtml = `<p class="text-center mt-3" id="addressInfo">Delivery address is: ${address.street} ${address.house_number}, ${address.city}, ${address.postal_code}, ${address.country}</p>`;
                } else {
                    addressHtml = '<p class="text-center mt-3" id="addressInfo">Address is not set, please set it in your profile.</p>';
                }

                $('#addressInfo').html(addressHtml);

                if (cartItems.length === 0) {
                    $('#cartItems').html('<p class="text-center mt-3">Cart is empty</p>');
                    document.cookie = "cartCount=0; path=/";
                    $('.cartCount').text(GetCookieValue('cartCount'));
                    $('.cartCount').hide();
                } else {
                    let totalSum = 0;
                    let totalCount = 0;
                    let itemsHtml = '<table class="table table-bordered mt-3"><thead><tr><th>Name</th><th>Quantity</th><th>Image</th><th>Total Price</th><th>Delete</th></thead><tbody>';
                    cartItems.forEach(item => {
                        const firstImage = item.image ? item.image.split(',')[0] : 'default.jpg';
                        const imagePath = firstImage.startsWith('../../') ? firstImage.substring(6) : firstImage;
                        const itemTotal = item.price * item.quantity;
                        totalCount += item.quantity;
                        totalSum += itemTotal;
                        itemsHtml += `
                            <tr>
                                <td><a href="product_detail.html?productID=${item.ItemID}" class="item-name">${item.name}</a></td>
                                <td><input type="number" class="quantity-input" data-id="${item.cart_id}" value="${item.quantity}" min="1" max="10"></td>
                                <td><img src="../${imagePath}" class="img-fluid" alt="${item.name}" style="width: 100px; height: auto;"></td>
                                <td>${itemTotal.toFixed(2)}&#8364;</td>
                                <td><button class="removeItemButton btn btn-danger" data-id="${item.cart_id}">&times;</button></td>
                            </tr>
                        `;
                    });
                    itemsHtml += '</tbody></table>';
                    itemsHtml += `<div class="text-center"><button class="btn btn-primary mt-3" id="payButton">Pay (${totalSum.toFixed(2)}&#8364;)</button></div>`;

                    document.cookie = "cartCount=" + totalCount + "; path=/";
                    $('.cartCount').text(GetCookieValue('cartCount'));

                    if (totalCount == 0) {
                        $('.cartCount').hide();
                    } else {
                        $('.cartCount').show();
                    }

                    $('#cartItems').html(itemsHtml);

                    $('.removeItemButton').click(function () {
                        const cartId = $(this).data('id');
                        removeCartItem(cartId);
                    });

                    $('.quantity-input').change(function () {
                        const cartId = $(this).data('id');
                        const newQuantity = $(this).val();
                        if (newQuantity < 1) {
                            removeCartItem(cartId);
                        } else if (newQuantity > 10) {
                            alert('Quantity cannot be more than 10.');
                            $(this).val(10);
                        } else {
                            updateCartItemQuantity(cartId, newQuantity);
                        }
                    });

                    $('#payButton').click(function () {
                        $.ajax({
                            url: '../back-end/Order/create_order.php',
                            type: 'POST',
                            success: function (response) {
                                if (response.status === 'success') {
                                    alert(response.message);
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
                    });
                }
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

function updateCartItemQuantity(cartId, newQuantity) {
    $.ajax({
        url: '../back-end/Cart/update_quantity.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ cart_id: cartId, quantity: newQuantity }),
        success: function (response) {
            if (response.status === 'success') {
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
