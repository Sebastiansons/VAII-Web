document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const productID = urlParams.get('productID');

    LoadFilterCategories().then(() => {
        if (productID && productID != 0) {
            LoadItem(productID);
        }
    });

    document.getElementById('editForm').addEventListener('submit', function (event) {
        event.preventDefault();
        SaveItem();
    });
});

function LoadItem(itemID) {
    fetch(`../back-end/get_product.php?ItemID=${itemID}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.ItemID) {
                document.getElementById('itemID').value = data.ItemID;
                document.getElementById('category-select').value = data.CategoryID;
                document.getElementById('name').value = data.Name;
                document.getElementById('description').value = data.Description;
                document.getElementById('price').value = data.Price;
            } else {
                alert('Item not found');
            }
        })
        .catch(error => console.error('Error fetching item:', error));
}

function SaveItem() {
    const itemID = document.getElementById('itemID').value;
    const categoryID = document.getElementById('category-select').value;
    const name = document.getElementById('name').value;
    const description = document.getElementById('description').value;
    const price = document.getElementById('price').value;
    fetch('../back-end/save_product.php', {
        method: 'POST', headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            ItemID: itemID,
            CategoryID: categoryID,
            Name: name,
            Description: description,
            Price: price
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Item saved successfully');
            window.location.href = `../index.html`;
        } else {
            alert('Failed to save item: ' + data.error);
        }
    }).catch(error => console.error('Error saving item:', error));
}