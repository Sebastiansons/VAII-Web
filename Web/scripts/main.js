function redirectToShop(id) {
    window.location.href = `pages/shop.html?categoryID=${id}`;
}

function GetCategories() {
    fetch('back-end/get_categories.php')
        .then(response => response.json())
        .then(data => {
            const categoriesDiv = document.getElementById('categories');
            categoriesDiv.innerHTML = '';
            data.forEach(category => {
                const categoryDiv = document.createElement('div');
                categoryDiv.classList.add('col-6', 'col-md-3', 'mb-4');
                categoryDiv.innerHTML = `
                <div class="card text-center category-card" onclick="redirectToShop(${category.CategoryID})">
                    <div class="card-body">
                        <i class="${category.Icon}" style="font-size: 2rem;"></i>
                        <h5 class="card-title mt-2">${category.Name}</h5>
                        <p class="card-text">${category.Description}</p>
                    </div>
                </div>`;
                categoriesDiv.appendChild(categoryDiv);
            });
        })
        .catch(error => console.error('Error fetching data:', error));
}

