document.addEventListener('DOMContentLoaded', function () {
    LoadFilterCategories();

    document.getElementById('searchButton').addEventListener('click', function () {
        LoadItems();
    });
});

function LoadFilterCategories() {
    $.ajax({
        url: `../back-end/Category/get_categories_filter.php`,
        type: 'GET',
        success: function (response) {
            if (response.status === 'success') {
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

                LoadItems();
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

function LoadItems(page = 1) {
    const categoryId = document.getElementById('category-select').value;
    const searchName = document.getElementById('search-name').value;
    const maxPrice = document.getElementById('max-price').value;

    fetch(`../back-end/Product/get_filtered_products.php?category_id=${categoryId}&search_name=${searchName}&max_price=${maxPrice}&page=${page}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const itemsDiv = document.getElementById('items');
            itemsDiv.innerHTML = '';

            if (data.role === 'Admin') {
                const addItemButtonDiv = document.createElement('div');
                addItemButtonDiv.classList.add('text-center', 'mb-3');
                const addItemButton = document.createElement('button');
                addItemButton.classList.add('btn', 'btn-primary');
                addItemButton.textContent = 'Add new item';
                addItemButton.style.width = 'auto';
                addItemButton.onclick = () => {
                    if (confirm('Are you sure you want to add new product?')) {
                        window.location.href = '../pages/edit_product.html?productID=0';
                    }
                };
                addItemButtonDiv.appendChild(addItemButton);
                itemsDiv.appendChild(addItemButtonDiv);
            }

            data.data.items.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('col-12', 'col-md-6', 'col-lg-4', 'mb-4');

                const firstImage = item.Image ? item.Image.split(',')[0] : 'default.jpg';
                const imagePath = firstImage.startsWith('../../') ? firstImage.substring(6) : firstImage;

                itemDiv.innerHTML = `
                <div class="card">
                    <div class="card-body d-flex">
                        <div class="flex-grow-1">
                            <h5 class="card-title">${item.Name}</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="card-text mb-0">$${item.Price}</p>
                            </div>
                            <p class="product-text card-text">${item.Description}</p>
                        </div>
                        <img src="../${imagePath}" class="card-img-right" alt="Product Image" style="width: 150px; height: auto; margin-left: 15px;">
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-3 mb-3">
                        ${data.role === 'Admin' ? `
                            <button class="btn btn-primary" onclick="EditProduct(${item.ItemID}); return false;">Edit</button>
                            <button class="btn btn-danger" onclick="DeleteItem(${item.ItemID})">Delete</button>
                            <button class="btn btn-success" onclick="DetailProduct(${item.ItemID}); return false;">Detail</button>
                        ` : `
                            <button class="btn btn-primary" onclick="DetailProduct(${item.ItemID}); return false;">Detail</button>
                            <button class="btn btn-success" onclick="AddToCart(${item.ItemID}); return false;">Buy</button>
                        `}
                    </div>
                </div>
            `;
                itemsDiv.appendChild(itemDiv);
            });

            const paginationDiv = document.getElementById('pagination');
            paginationDiv.innerHTML = '';

            const prevLink = document.createElement('a');
            prevLink.href = '#';
            prevLink.innerText = 'Previous';
            prevLink.onclick = (e) => {
                e.preventDefault();
                if (page > 1) LoadItems(page - 1);
            };
            paginationDiv.appendChild(prevLink);

            for (let i = 1; i <= data.data.total_pages; i++) {
                const pageLink = document.createElement('a');
                pageLink.href = '#';
                pageLink.innerText = i;
                pageLink.classList.add('page-link');
                if (i === data.data.current_page) {
                    pageLink.classList.add('active');
                }
                pageLink.onclick = (e) => {
                    e.preventDefault();
                    LoadItems(i);
                };
                paginationDiv.appendChild(pageLink);
            }

            const nextLink = document.createElement('a');
            nextLink.href = '#';
            nextLink.innerText = 'Next';
            nextLink.onclick = (e) => {
                e.preventDefault();
                if (page < data.data.total_pages) LoadItems(page + 1);
            };
            paginationDiv.appendChild(nextLink);
        })
        .catch(error => console.error('Error fetching items:', error));
}

function DeleteItem(itemID) {
    if (confirm('Are you sure you want to delete this product?')) {
        $.ajax({
            url: '../back-end/Product/delete_product.php',
            type: 'POST',
            data: JSON.stringify({ Id: itemID }),
            contentType: 'application/json',
            success: function (response) {
                if (response.status === 'success') {
                    alert(response.message);
                    UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                    LoadItems();
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
    }
}

function EditProduct(id) {
    if (confirm(`Are you sure you want to edit this product?`)) {
        window.location.href = `../pages/edit_product.html?productID=${id}`;
    }
}

function DetailProduct(id) {
    window.location.href = `../pages/product_detail.html?productID=${id}`;
}