function LoadFilterCategories() {
    return new Promise((resolve, reject) => {
        fetch('../back-end/get_categories_filter.php')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('category-select');
                data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.CategoryID;
                    option.textContent = category.Name;
                    select.appendChild(option);
                }); resolve();
            }).catch(error => { console.error('Error fetching categories:', error); reject(error); });
    });
}

function LoadItems(page = 1) {
    const categoryId = document.getElementById('category-select').value;
    const searchName = document.getElementById('search-name').value;
    const maxPrice = document.getElementById('max-price').value;

    fetch(`../back-end/get_filtered_products.php?category_id=${categoryId}&search_name=${searchName}&max_price=${maxPrice}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            const itemsDiv = document.getElementById('items');
            itemsDiv.innerHTML = '';
            data.items.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('col-12', 'col-md-6', 'col-lg-4', 'mb-4');
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
                            <img src="../images/products/${item.Image}" class="card-img-right" alt="Product Image" style="width: 150px; height: auto; margin-left: 15px;">
                        </div>
                        <div class="d-flex justify-content-center gap-2 mt-3 mb-3">
                            <button class="btn btn-success" onclick="productModification(${item.ItemID}); return false;">Edit</button>
                            <button class="btn btn-danger" onclick="deleteItem(${item.ItemID},${categoryId})">Delete</button>
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

            for (let i = 1; i <= data.total_pages; i++) {
                const pageLink = document.createElement('a');
                pageLink.href = '#';
                pageLink.innerText = i;
                pageLink.classList.add('page-link');
                if (i === data.current_page) {
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
                if (page < data.total_pages) LoadItems(page + 1);
            };
            paginationDiv.appendChild(nextLink);
        })
        .catch(error => console.error('Error fetching items:', error));
}

function DeleteItem(itemID, categoryID) {
    if (confirm('Are you sure you want to delete this item?')) {
        fetch('../back-end/delete_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ItemID: itemID })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Item deleted successfully');
                window.location.href = `../pages/shop.html?categoryID=${categoryID}`;
            }
        })
    }
}

function ProductModification(id) {
    window.location.href = `../pages/product_modif.html?productID=${id}`;
}