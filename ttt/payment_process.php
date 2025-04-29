<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'conndb.php';
    
    // Get form data and sanitize
    $cardholderName = mysqli_real_escape_string($conn, $_POST['name']);
    $cardNumber = mysqli_real_escape_string($conn, $_POST['card-number']);
    $expiryDate = mysqli_real_escape_string($conn, $_POST['expiry']);
    $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);
    
    // Validate inputs
    $errors = [];
    
    // Validate card number (basic Luhn algorithm check)
    $cardNumberClean = preg_replace('/\D/', '', $cardNumber);
    if (!validateCreditCard($cardNumberClean)) {
        $errors[] = "Invalid card number";
    }
    
    // Validate expiry date
    if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiryDate)) {
        $errors[] = "Invalid expiry date format (MM/YY required)";
    } else {
        list($month, $year) = explode('/', $expiryDate);
        $expMonth = intval($month);
        $expYear = intval('20' . $year);
        $currentMonth = intval(date('m'));
        $currentYear = intval(date('Y'));
        
        if ($expYear < $currentYear || ($expYear == $currentYear && $expMonth < $currentMonth)) {
            $errors[] = "Card has expired";
        }
    }
    
    // Validate CVV
    if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
        $errors[] = "Invalid CVV";
    }
    
    // If validation passes, insert to DB
    if (empty($errors)) {
        // Only store last 4 digits of card number for reference
        $lastFourDigits = substr($cardNumberClean, -4);
        $maskedCardNumber = str_repeat('*', strlen($cardNumberClean) - 4) . $lastFourDigits;
        
        // Get user ID from session (assuming user authentication is implemented)
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default for testing
        
        // Insert data into database with proper columns
        $sql = "INSERT INTO cardDetails (user_id, cardholderName, cardNumber, expiryDate, created_at) 
                VALUES ('$userId', '$cardholderName', '$maskedCardNumber', '$expiryDate', NOW())";
        
        if (mysqli_query($conn, $sql)) {
            $successMessage = "Payment information saved successfully!";
        } else {
            $errorMessage = "Error: Unable to process your payment information.";
            // Only log the actual error, don't display to user
            error_log("MySQL Error: " . mysqli_error($conn));
        }
    } else {
        $errorMessage = implode("<br>", $errors);
    }
    
    // Close connection
    mysqli_close($conn);
}

// Function to validate credit card number using Luhn algorithm
function validateCreditCard($number) {
    // Remove non-digits and check if empty
    if (empty($number)) return false;
    
    // Check length
    if (strlen($number) < 13 || strlen($number) > 19) return false;
    
    // Luhn algorithm
    $sum = 0;
    $numDigits = strlen($number);
    $parity = $numDigits % 2;
    
    for ($i = 0; $i < $numDigits; $i++) {
        $digit = intval($number[$i]);
        
        if ($i % 2 == $parity) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        
        $sum += $digit;
    }
    
    return ($sum % 10 == 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        .container {
            background: rgba(0, 0, 0, 0.80) !important;
            border-radius: 10px !important;
            padding: 40px !important;
            width: 90% !important;
            max-width: 400px !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            position: relative !important;
        }
        
        .close-btn {
            position: absolute !important;
            right: 15px !important;
            top: 15px !important;
            font-size: 24px !important;
            cursor: pointer !important;
            color: #ff6b6b !important;
            transition: transform 0.3s ease !important;
        }

        .close-btn:hover {
            transform: rotate(90deg) !important;
            color: #ff4f4f !important;
        }

        .alert {
            width: 100% !important;
            padding: 15px !important;
            margin-bottom: 20px !important;
            border-radius: 5px !important;
            text-align: center !important;
            color: white !important;
        }
        
        .alert-success {
            background-color: rgba(0, 150, 136, 0.2) !important;
            border: 1px solid #009688 !important;
            color: #009688 !important;
        }
        
        .alert-danger {
            background-color: rgba(244, 67, 54, 0.2) !important;
            border: 1px solid #f44336 !important;
            color: #f44336 !important;
        }
        
        .result-icon {
            font-size: 60px !important;
            margin-bottom: 20px !important;
        }
        
        .success-icon {
            color: #009688 !important;
        }
        
        .error-icon {
            color: #f44336 !important;
        }
        
        .result-message {
            color: white !important;
            text-align: center !important;
            margin-bottom: 20px !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="close-btn" onclick="document.getElementById('payment-popup').style.display='none'">
            <i class="fas fa-times"></i>
        </div>
        
        <?php if (isset($successMessage)): ?>
            <div class="result-icon success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="result-message">
                <?php echo $successMessage; ?>
            </div>
            <p style="color: white; text-align: center;">Thank you for your payment. This window will close automatically.</p>
        <?php elseif (isset($errorMessage)): ?>
            <div class="result-icon error-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="alert alert-danger">
                <?php echo $errorMessage; ?>
            </div>
            <button class="cardBtn" onclick="history.back()">
                <span class="cover"></span>
                Try Again
            </button>
        <?php endif; ?>
    </div>
</body>
</html>