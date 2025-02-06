function deparam(query) {
    const pairs = query.split('&');
    const result = {};
    pairs.forEach(pair => {
        const [key, value] = pair.split('=');
        result[decodeURIComponent(key)] = decodeURIComponent(value || '');
    });
    return result;
}

$(document).ready(function () {
    $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd"
    });

    const role = GetCookieValue('role');
    if (role === 'Support') {
        $('#filterForm').show();
    }

    $('#orderFilterForm').on('submit', function (e) {
        e.preventDefault();
        const filters = $(this).serialize();
        loadOrders(1, filters);
    });

    function loadOrders(page = 1, filters = '') {
        $.ajax({
            url: '../back-end/Order/get_orders.php',
            type: 'GET',
            data: { page: page, ...deparam(filters) },
            success: function (response) {
                if (response.status === 'success') {
                    renderOrders(response.orders);
                    renderPagination(response.total_pages, page);
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

    function renderOrders(orders) {
        let ordersHtml = '<table class="table table-bordered"><thead><tr><th>Order ID</th><th>Created At</th><th>Total Price</th><th>Status</th><th>Detail</th></tr></thead><tbody>';
        orders.forEach(order => {
            let statusClass = '';
            switch (order.statusName) {
                case 'Canceled':
                    statusClass = 'text-danger';
                    break;
                case 'Processing':
                    statusClass = 'text-primary';
                    break;
                case 'Sent':
                    statusClass = 'text-success';
                    break;
                case 'Created':
                default:
                    statusClass = 'text-info';
                    break;
            }
            ordersHtml += `
                <tr>
                    <td>${order.orderID}</td>
                    <td>${order.created_at}</td>
                    <td>${order.total_price}&#8364;</td>
                    <td class="${statusClass}">
                        ${role === 'Support' ? `
                        <select class="status-select" data-order-id="${order.orderID}">
                            <option value="Created" ${order.statusName === 'Created' ? 'selected' : ''}>Created</option>
                            <option value="Processing" ${order.statusName === 'Processing' ? 'selected' : ''}>Processing</option>
                            <option value="Sent" ${order.statusName === 'Sent' ? 'selected' : ''}>Sent</option>
                            <option value="Canceled" ${order.statusName === 'Canceled' ? 'selected' : ''}>Canceled</option>
                        </select>
                        ` : order.statusName}
                    </td>
                    <td><a href="order_detail.html?orderID=${order.orderID}" class="btn btn-primary">Detail</a></td>
                </tr>
            `;
        });
        ordersHtml += '</tbody></table>';
        $('#ordersTable').html(ordersHtml);

        if (role === 'Support') {
            $('.status-select').off('change').on('change', function () {
                const orderID = $(this).data('order-id');
                const newStatus = $(this).val();
                updateOrderStatus(orderID, newStatus);
            });
        }
    }

    function updateOrderStatus(orderID, newStatus) {
        $.ajax({
            url: '../back-end/Order/update_order_status.php',
            type: 'POST',
            data: { orderID: orderID, status: newStatus },
            success: function (response) {
                if (response.status === 'success') {
                    alert('Order status updated successfully.');
                } else if (response.status === 'expired') {
                    alert('SessionID has expired. Please log in again.');
                    Logout();
                } else {
                    alert('Failed to update order status.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('An error occurred: ' + textStatus);
            }
        });
    }

    function renderPagination(totalPages, currentPage) {
        let paginationHtml = '';
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
        $('#pagination').html(paginationHtml);

        $('#pagination a').click(function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            loadOrders(page);
        });
    }

    loadOrders(1);
});