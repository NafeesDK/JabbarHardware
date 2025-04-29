// cart.js
if (typeof currentProductId === 'undefined') {
    let currentProductId = null;
    let currentQuantity = 1;

    function showProductPopup(product) {
        currentProductId = product.id;
        document.getElementById('popupProductImage').src = product.image_url;
        document.getElementById('popupProductName').textContent = product.name;
        document.getElementById('popupProductDescription').textContent = product.description;
        document.getElementById('popupOriginalPrice').textContent = "LKR. " + product.original_price;
        document.getElementById('popupDiscountedPrice').textContent = "LKR. " + product.discounted_price;
        document.getElementById('colorOptions').innerHTML = generateColorOptions(product.colors);
        document.getElementById('quantityValue').textContent = currentQuantity;
        document.getElementById('productPopup').style.display = 'flex';
    }   

    function closeProductPopup() {
        document.getElementById('productPopup').style.display = 'none';
        currentQuantity = 1;
    }

    function changeQuantity(amount) {
        currentQuantity = Math.max(1, currentQuantity + amount);
        document.getElementById('quantityValue').textContent = currentQuantity;
    }

    function generateColorOptions(colors) {
        if (!colors || colors.length === 0) {
            return '<span>N/A</span>';
        }
        return colors.map(color => `<div class="color-option" style="background-color: ${color};"></div>`).join('');
    }

    // New function to fetch cart count from database
    function updateCartCountFromDatabase() {
        fetch('getCartCount.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.count;
                        // Also update session storage to keep it in sync
                        sessionStorage.setItem('cartCount', data.count);
                    }
                }
            })
            .catch(error => {
                console.error('Error updating cart count:', error);
            });
    }

    function addToCart() {
        const productId = currentProductId;
        const quantity = currentQuantity;

        const isLoggedIn = document.body.getAttribute('data-logged-in') === 'true'; // Check login status

        if (!isLoggedIn) {
            showNotification("Please log in to add items to your cart.");
            return; // Exit the function if not logged in
        }

        const formData = new FormData();
        formData.append('add_to_cart', '1');
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        fetch('AddToCart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Instead of manually incrementing, get the actual count from database
                updateCartCountFromDatabase();
                showNotification(data.message);
            } else {
                showNotification(data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error); // Log the error for debugging
            showNotification("An error occurred. Please try again.");
        });

        closeProductPopup();
    }

    function showNotification(message) {
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => notification.classList.add('show'), 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // First try to get count from session storage
        let savedCount = sessionStorage.getItem('cartCount');
        if (savedCount) {
            document.querySelector('.cart-count').textContent = savedCount;
        } else {
            document.querySelector('.cart-count').textContent = '0';
        }
        
        // Then update from database to ensure accuracy
        updateCartCountFromDatabase();
    });
}

// Add this to the end of your cart.js file or in a script tag at the bottom of ttt.php
document.addEventListener('DOMContentLoaded', function() {
    // Apply fixed position class to cart container
    const cartContainer = document.querySelector('.cart-container');
    if (cartContainer) {
        cartContainer.classList.add('fixed-cart');
    }
    
    // Make sure the cart count is updated
    updateCartCountFromDatabase();
});