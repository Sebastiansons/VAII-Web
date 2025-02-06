document.addEventListener('DOMContentLoaded', function () {
    loadProductDetail();
});

function loadProductDetail() {
    const urlParams = new URLSearchParams(window.location.search);
    const productID = urlParams.get('productID');

    if (productID) {
        $.ajax({
            url: `../back-end/Product/get_product_detail.php?productID=${productID}`,
            type: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    if (response.sessionId != null) {
                        UpdateSession(response.sessionId, response.sessionIdExpirationDate);
                    }

                    const product = response.data;
                    const productDetailDiv = document.getElementById('productDetail');

                    const images = product.Image ? product.Image.split(',') : [];

                    let carouselIndicators = '';
                    let carouselInner = '';
                    images.forEach((image, index) => {
                        carouselIndicators += `
                                <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="${index}" ${index === 0 ? 'class="active"' : ''} aria-current="true" aria-label="Slide ${index + 1}"></button>
                            `;
                        carouselInner += `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                    <img src="Web/${image}" class="d-block w-100" alt="Product Image">
                                </div>
                            `;
                    });

                    productDetailDiv.innerHTML = `
                            <div class="col-12 col-md-6">
                                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-indicators">
                                        ${carouselIndicators}
                                    </div>
                                    <div class="carousel-inner">
                                        ${carouselInner}
                                    </div>
                                    <button id="car-prev" class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button id="car-next" class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 product-detail">
                                <h3>${product.Name}</h3>
                                <p>${product.Description}</p>
                                <h4>$${product.Price}</h4>
                                <div class="buy-button">
                                    <button id="buyButton" class="btn btn-success" style="display: none;" onclick="AddToCart(${product.ItemID})">Add to cart</button>
                                </div>
                            </div>
                        `;


                    if (response.role && response.role === 'Customer') {
                        document.getElementById('buyButton').style.display = 'block';
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
    } else {
        alert('Product ID is required.');
    }
}