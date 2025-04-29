<?php
session_start();

require_once 'conndb.php';

$timeout_duration = 900; 


if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        
        session_destroy();
        header("Location: ttt.php"); 
        exit();
    }
    $_SESSION['LAST_ACTIVITY'] = time();
}

$isLoggedIn = isset($_SESSION['user_id']);
$userFullname = $isLoggedIn ? $_SESSION['fullname'] : '';

if (isset($_POST['add_to_cart'])) {
    include 'AddToCart.php'; 
    exit(); 
}

$offer_sql = "SELECT * FROM products WHERE category = 'offer'";
$offer_result = $conn->query($offer_sql);


$regular_sql = "SELECT * FROM products WHERE category = 'regular'";
$regular_result = $conn->query($regular_sql);


function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


if (isset($_POST['register'])) {
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $response = array('success' => false, 'message' => '');

    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $response['message'] = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $response['message'] = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $response['message'] = "Password must be at least 6 characters long";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['message'] = "Email already registered";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Registration successful! Please login.";
            } else {
                $response['message'] = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jabbar Hardware Stores</title>

    
    <link rel="stylesheet" href="Home.css"> 
    <link rel="stylesheet" href="products.css"> 
    <link rel="stylesheet" href="last.css"> 
    <link rel="stylesheet" href="ordernow.css"> 
    <link rel="stylesheet" href="OrderDetails.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script>
    let currentProductId = null;

    function showProductPopup(product) {
        currentProductId = product.id;
    }

</script>
</head>
<style>
    .fixed-cart {
        position: fixed !important;
        right: 0 !important; 
        top: 50% !important;
        transform: translateY(-50%) !important;
        z-index: 9999 !important;
        transition: all 0.3s ease !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Make sure the cart button maintains its current style */
    .fixed-cart .cart-btn {
        border-radius: 30px 0 0 30px !important;
        width: 50px !important;
        height: 60px !important;
    }

    body {
        overflow-x: hidden !important; /* Prevent horizontal scrollbar */
    }
    body {
    margin: 0;
    height: 90vh;
    overflow-y: scroll;
    scroll-snap-type: y mandatory;
    background-image: url('logoo.jpg'); 
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat; 
    background-attachment: fixed;
}

.page {
    scroll-snap-align: start; 
    height: 100vh; 
}

#page3, #page2, #page1 {
    background-color: rgba(0, 0, 0, 0.80);
    height: 100vh; 
}

.cart-container {
    width: auto;
    margin: 0;
    padding: 0;
    position: fixed;
    right: 0; 
    top: 50%;
    transform: translateY(-50%);
    z-index: 1000;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4));
    transition: all 0.3s ease;
}

.cart-container:hover {
    filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.5));
}

.fixed-cart {
    position: fixed !important;
    right: 0 !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    z-index: 9999 !important;
    transition: all 0.3s ease !important;
    margin: 0 !important;
    padding: 0 !important;
}

.cart-btn {
    background: linear-gradient(135deg, rgba(0, 150, 136, 0.95), rgba(0, 121, 107, 0.95));
    border: none;
    border-radius: 30px 0 0 30px; 
    width: 60px;
    height: 70px;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.1);
}

.cart-btn:hover {
    background: linear-gradient(135deg, rgba(0, 150, 136, 1), rgba(0, 121, 107, 1));
    width: 65px;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
}

.cart-btn:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.cart-btn:hover:before {
    opacity: 1;
}

.cart-icon {
    font-size: 28px;
    color: white;
    font-style: normal;
    margin-left: -5px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
}

.cart-btn:hover .cart-icon {
    transform: scale(1.1);
}

.cart-count {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: #ff5252;
    color: white;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    border: 2px solid rgba(30, 30, 30, 0.95);
    transition: all 0.3s ease;
}

.cart-btn:hover .cart-count {
    transform: scale(1.1);
    box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
}

/* Cart Items Container (when expanded) */
#cartItems {
    display: none;
    position: fixed;
    right: 70px;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(30, 30, 30, 0.95);
    border-radius: 15px 0 0 15px;
    padding: 20px;
    max-height: 80vh;
    overflow-y: auto;
    width: 350px;
    border: 1px solid rgba(0, 150, 136, 0.3);
    box-shadow: 0 5px 30px rgba(0, 0, 0, 0.4);
    z-index: 999;
    animation: slide-in 0.3s ease-out;
    backdrop-filter: blur(5px);
}

@keyframes slide-in {
    from { transform: translate(100%, -50%); opacity: 0; }
    to { transform: translate(0, -50%); opacity: 1; }
}

/* Scrollbar styling for cart items */
#cartItems::-webkit-scrollbar {
    width: 8px;
}

#cartItems::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
}

#cartItems::-webkit-scrollbar-thumb {
    background: rgba(0, 150, 136, 0.7);
    border-radius: 10px;
}

#cartItems::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 150, 136, 0.9);
}

/* Cart item styling */
#cartItems .card {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 10px;
    background-color: rgba(20, 20, 20, 0.7);
    border: 1px solid rgba(0, 150, 136, 0.2);
    transition: all 0.3s ease;
}

#cartItems .card:hover {
    background-color: rgba(30, 30, 30, 0.9);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Empty cart message */
.empty-cart-message {
    color: #e0e0e0;
    text-align: center;
    padding: 30px 0;
    font-style: italic;
}

/* Media queries for responsiveness */
@media (max-width: 768px) {
    .notification {
        top: auto;
        bottom: 20px;
        left: 20px;
        right: 20px;
        max-width: none;
        text-align: center;
    }
    
    #cartItems {
        width: calc(100% - 90px);
        right: 60px;
    }
}


#signupEmailCodeContainer {
    display: flex;
    align-items: center;
    justify-content: flex-start; /* Adjust alignment if needed */
}

#signupEmailCode {
    display: inline-block !important;
    visibility: visible !important;
    transition: width 0.3s ease, visibility 0.3s ease;
    opacity: 1 !important;
    width: 100px !important; /* Ensure it's big enough */
    height: 40px !important;
    background-color: transparent;
}

.popup-container {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.85);
        justify-content: center;
        align-items: center;
        z-index: 1000;
        backdrop-filter: blur(3px);
    }

    .popup {
        background-color: rgba(30, 30, 30, 0.95);
        color: #009688;
        padding: 35px;
        border-radius: 15px;
        width: 90%;
        max-width: 400px;
        position: relative;
        border: 1px solid rgba(0, 150, 136, 0.3);
        box-shadow: 0 5px 30px rgba(0, 150, 136, 0.2);
        animation: popup-fade 0.3s ease-in-out;
    }

    @keyframes popup-fade {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    .popup h2 {
        color: #009688;
        margin-bottom: 25px;
        font-size: 1.8rem;
        text-align: center;
    }

    .option-btn {
        background-color: #009688cc;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 25px;
        cursor: pointer;
        margin: 10px 0;
        width: 100%;
        transition: background-color 0.3s;
    }

    .option-btn:hover {
        background-color: #00796bcc;
    }

    .close-btn {
        position: absolute;
        right: 15px;
        top: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #ff6b6b;
        transition: transform 0.3s ease;
    }

    .close-btn:hover {
        transform: rotate(90deg);
        color: #ff4f4f;
    }

    .form-group {
        margin-bottom: 22px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #e0e0e0;
        font-weight: 500;
        text-align: left;
        font-size: 0.95rem;
    }

    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid rgba(0, 150, 136, 0.3);
        border-radius: 25px;
        box-sizing: border-box;
        background-color: rgba(20, 20, 20, 0.8);
        color: white;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .form-group input:focus {
        outline: none;
        border-color: #009688;
        box-shadow: 0 0 0 3px rgba(0, 150, 136, 0.2);
    }

    /* Style for the placeholder text */
    .form-group input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    /* Style for autofill background */
    .form-group input:-webkit-autofill,
    .form-group input:-webkit-autofill:hover,
    .form-group input:-webkit-autofill:focus {
        -webkit-text-fill-color: white;
        -webkit-box-shadow: 0 0 0px 1000px rgba(20, 20, 20, 0.8) inset;
        transition: background-color 5000s ease-in-out 0s;
    }

    .error-message {
        color: #ff6b6b;
        font-size: 0.85rem;
        margin-top: 6px;
        display: none;
        text-align: left;
        padding-left: 15px;
    }

    .submit-btn {
        position: relative;
        padding: 12px 20px;
        border: 2px solid #009688;
        cursor: pointer;
        background-color: transparent;
        color: white;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 25px;
        width: 100%;
        overflow: hidden;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .cover {
        background: #009688;
        height: 100%;
        width: 0%;
        border-radius: 25px;
        position: absolute;
        left: 0;
        bottom: 0;
        z-index: -1;
        transition: width 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .submit-btn:hover .cover {
        width: 100%;
    }

    .submit-btn:hover {
        color: white;
        box-shadow: 0 5px 15px rgba(0, 121, 107, 0.3);
        transform: translateY(-2px);
    }

    .switch-form {
        margin-top: 25px;
        text-align: center;
        color: #e0e0e0;
        font-size: 0.95rem;
    }

    .switch-form span {
        color: #009688;
        cursor: pointer;
        transition: all 0.3s ease;
        padding-bottom: 2px;
        border-bottom: 1px dashed transparent;
    }

    .switch-form span:hover {
        color: #00b5a3;
        border-bottom-color: #00b5a3;
    }

    /* For mobile responsiveness */
    @media (max-width: 500px) {
        .popup {
            padding: 25px 20px;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group input {
            padding: 10px 15px;
        }
        
        .popup h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
    }
    .notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: rgba(0, 150, 136, 0.95);
    color: white;
    padding: 15px 25px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.25);
    z-index: 2000;
    transform: translateX(150%);
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(255, 255, 255, 0.1);
    font-weight: 500;
    backdrop-filter: blur(5px);
    animation: notification-glow 2s infinite alternate;
    max-width: 350px;
    word-wrap: break-word;
}

@keyframes notification-glow {
    from { box-shadow: 0 5px 20px rgba(0, 150, 136, 0.2); }
    to { box-shadow: 0 5px 20px rgba(0, 150, 136, 0.4); }
}

.notification.show {
    transform: translateX(0);
}

.welcome-container {
    position: absolute;
    left: 30px;  
    top: 120px;  
    text-align: left;
}

.welcome-msg {
    color: #009688;
    font-size: 1.8rem;
    margin: 0;
    text-shadow: 4px 2px 4px rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.8s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .welcome-msg {
        font-size: 1.4rem;
    }
}

/* Add these styles to your existing CSS */
.email-container {
    display: flex;
    align-items: center;
}

.email-input {
    width: 100% !important;
    padding: 8px;
    border: 2px solid #333;
    border-radius: 4px;
    box-sizing: border-box;
    background-color: rgba(0, 0, 0, 0.85);
    color: white;
    transition: border-color 0.3s ease, width 0.3s ease;
}

.code-container {
    display: none; 
    margin-left: 5px; 
}

.code-input {
    width: 30% !important;
    padding: 8px;
    border: 2px solid #333;
    border-radius: 4px;
    box-sizing: border-box;
    background-color: rgba(0, 0, 0, 0.85);
    color: white;
    transition: border-color 0.3s ease;
}

.email-input:focus, .code-input:focus {
    outline: none;
    border-color: #009688;
}

.email-input.shrink {
    width: 70% !important;
}

/*                  For making users to get domain suggestions  might need in future
.domain-part {
    display: none;
    background-color: rgba(0, 0, 0, 0.85);
    color: #009688;
    padding: 8px;
    border: 2px solid #333;
    border-radius: 4px;
    font-weight: bold;
}

.domain-part.show {
    display: block;
}*/
.confirm-order-details {
    position: relative;
}

.delivery-details-notification {
    position: absolute;
    background-color: #009688;
    border: 1px solid rgb(3, 112, 101);
    padding: 10px;
    color: white;
    display: none;
    z-index: 1;
    bottom: 100%; 
    left: 32%; 
    transform: translateX(-50%);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    margin-bottom: 10px; 
}

.delivery-details-notification::before {
    content: "";
    position: absolute;
    bottom: -20px; 
    left: 50%;
    margin-left: -10px;
    border-width: 10px;
    border-style: solid;
    border-color: #009688 transparent transparent transparent; 
}

.payment-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
    justify-content: center;
    align-items: center;
}

.payment-modal-content {
    position: relative;
    width: 90%;
    max-width: 400px;
    margin: auto;
    border-radius: 10px;
}

.close-payment {
    position: absolute;
    right: 15px;
    top: 10px;
    color: white;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1001;
}
</style>
<body data-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>" data-user-id="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
        
    <div class="cart-container fixed-cart">
        <button class="cart-btn" onclick="window.location.href='Vcart.html';">
            <i class="cart-icon">🛒</i>
            <span class="cart-count">0</span>
        </button>
    </div>
        
    <div class="page" id="page1">
        <header>
            <div class="top-right">
                <button class="signup-btn" type="button" aria-label="Sign Up">
                    <span class="cover"></span>SIGN UP
                </button>
                <button class="login-btn" type="button" aria-label="Log In">
                    <span class="cover"></span>LOG IN
                </button>
            </div>
            <div class="logo-container">
                <img src="logoo.jpg" alt="Logo" class="logo-image">
            </div>
            <div class="header-center">
                <h1>Jabbar Hardware Stores</h1>
                <input type="text" placeholder="Search..." class="search-bar">
            </div>
            <div class="header-right">
                <button class="hamburger" onclick="toggleMenu()" aria-label="Menu">&#9776;</button>
            </div>
        </header>
        <main>
            <?php if ($isLoggedIn): ?>
                <?php
                $hour = date('H');
                if ($hour < 12) {
                    $greeting = "Good Morning";
                } elseif ($hour < 18) {
                    $greeting = "Good Afternoon";
                } else {
                    $greeting = "Hi"; 
                }
                ?>
                <div class="welcome-container">
                    <h3 class="welcome-msg"><?php echo $greeting . ", " . htmlspecialchars($userFullname) . "!"; ?></h3>
                </div>
            <?php endif; ?>
            <div class="center-content">
                <h2>From Blueprint to Build,<br>We've got you covered.</h2>
                <button class="shop-now-btn" onclick="shopNow()">
                    <span class="cover"></span>
                    Shop Now
                </button>
            </div>
        </main>
        <nav class="menu" id="menu">
            <ul>
                <li><a href="#home" onclick="scrollToPage('page1')">Home</a></li>
                <li><a href="#product" onclick="scrollToPage('page2')">Product</a></li>
                <li><a href="#about" onclick="scrollToPage('page3')">About Us</a></li>
                <li><a href="#contact" onclick="scrollToPage('page3')">Contact Us</a></li>
                <li><a href="#track">Track Order</a></li>
                <li><a href="#history">Order History</a></li>
            </ul>
        </nav>
    </div>

    
    <div class="page" id="page2">
        <main>
            
            <section class="menu bright-bg">
                <button class="pre-btn"><img src="arrow.png" alt=""></button>
                <div class="menu-grid" id="offer-grid">
                    
                    <?php while($offer = $offer_result->fetch_assoc()): ?>
                    <div class="card offer">
                        <div class="imgBx">
                            <img src="<?php echo htmlspecialchars($offer['image_url']); ?>" alt="<?php echo htmlspecialchars($offer['name']); ?>">
                        </div>
                        <div class="content">
                            <div class="contentBx">
                                <h3><?php echo htmlspecialchars($offer['name']); ?><br></h3>
                                    <p><span><?php echo htmlspecialchars($offer['description']); ?></span></p>
                                    <div class="price-container">
                                        <?php if (!is_null($offer['discounted_price'])): ?>
                                            <span class="original-price"><strike>LKR. <?php echo number_format($offer['original_price'], 2); ?></strike></span>
                                            <span class="discounted-price">LKR. <?php echo number_format($offer['discounted_price'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="final-price">LKR. <?php echo number_format($offer['original_price'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <div class="button-container">
                                    <button class="card-btn add-to-cart" onclick="showProductPopup({
                                        id: '<?php echo htmlspecialchars($offer['id']); ?>',
                                        image_url: '<?php echo htmlspecialchars($offer['image_url']); ?>',
                                        description: '<?php echo htmlspecialchars($offer['description']); ?>',
                                        name: '<?php echo htmlspecialchars($offer['name']); ?>',
                                        original_price: '<?php echo number_format($offer['original_price'], 2); ?>',
                                        discounted_price: '<?php echo number_format($offer['discounted_price'], 2); ?>'
                                    })">Add to Cart</button>
                                    
                                    <button class="card-btn order-now" onclick="orderNowPopup({
                                        id: '<?php echo htmlspecialchars($offer['id']); ?>',
                                        image_url: '<?php echo htmlspecialchars($offer['image_url']); ?>',
                                        name: '<?php echo htmlspecialchars($offer['name']); ?>',
                                        original_price: '<?php echo number_format($offer['discounted_price'], 2); ?>',
                                        colors: <?php echo json_encode($offer['colors'] ?? []); ?>
                                    })">Order Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <button class="nxt-btn"><img src="arrow.png" alt=""></button>
            </section>
    
            
            <section class="menu dark-bg">
                <button class="pre-btn"><img src="arrow.png" alt=""></button>
                <div class="menu-grid" id="product-grid">
                    
                    <?php while($regular = $regular_result->fetch_assoc()): ?>
                    <div class="card regular">
                        <div class="imgBx">
                            <img src="<?php echo htmlspecialchars($regular['image_url']); ?>" alt="<?php echo htmlspecialchars($regular['name']); ?>">
                        </div>
                        <div class="content">
                            <div class="contentBx">
                                <h3><?php echo htmlspecialchars($regular['name']); ?><br></h3>
                                    <p><span><?php echo htmlspecialchars($regular['description']); ?></span></p>
                                <div class="price-container">
                                    <span class="original-price">LKR. <?php echo number_format($regular['original_price'], 2); ?></span>
                                </div>
                                <div class="button-container">
                                    <button class="card-btn add-to-cart" onclick="showProductPopup({
                                        id: '<?php echo htmlspecialchars($regular['id']); ?>',
                                        image_url: '<?php echo htmlspecialchars($regular['image_url']); ?>',
                                        description: '<?php echo htmlspecialchars($regular['description']); ?>',
                                        name: '<?php echo htmlspecialchars($regular['name']); ?>',
                                        original_price: '<?php echo number_format($regular['original_price'], 2); ?>'
                                    })">Add to Cart</button>
                                    <button class="card-btn order-now" onclick="orderNowPopup({
                                        id: '<?php echo htmlspecialchars($regular['id']); ?>',
                                        image_url: '<?php echo htmlspecialchars($regular['image_url']); ?>',
                                        name: '<?php echo htmlspecialchars($regular['name']); ?>',
                                        original_price: '<?php echo number_format($regular['original_price'], 2); ?>'
                                    })">Order Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <button class="nxt-btn"><img src="arrow.png" alt=""></button>
            </section>
        </main>
    </div>

    
    <div class="page" id="page3">
        <main>
            <section id="about">
                <h2>About Us</h2>
                <div class="about-section">
                    <div class="shop-info">
                        <h3>Shop Info</h3>
                        <p>Welcome to Jabbar Hardware Stores! We have been serving the community since 2025. Our mission is to provide quality hardware products and exceptional customer service.</p>
                    </div>
                    <div class="vertical-line"></div>
                    <div class="team-members">
                        <h3>Team Members</h3>
                        <div class="team-member">
                            <img src="1.jpg" alt="Team Member 1" class="team-photo">
                            <p>John Doe - Store Manager</p>
                        </div>
                        <div class="team-member">
                            <img src="2.jpg" alt="Team Member 2" class="team-photo">
                            <p>Jane Smith - Sales Associate</p>
                        </div>
                    </div>
                </div>
                <hr class="horizontal-line">
            </section>
            
            <section id="contact">
                <h2>Contact Us</h2>
                <div class="contact-section">
                    <form class="feedback-form">
                        <div class="input-container">
                            <input type="text" id="name" maxlength="50" required>
                            <label for="name">Name</label>
                            <span class="error-message" id="name-error"></span>
                        </div>
                    
                        <div class="input-container">
                            <input type="email" id="email" maxlength="50" required>
                            <label for="email">Email</label>
                            <span class="error-message" id="email-error"></span>
                        </div>
                    
                        <div class="input-container">
                            <input type="text" id="subject" maxlength="100" required>
                            <label for="subject">Subject</label>
                            <span class="error-message" id="subject-error"></span>
                        </div>
                        <div class="input-container">
                            <textarea id="message" maxlength="500" required></textarea>
                            <label for="message">Message</label>
                            <div id="charCount">0/500</div>
                        </div>
                        <button type="submit" class="submit-btn">
                            <span class="cover"></span>
                            Submit
                        </button>
                    </form>
                    <div class="vertical-line"></div>
                    <div class="contact-details">
                        <p>Phone: +94 12345678</p>
                        <p>Email: <a href="#">JabbarHardware@gmail.com</a></p>
                        <p>Address: 123, Somewhere, Somewhere</p>
                        <p>Business Hours: Open from 9:00 AM to 6:00 PM</p>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <section class="social-media">
            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
        </section>
        <p class="Copyrights">Copyrights reserved by Jabbar Stores.</p>
        <p class="designed">Webpage designed by <a href="#">Nafees@gmail.com.</a></p>
    </footer>

    <script>
    function shopNow() {
        document.getElementById('page2').scrollIntoView({ 
            behavior: 'smooth'
        });
    }
    </script>
    <script src="home.js"></script>
    <script src="product.js"></script>
    <script src="details.js"></script>
    <script src="cart.js"></script>
    <script>
        function showPopup(type) {
            const popup = document.getElementById(`${type}Popup`);
            if (popup) {
                popup.style.display = 'flex';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Add click listeners for the buttons
            document.querySelector('.login-btn').addEventListener('click', () => showPopup('login'));
            document.querySelector('.signup-btn').addEventListener('click', () => showPopup('signup'));
            
            // Add close functionality
            document.querySelectorAll('.close-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('loginPopup').style.display = 'none';
                    document.getElementById('signupPopup').style.display = 'none';
                });
            });
        });

        function switchForm(type) {
            document.getElementById('loginPopup').style.display = 'none';
            document.getElementById('signupPopup').style.display = 'none';
            document.getElementById(type + 'Popup').style.display = 'flex';
        }
    </script>

    <div class="popup-container" id="loginPopup">
        <div class="popup">
            <span class="close-btn">&times;</span>
            <h2>Login</h2>
            <form id="loginForm" method="post">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="email" required>
                    <div class="error-message" id="loginEmailError"></div>
                </div>
                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="password" required>
                    <div class="error-message" id="loginPasswordError"></div>
                </div>
                <button type="submit" class="submit-btn">
                    <span class="cover"></span>
                    Login
                </button>
            </form>
            <div class="switch-form" onclick="switchForm('signup')">
                Don't have an account? <span>Sign up</span>
            </div>
        </div>
    </div>

    <div class="popup-container" id="signupPopup">
        <div class="popup">
            <span class="close-btn">&times;</span>
            <h2>Sign Up</h2>
            <form id="signupForm" method="post">
                <div class="form-group">
                    <label for="signupName">Full Name</label>
                    <input type="text" id="signupName" name="fullname" required>
                    <div class="error-message" id="signupNameError"></div>
                </div>
                <div class="form-group">
                    <label for="signupEmail">Email</label>
                    <input type="text" id="signupEmail" name="email" class="email-input" required>
                    <div class="error-message" id="signupEmailError"></div>
                </div>
                <div class="form-group">
                    <label for="signupPassword">Password</label>
                    <input type="password" id="signupPassword" name="password" required>
                    <div class="error-message" id="signupPasswordError"></div>
                </div>
                <div class="form-group">
                    <label for="signupConfirmPassword">Confirm Password</label>
                    <input type="password" id="signupConfirmPassword" name="confirm_password" required>
                    <div class="error-message" id="signupConfirmPasswordError"></div>
                </div>
                <button type="submit" class="submit-btn">
                    <span class="cover"></span>
                    Sign Up
                </button>
            </form>
            <div class="switch-form" onclick="switchForm('login')">
                Already have an account? <span>Login</span>
            </div>
        </div>
    </div>

    <div class="popup-container" id="orderNowPopup">
        <div class="popup">
            <span class="close-btn" onclick="closeOrderNowPopup()">&times;</span>
            <div class="product-details">
                <div class="product-image-quantity">
                    <img id="popupProductImg" src="" alt="Product Image" class="popup-image">
                    <div class="quantity-selector">
                        <button onclick="changeQuantity(-1)">&#8722;</button>
                        <span id="quantityValue">1</span>
                        <button onclick="changeQuantity(1)">&#43;</button>
                    </div>
                    <div class="vertical-line1"></div>
                    <div class="color-options-container">
                        <span class="color-fam-label">Color Family:</span>
                        <div class="color-options" id="colorOptions"></div>
                    </div>
                </div>
                <h3 id="popupProductName"></h3>
                <div class="price-container">
                    <span class="delivery-fee" title="Delivery fee only applies for locations outside Sainthamaruthu - Maruthamunai">
                        LKR. 400 +
                    </span>
                    <!-- <span class="tooltip">Delivery fee only applies for locations outside Sainthamaruthu - Maruthamunai</span> -->
                    <span class="order-price" id="popupOrderPrice"></span>
                </div>
                <div class="confirm-order-details" onclick="confirmOrderDetails()" onmouseover="showDeliveryDetails()" onmouseout="hideDeliveryDetails()"> 
                    <span>Confirm </span>delivery details
                    <div class="delivery-details-notification" id="delivery-details-notification">
                    </div>
                </div>
                <div class="payment-details" onclick="showPaymentDetails()">Payment Details - <span>Please fill</span></div>
                <div class="button-container">
                    <button class="order-now-btn" onclick="placeOrder(currentProductId, currentQuantity)">Place Order</button>
                </div>
            </div>
        </div>
    </div>

    <div class="popup-container" id="confirmOrderDetailsPopup">
        <div class="popup">
            <span class="close-btn" onclick="closeConfirmOrderDetailsPopup()">&times;</span>
            <form id="confirmOrderDetailsForm">
                <div class="form-group">
                    <label for="fullName">Full Name:</label> 
                    <input type="text" id="fullName" name="fullName" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" required>
                </div>
                <div class="form-group">
                    <label for="phoneNo">Phone No:</label>
                    <input type="text" id="phoneNo" name="phoneNo" required>
                </div>
                <div class="button-container">
                    <button type="button" class="confirm-btn" onclick="confirmOrderDetailsFormSubmit()">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <div class="popup-container" id="paymentOptionsPopup">
        <div class="popup">
            <span class="close-btn" onclick="closePaymentOptionsPopup()">&times;</span>
            <h2>Select Payment Method</h2>
            <div class="button-container">
                <button class="option-btn" onclick="payWithCash()">Cash on Delivery</button>
                <button class="option-btn" onclick="payWithCard()">Pay via Card</button>
            </div>
        </div>
    </div>

    <div id="payment-popup" class="payment-modal">
        <div class="payment-modal-content">
        </div>
    </div>

    <script src="cities.js"></script>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('login', '1');

        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message);
                //window.location.reload(true);
                setTimeout(() => location.reload(), 2000);
            } else {
                document.getElementById('loginEmailError').textContent = data.message;
                document.getElementById('loginEmailError').style.display = 'block';
            }
        });
    });

    document.getElementById('signupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('register', '1');

        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Log the response to see what is returned
            if (data.success) {
                showNotification(data.message);
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                console.log('Error:', data.message); // Log the error message
                document.getElementById('signupEmailError').textContent = data.message;
                document.getElementById('signupEmailError').style.display = 'block';
            }
        });
    });
    </script>

    <script>
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
    </script>

    <script>
    function scrollToPage(pageId) {
        document.getElementById(pageId).scrollIntoView({ 
            behavior: 'smooth' 
        });
    }
    </script>

    <script>
    let menuOpen = false;

    function toggleMenu() {
        const menu = document.getElementById('menu');
        menu.style.display = menuOpen ? 'none' : 'block'; // Toggle menu visibility
        menuOpen = !menuOpen; // Update the state
    }

    document.addEventListener('click', function(event) {
        const menu = document.getElementById('menu');
        const hamburger = document.querySelector('.hamburger');

        // Check if the click was outside the menu and the hamburger button
        if (menuOpen && !menu.contains(event.target) && !hamburger.contains(event.target)) {
            menu.style.display = 'none'; // Hide the menu
            menuOpen = false; // Update the state
        }
    });
    </script>

    <script>
        function addToCart() {
            const productId = currentProductId;
            const quantity = currentQuantity;

            const formData = new FormData();
            formData.append('add_to_cart', '1');
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            fetch('AddToCart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log("Response Status:", response.status); // Log the response status
                return response.json(); // Parse the JSON response
            })
            .then(data => {
                console.log("Response Data:", data); // Log the parsed data
                console.log("Success Value:", data.success); // Log the success value
                if (data.success) {
                    showNotification(data.message); // Show success notification
                } else {
                    showNotification(data.message); // Show error notification
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification("An error occurred. Please try again"); // Show generic error notification
            });
        }
    </script>
    <script>
        function orderNowPopup(product) {
            currentProductId = product.id;
            document.getElementById('popupProductImg').src = product.image_url;
            document.getElementById('popupProductName').textContent = product.name;

            // Calculate and display the order price
            const productPrice = parseFloat(product.original_price.replace(/,/g, ''));
            const deliveryFee = 400;
            const totalPrice = productPrice + deliveryFee;

            // Update the price display
            document.getElementById('popupOrderPrice').textContent =
                "LKR. " + productPrice.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + 
                "  = LKR. " + totalPrice.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // Generate color options (if any)
            document.getElementById('colorOptions').innerHTML = generateColorOptions(product.colors);

            // Set default quantity in the popup
            document.getElementById('quantityValue').textContent = 1; // Default quantity

            // Show the popup
            document.getElementById('orderNowPopup').style.display = 'flex';
        }
                
        function placeOrder(productId, quantity) {
            // Check if user is logged in
            const isLoggedIn = document.body.getAttribute('data-logged-in') === 'true';
            
            if (!isLoggedIn) {
                showNotification("Please log in to place an order.");
                // Show login popup
                document.getElementById('loginPopup').style.display = 'flex';
                closeOrderNowPopup();
                return;
            }
            
            const formData = new FormData();
            formData.append('order_now', '1');
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            
            fetch('orderNow.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message);
                    closeOrderNowPopup(); // Close the popup on success
                } else {
                    if (data.redirect === "login") {
                        showNotification(data.message);
                        document.getElementById('loginPopup').style.display = 'flex';
                        closeOrderNowPopup();
                    } else {
                        showNotification(data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification("An error occurred. Please try again.");
            });
        }
        function closeOrderNowPopup() {
            document.getElementById('orderNowPopup').style.display = 'none';
            currentQuantity = 1; 
        }

        function changeQuantity(amount) {
            currentQuantity = Math.max(1, currentQuantity + amount); // Ensure it doesn't go below 1
            document.getElementById('quantityValue').textContent = currentQuantity;
            
            // Update price when quantity changes
            const productId = currentProductId;
            updateOrderPrice(productId, currentQuantity);
        }

        function updateOrderPrice(productId, quantity) {
            // Get the original product price from the page
            const productCards = document.querySelectorAll('.card');
            let productPrice = 0;
            
            productCards.forEach(card => {
                const addToCartBtn = card.querySelector('.add-to-cart');
                if (addToCartBtn && addToCartBtn.onclick.toString().includes(productId)) {
                    const priceElement = card.querySelector('.price-container .original-price') || 
                                    card.querySelector('.price-container .discounted-price') ||
                                    card.querySelector('.price-container .final-price');
                    if (priceElement) {
                        const priceText = priceElement.textContent.replace('LKR. ', '').replace(',', '');
                        productPrice = parseFloat(priceText);
                    }
                }
            });
            
            const deliveryFee = 400;
            const totalPrice = (productPrice * quantity) + deliveryFee;
            
            if (!isNaN(totalPrice)) {
                document.getElementById('popupOrderPrice').textContent = "LKR. " + totalPrice.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }

        function generateColorOptions(colors) {
            if (!colors || colors.length === 0) {
                return '<span>N/A</span>';
            }
            return colors.map(color => `<div class="color-option" style="background-color: ${color};"></div>`).join('');
        }


        function showDeliveryDetails() {
            const deliveryDetailsDiv = document.getElementById('delivery-details-notification');
            const userId = <?php echo $_SESSION['user_id']; ?>; // Assuming user ID is stored in session

            fetch('fetchDeliveryDetails.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `user_id=${userId}`,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const deliveryDetails = data.deliveryDetails;
                    const html = `
                        <p>Name: ${deliveryDetails.full_name}</p>
                        <p>Location: ${deliveryDetails.address}, ${deliveryDetails.city}</p>
                        <p>Phone Number: ${deliveryDetails.phone_no}</p>
                    `;
                    deliveryDetailsDiv.innerHTML = html;
                    deliveryDetailsDiv.style.display = 'block'; // Show the notification
                } else {
                    deliveryDetailsDiv.innerHTML = 'No delivery details found.';
                    deliveryDetailsDiv.style.display = 'block'; // Show the notification
                }
            })
            .catch(error => {
                console.error('Error:', error);
                deliveryDetailsDiv.innerHTML = 'Error fetching delivery details.';
                deliveryDetailsDiv.style.display = 'block'; // Show the notification
            });
        }

        function hideDeliveryDetails() {
            const deliveryDetailsDiv = document.getElementById('delivery-details-notification');
            deliveryDetailsDiv.style.display = 'none'; // Hide the notification
        }

        function showPaymentDetails() {
            document.getElementById('paymentOptionsPopup').style.display = 'flex';
        }

        function closePaymentOptionsPopup() {
            document.getElementById('paymentOptionsPopup').style.display = 'none';
        }

        function payWithCash() {
            closePaymentOptionsPopup();
        }

        function payWithCard() {
        const modal = document.getElementById('payment-popup');
        
        // Fetch the payment form content
        fetch('payment_form.php')
            .then(response => response.text())
            .then(data => {
                // Insert the payment form HTML into the modal
                document.querySelector('.payment-modal-content').innerHTML = data;
                
                // Display the modal
                modal.style.display = 'flex';
                
                // Initialize all the payment form's JavaScript
                initPaymentForm();
            })
            .catch(error => {
                console.error('Error loading payment form:', error);
            });
    }

    // Initialize the payment form's interactive features
    function initPaymentForm() {
        const nameInput = document.getElementById('name');
        const cardNumberInput = document.getElementById('card-number');
        const expiryInput = document.getElementById('expiry');
        const cvvInput = document.getElementById('cvv');
        const paymentForm = document.getElementById('payment-form');
        
        const cardHolderDisplay = document.getElementById('card-holder-display');
        const cardNumberDisplay = document.getElementById('card-number-display');
        const expiryDisplay = document.getElementById('expiry-display');
        const cardTypeDisplay = document.getElementById('card-type');
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('payment-popup');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
        // Format card number with spaces
        function formatCardNumber(value) {
            const v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            const matches = v.match(/\d{4,16}/g);
            const match = matches && matches[0] || '';
            const parts = [];
            
            for (let i = 0, len = match.length; i < len; i += 4) {
                parts.push(match.substring(i, i + 4));
            }
            
            if (parts.length) {
                return parts.join(' ');
            } else {
                return value;
            }
        }
        
        // Detect card type based on first digits
        function detectCardType(number) {
            if (number.startsWith('4')) {
                return 'VISA';
            } else if (/^5[1-5]/.test(number)) {
                return 'MASTERCARD';
            } else if (number.startsWith('3') && (number.charAt(1) === '4' || number.charAt(1) === '7')) {
                return 'AMEX';
            } else {
                return 'CARD';
            }
        }
        
        // Handle cardholder name input
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                cardHolderDisplay.textContent = this.value.toUpperCase() || '';
            });
        }
        
        // Handle card number input
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function() {
                // Format the input value
                this.value = formatCardNumber(this.value);
                
                // Update the display on the card
                cardNumberDisplay.textContent = this.value || 'xxxx xxxx xxxx xxxx';
                
                // Detect and display card type
                const cardType = detectCardType(this.value.replace(/\D/g, ''));
                cardTypeDisplay.textContent = cardType;
            });
        }
        
        // Handle expiry date input
        if (expiryInput) {
            expiryInput.addEventListener('input', function(e) {
                let value = this.value.replace(/[^\d]/g, '');
                
                if (value.length > 3 && !this.value.includes('/')) {
                    value = value.substring(0, 2) + '/' + value.substring(2);
                }
                
                if (value.length > 5) {
                    value = value.substring(0, 5);
                }
                
                this.value = value;
                expiryDisplay.textContent = this.value || 'MM/YY';
            });
            
            // Handle backspace in expiry date field
            expiryInput.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    const cursorPosition = this.selectionStart;
                    
                    if (cursorPosition === 3 && this.value.charAt(2) === '/') {
                        e.preventDefault();
                        this.value = this.value.substring(0, 2);
                        this.selectionStart = this.selectionEnd = 2;
                        expiryDisplay.textContent = this.value || 'MM/YY';
                    }
                }
            });
        }
        
        // Form validation before submission
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent normal form submission
                
                let isValid = true;
                const errorMessages = [];
                
                // Basic card number validation
                const cardNum = cardNumberInput.value.replace(/\s/g, '');
                if (cardNum.length < 13 || cardNum.length > 19 || !/^\d+$/.test(cardNum)) {
                    isValid = false;
                    errorMessages.push("Please enter a valid card number");
                    cardNumberInput.style.borderColor = "#f44336";
                }
                
                // Expiry date validation
                const expiry = expiryInput.value;
                if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
                    isValid = false;
                    errorMessages.push("Please enter a valid expiry date (MM/YY)");
                    expiryInput.style.borderColor = "#f44336";
                } else {
                    const [month, year] = expiry.split('/');
                    const expiryDate = new Date(2000 + parseInt(year), parseInt(month) - 1);
                    const today = new Date();
                    
                    if (expiryDate < today) {
                        isValid = false;
                        errorMessages.push("Card has expired");
                        expiryInput.style.borderColor = "#f44336";
                    }
                }
                
                // CVV validation
                if (!/^\d{3,4}$/.test(cvvInput.value)) {
                    isValid = false;
                    errorMessages.push("Please enter a valid CVV");
                    cvvInput.style.borderColor = "#f44336";
                }
                
                // Name validation
                if (nameInput.value.trim().length < 3) {
                    isValid = false;
                    errorMessages.push("Please enter the cardholder name");
                    nameInput.style.borderColor = "#f44336";
                }
                
                // Show errors or submit
                if (!isValid) {
                    alert("Please correct the following errors:\n" + errorMessages.join("\n"));
                } else {
                    // Use AJAX to submit the form data
                    const formData = new FormData(this);
                    
                    fetch('payment_process.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Insert the response into the modal
                        document.querySelector('.payment-modal-content').innerHTML = data;
                        
                        // If there's a success message, close the modal after some time
                        if (document.querySelector('.alert-success')) {
                            setTimeout(() => {
                                document.getElementById('payment-popup').style.display = 'none';
                            }, 3000);
                        } else {
                            // Re-initialize the form if there was an error
                            initPaymentForm();
                        }
                    });
                }
            });
        }
    }

    </script>
    <script>
        function toggleCart() {
            const cartItemsContainer = document.getElementById('cartItems');
            if (cartItemsContainer.style.display === 'none') {
                fetchCartItems();
                cartItemsContainer.style.display = 'block';
            } else {
                cartItemsContainer.style.display = 'none';
            }
        }

        function fetchCartItems() {
            fetch('fetchCart.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayCartItems(data.data);
                    } else {
                        console.error('Failed to fetch cart items');
                    }
                })
                .catch(error => {
                    console.error('Error fetching cart items:', error);
                });
        }

        function displayCartItems(items) {
            const cartItemsContainer = document.getElementById('cartItems');
            cartItemsContainer.innerHTML = ''; // Clear previous items

            items.forEach(item => {
                const cartItem = document.createElement('div');
                cartItem.className = 'card'; // Use the same class as product cards
                cartItem.innerHTML = `
                    <div class="imgBx">
                        <img src="${item.image_url}" alt="${item.name}">
                    </div>
                    <div class="content">
                        <div class="contentBx">
                            <h3>${item.name}</h3>
                            <p>Quantity: ${item.quantity}</p>
                            <div class="price-container">
                                <span class="original-price">LKR. ${parseFloat(item.original_price).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                `;
                cartItemsContainer.appendChild(cartItem);
            });

            // Update cart count
            document.querySelector('.cart-count').textContent = items.length;
        }
        // Function to update the cart count
        function updateCartCount() {
            fetch('getCartCount.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('.cart-count').textContent = data.count;
                    } else {
                        document.querySelector('.cart-count').textContent = '0';
                    }
                })
                .catch(error => {
                    console.error('Error fetching cart count:', error);
                    document.querySelector('.cart-count').textContent = '0';
                });
        }

        // Update cart count when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });

        // Call updateCartCount after adding to cart
        function addToCart() {
            const productId = currentProductId;
            const quantity = currentQuantity;

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
                return response.json();
            })
            .then(data => {
                console.log("Response Data:", data);
                console.log("Success Value:", data.success);
                if (data.success) {
                    showNotification(data.message);
                    updateCartCount(); // Update cart count after successful add
                } else {
                    showNotification(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification("An error occurred. Please try again");
            });
        }

        function confirmOrderDetails() {
            document.getElementById('confirmOrderDetailsPopup').style.display = 'flex';
        }

        function closeConfirmOrderDetailsPopup() {
            document.getElementById('confirmOrderDetailsPopup').style.display = 'none';
        }

        function confirmOrderDetailsFormSubmit() {
            // Collect form data
            const fullName = document.getElementById('fullName').value;
            const address = document.getElementById('address').value;
            const city = document.getElementById('city').value;
            const phoneNo = document.getElementById('phoneNo').value;

            // Prepare data to send
            const data = new FormData();
            data.append('full_name', fullName);
            data.append('address', address);
            data.append('city', city);
            data.append('phone_no', phoneNo);

            // Send data to the server
            fetch('submitOrderDetails.php', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message); // Show success notification
                    closeConfirmOrderDetailsPopup(); // Close the popup
                } else {
                    showNotification(data.message); // Show error notification
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification("An error occurred. Please try again."); // Show generic error notification
            });
        }
    </script>

<?php include 'AddToCart.php'; ?>

</body>
</html>
