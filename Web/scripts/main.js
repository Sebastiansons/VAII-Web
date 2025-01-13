$(document).ready(function () {
    GetCategories();

    function GetCategories() {
        $.ajax({
            url: 'back-end/get_categories.php',
            type: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    const categoriesDiv = document.getElementById('categories');
                    categoriesDiv.innerHTML = '';

                    if (response.role === 'Admin') {
                        const addCategoryButtonDiv = document.createElement('div');
                        addCategoryButtonDiv.classList.add('col-12', 'text-center', 'mb-4');
                        const addCategoryButton = document.createElement('button');
                        addCategoryButton.textContent = 'Add new category';
                        addCategoryButton.classList.add('btn', 'btn-primary');
                        addCategoryButton.style.width = 'auto';
                        addCategoryButton.onclick = () => {
                            if (confirm('Do you really want to create a new category?')) {
                                window.location.href = 'pages/edit_category.html?categoryID=0';
                            }
                        };
                        addCategoryButtonDiv.appendChild(addCategoryButton);
                        categoriesDiv.appendChild(addCategoryButtonDiv);
                    }

                    response.categories.forEach(category => {
                        const categoryDiv = document.createElement('div');
                        categoryDiv.classList.add('col-6', 'col-md-3', 'mb-4');
                        categoryDiv.innerHTML = `
                            <div class="card text-center category-card">
                                <div class="card-body" onclick="redirectToShop(${category.CategoryID})">
                                    <i class="${category.Icon}" style="font-size: 2rem;"></i>
                                    <h5 class="card-title mt-2">${category.Name}</h5>
                                    <p class="card-text">${category.Description}</p>
                                </div>
                                ${response.role === 'Admin' ? `
                                <div class="card-footer">
                                    <button class="btn btn-success mr-2" onclick="editCategory(event, '${category.Name}', ${category.CategoryID})">Edit</button>
                                    <button class="btn btn-danger" onclick="deleteCategory(event, '${category.Name}', ${category.CategoryID})">Delete</button>
                                </div>
                                ` : ''}
                            </div>
                        `;
                        categoriesDiv.appendChild(categoryDiv);
                    });

                    if (response.sessionId != null) {
                         UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                    }
                } else if (response.status === 'expired') {
                    alert('SessionID has expired. Please log in again.');
                    Logout();
                } else {
                    alert('Failed to load categories.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('An error occurred: ' + textStatus);
            }
        });
    }

    window.redirectToShop = function (id) {
        window.location.href = `pages/shop.html?categoryID=${id}`;
    };

    window.editCategory = function (event, name, id) {
        event.stopPropagation();
        if (confirm(`Do u really want to edit category ${name}?`)) {     
            window.location.href = `pages/edit_category.html?categoryID=${id}`;
        }
    };

    window.deleteCategory = function (event, name, id) {
        event.stopPropagation();
        if (confirm(`Are you sure you want to delete category ${name}?`)) {
            $.ajax({
                url: 'back-end/Category/delete_category.php',
                type: 'POST',
                data: JSON.stringify({ Id: id }),
                contentType: 'application/json',
                success: function (response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                        GetCategories();
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
    };
});
