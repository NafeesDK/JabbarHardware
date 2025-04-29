<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conndb.php';

ini_set("log_errors", 1);
ini_set("error_log", "C:\\xampp\\htdocs\\ttt\\php-error.log");
 error_reporting(E_ALL);
 ini_set('display_errors', 1);

header('Content-Type: application/json');

$isAdded = true; 

$response = ['success' => $isAdded, 'message' => $isAdded ? 'Item added to cart successfully.' : 'Failed to add item to cart.'];

if (isset($_POST['add_to_cart'])) {
    $user_id = $_SESSION['user_id']; 
    $product_id = intval($_POST['product_id']); 
    $quantity = intval($_POST['quantity']); 


    if (!$user_id) {
        $response['message'] = "Please log in first.";
    } elseif (empty($product_id) || empty($quantity) || $quantity < 1) {
        $response['message'] = "Invalid product or quantity.";
    } else {
        $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update quantity for the existing product in the cart
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $quantity;

            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Cart updated successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = "Failed to update cart." . $stmt->error;
            }
        } else {
            // Insert new product into the cart
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Item added to cart successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = "Failed to add item to cart." . $stmt->error;
            }
        }
        $stmt->close();
    }

    echo json_encode($response);
    exit();
}
?>

<div class="popup-container" id="productPopup">
    <div class="popup">
        <span class="close-btn" onclick="closeProductPopup()">&times;</span>
        <div class="product-details">
            <div class="product-image-quantity">
                <img id="popupProductImage" src="" alt="Product Image" class="popup-image">
                <div class="quantity-selector">
                    <button onclick="changeQuantity(-1)">&#8722;</button>
                    <span id="quantityValue">1</span>
                    <button onclick="changeQuantity(1)">&#43;</button>
                </div>
                <div class="vertical-line1"></div>
                <div class="color-options-container">
                    <span class="color-family-label">Color Family:</span>
                    <div class="color-options" id="colorOptions"></div>
                </div>
                </div>
                <div class="price-container">
                    <span class="original-price" id="popupOriginalPrice"></span>
                    <span class="discounted-price" id="popupDiscountedPrice"></span>
                </div>
                <h3 id="popupProductName"></h3>
                <p id="popupProductDescription"></p>
            <div class="button-container">
                <button class="add-to-cart-btn" onclick="addToCart(currentProductId, currentQuantity)">Add to Cart</button>
            </div>
        </div>
    </div>
</div>


<style>
.popup-container {
    display: none; 
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8); 
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.popup {
    background-color: rgba(0, 0, 0, 0.85); 
    padding: 15px; 
    border-radius: 10px;
    width: 400px; 
    height: auto; 
    max-width: 90%; 
    position: relative;
    color: #009688; 
    overflow: hidden; 
    text-align: left;
}

.popup-image {
    width: 100px; 
    height: auto; 
    margin-right: 10px; 
}

.product-image-quantity {
    display: flex; 
    align-items: center; 
}

.quantity-selector {
    display: flex;
    align-items: center;
    margin-left: 10px; 
}

.quantity-selector button {
    width: 30px;
    height: 30px;
    font-size: 20px;
}

.quantity-selector span {
    width: 40px; 
    text-align: center; 
}

.vertical-line1 {
    width: 2px; 
    background-color: #ccc; 
    height: 60px; 
    margin: 0 10px; 
}

.color-options-container {
    display: flex;
    flex-direction: column; 
    align-items: flex-start; 
}

.color-family-label {
    margin-bottom: 5px; 
    font-weight: bold; 
}

.color-options {
    display: flex;
    margin-left: 0; 
}

.color-option {
    width: 20px;
    height: 20px;
    border: 1px solid #fff; 
    margin-right: 5px;
}

.price-container {
    margin-top: 10px; 
    text-align: left; 
    margin-bottom: 10px;
}

.original-price {
    text-decoration: line-through; 
    color: #ff0000; 
}

.discounted-price {
    color: #00ff00; 
    font-size: 1.2em; 
}

#popupOriginalPrice {
    color: #ff0000;
    text-decoration: line-through;
}

#popupOriginalPrice.no-discount {
    text-decoration: none;
    color: #fff;
}

#popupDiscountedPrice {
    color: #00ff00;
    font-size: 1.2em;
}

.button-container {
    display: flex; 
    justify-content: center; 
    margin-top: 10px; 
}

.add-to-cart-btn {
    background-color: rgba(0, 150, 136, 0.8); 
    color: white; 
    border: none; 
    padding: 10px 20px; 
    border-radius: 25px; 
    cursor: pointer; 
    font-size: 16px; 
    transition: background-color 0.3s ease; 
    display: block; 
    margin: 10px auto; 
}

.add-to-cart-btn:hover {
    background-color: rgba(0, 121, 107, 0.8); 
}

#popupProductName {
    margin: 0;
    text-align: left;
}

#popupProductDescription {
    margin: 0;
    text-align: left;
}
</style>

<!-- Add to Cart Popup JavaScript -->
<script>

function showProductPopup(product) {
    currentProductId = product.id;

    document.getElementById('popupProductImage').src = product.image_url;
    document.getElementById('popupProductName').textContent = product.name;
    document.getElementById('popupProductDescription').textContent = product.description;
    const originalPriceElem = document.getElementById('popupOriginalPrice');
    const discountedPriceElem = document.getElementById('popupDiscountedPrice');

    originalPriceElem.textContent = "LKR. " + product.original_price;

    if (!product.discounted_price || product.discounted_price === "0.00" || product.discounted_price === "NULL") {
        originalPriceElem.classList.add('no-discount');
        discountedPriceElem.style.display = "none";
    } else {
        originalPriceElem.classList.remove('no-discount');
        discountedPriceElem.textContent = "LKR. " + product.discounted_price;
        discountedPriceElem.style.display = "inline";
    }
    document.getElementById('colorOptions').innerHTML = generateColorOptions(product.colors);
    document.getElementById('quantityValue').textContent = currentQuantity;
    document.getElementById('productPopup').style.display = 'flex';
}

function closeProductPopup() {
    document.getElementById('productPopup').style.display = 'none';
    currentQuantity = 1; 
}
let currentQuantity = 1; // Initialize currentQuantity

function changeQuantity(amount) {
    currentQuantity = Math.max(1, currentQuantity + amount); // Ensure it doesn't go below 1
    document.getElementById('quantityValue').textContent = currentQuantity; // Update the displayed quantity
}

function generateColorOptions(colors) {
    if (!colors || colors.length === 0) {
        return '<span>N/A</span>';
    }
    return colors.map(color => `<div class="color-option" style="background-color: ${color};"></div>`).join('');
}

function showNotification(message) {
    // Remove any existing notification
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Create new notification
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);

    // Remove notification after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function addToCart() {
    const productId = currentProductId;
    const quantity = currentQuantity;

    const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>; // Pass PHP variable to JavaScript

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
    .then(response => {
        console.log("Response Status:", response.status);
        // Check if the response is OK (status in the range 200-299)
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json(); // Parse the JSON response
    })
    .then(data => {
        console.log("Response Data:", data);
        // Check if data is defined and has the success property
        if (data && data.success) {
            // Increment the cart count
            let cartCountElement = document.querySelector('.cart-count');
            let currentCount = parseInt(cartCountElement.textContent) || 0; 
            let newCount = currentCount + currentQuantity; 
            cartCountElement.textContent = newCount; 

            showNotification(data.message);
        } else {
            // Handle the case where data is undefined or success is false
            showNotification(data ? data.message : "An unexpected error occurred.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification("An error occurred. Please try again.");
    });

    closeProductPopup();
}


document.addEventListener('DOMContentLoaded', () => {
    if (savedCount) {
        document.querySelector('.cart-count').textContent = savedCount; // Set the cart count from session storage
    } else {
        document.querySelector('.cart-count').textContent = '0'; // Default to 0 if no count is saved
    }
});
</script>
