$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const orderID = urlParams.get('orderID');

    if (orderID) {
        loadOrderDetails(orderID);
    } else {
        alert('Order ID is required.');
    }

    function loadOrderDetails(orderID) {
        $.ajax({
            url: '../back-end/Order/order_detail.php',
            type: 'GET',
            data: { orderID: orderID },
            success: function (response) {
                if (response.status === 'success') {
                    renderOrderDetails(response.order);
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

    function renderOrderDetails(order) {
        $('#orderTitle').text(`Order Details - Order #${order.orderID}`);
        let itemsHtml = '<table class="table table-bordered"><thead><tr><th>Image</th><th>Name</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead><tbody>';
        order.items.forEach(item => {
            const firstImage = item.first_image ? item.first_image.split(',')[0] : 'default.jpg';
            const imagePath = firstImage.startsWith('../../') ? firstImage.substring(6) : firstImage;
            const itemTotal = item.Price * item.quantity;
            itemsHtml += `
                <tr>
                    <td><img src="../${imagePath}" alt="${item.Name}" style="width: 100px; height: auto;"></td>
                    <td><a href="product_detail.html?productID=${item.ItemID}">${item.Name}</a></td>
                    <td>${item.quantity}</td>
                    <td>${item.Price}&#8364;</td>
                    <td>${itemTotal.toFixed(2)}&#8364;</td>
                </tr>
            `;
        });
        itemsHtml += '</tbody></table>';
        $('#orderItemsTable').html(itemsHtml);
    }
});