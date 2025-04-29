<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conndb.php';

// Check if user is logged in
if (isset($_POST['order_now'])) {
    $response = array('success' => false, 'message' => '');
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $response['success'] = false;
        $response['message'] = "Please log in to place an order.";
        $response['redirect'] = "login";
    } else {
        $user_id = $_SESSION['user_id'];
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        
        // Additional validation
        if (empty($product_id) || empty($quantity) || $quantity < 1) {
            $response['success'] = false;
            $response['message'] = "Invalid product or quantity.";
        } else {
            // Process the order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Order placed successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = "Failed to place order: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    echo json_encode($response);
    exit();
}
?>

<script>
let currentQuantity = 1; // Initialize currentQuantity

function showOrderNowPopup(product) {
    currentProductId = product.id;

    document.getElementById('popupProductImg').src = product.image_url;
    document.getElementById('popupProductName').textContent = product.name;

    // Calculate and display the order price
    const productPrice = parseFloat(product.original_price); // Assuming you have the original price
    const deliveryFee = 400;
    const totalPrice = productPrice + deliveryFee;

    document.getElementById('popupOrderPrice').textContent = "LKR. " + totalPrice.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    document.getElementById('colorOptions').innerHTML = generateColorOptions(product.colors);
    document.getElementById('quantityValue').textContent = currentQuantity;
    document.getElementById('orderNowPopup').style.display = 'flex';
}

function closeOrderNowPopup() {
    document.getElementById('orderNowPopup').style.display = 'none';
    currentQuantity = 1; 
}

function confirmOrderDetails() {
    // Logic to confirm order details
    showNotification("Order details confirmed!");
}

function showPaymentDetails() {
    // Logic to show payment details
    showNotification("Please fill in your payment details.");
}

function orderNow(productId, quantity) {
    // Logic to handle the order now action
    showNotification("Order placed successfully!");
}

function changeQuantity(amount) {
    currentQuantity = Math.max(1, currentQuantity + amount); // Ensure it doesn't go below 1
    document.getElementById('quantityValue').textContent = currentQuantity; 
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
        setTimeout(() => notification
        .remove(), 300);
    }, 5000);
}

document.addEventListener('DOMContentLoaded', () => {
    // Any additional initialization code can go here
});
</script>