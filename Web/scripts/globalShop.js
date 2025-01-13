function AddToCart(itemID) {
    if (confirm('Do you really want to add this item to the cart?')) {
        const role = GetCookieValue('role');
        if (role === 'Customer') {
            $.ajax({
                url: '../back-end/Cart/add_to_cart.php',
                type: 'POST',
                data: { ItemID: itemID },
                success: function (response) {
                    if (response.status === 'success') {
                        alert(response.message);
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
            alert('Only customers can add items to the cart.');
        }
    }
}