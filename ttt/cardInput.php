<?php
// Start session
session_start();

// Check if form is submitted
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
    <title>Payment Details</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: rgba(0, 0, 0, 0.80);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            background: rgba(0, 0, 0, 0.80);
            border-radius: 10px;
            padding: 40px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card-container {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }
        
        .card {
            width: 340px;
            height: 200px;
            background: linear-gradient(45deg, #009688, #003f38);
            border-radius: 15px;
            padding: 25px;
            position: relative;
            color: white;
            margin-bottom: 20px;
        }
        
        .card-logo {
            position: absolute;
            right: 25px;
            top: 25px;
            font-size: 26px;
            font-weight: bold;
        }
        
        .card-number {
            font-size: 22px;
            margin-top: 60px;
            letter-spacing: 2px;
        }
        
        .card-details {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .card-holder {
            font-size: 12px;
        }
        
        .card-holder-name {
            font-size: 16px;
            text-transform: uppercase;
        }
        
        .expiry {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        
        .expiry-title {
            font-size: 12px;
        }
        
        .expiry-date {
            font-size: 16px;
        }
        
        .form-container {
            width: 100%;
            max-width: 400px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            margin-left: 10px;
            color: white;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #009688;
        }
        
        input {
            width: 100%;
            padding: 12px 12px 12px 35px;
            border: 2px solid rgba(0, 150, 136, 0.3);
            border-radius: 25px;
            font-size: 1rem;
            background-color: rgba(20, 20, 20, 0.8);
            color: white;
            transition: all 0.3s ease;
        }
        input:focus {
            outline: none;
            border-color: #009688;
            box-shadow: 0 0 0 3px rgba(0, 150, 136, 0.2);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
                
        .date-cvv {
            display: flex;
            gap: 20px;
        }
        
        .date-cvv > div {
            flex: 1;
        }
        
        .cardBtn {
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
        .cardBtn:hover .cover {
            width: 100%;
        }
        
        .cardBtn:hover {
            color: white;
            box-shadow: 0 5px 15px rgba(0, 121, 107, 0.3);
            transform: translateY(-2px);
        }
        
        .alert {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        
        .alert-success {
            background-color: rgba(0, 150, 136, 0.2);
            border: 1px solid #009688;
            color: #009688;
        }
        
        .alert-danger {
            background-color: rgba(244, 67, 54, 0.2);
            border: 1px solid #f44336;
            color: #f44336;
        }

        .card-type {
            position: absolute;
            right: 25px;
            top: 25px;
            font-size: 26px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="payment-form">
            <div class="card-container">
                <div class="card">
                    <div class="card-type" id="card-type">CARD</div>
                    <div class="card-number" id="card-number-display">xxxx xxxx xxxx xxxx</div>
                    <div class="card-details">
                        <div>
                            <div class="card-holder">CARD HOLDER</div>
                            <div class="card-holder-name" id="card-holder-display"></div>
                        </div>
                        <div class="expiry">
                            <div class="expiry-title">VALID THRU</div>
                            <div class="expiry-date" id="expiry-display">MM/YY</div>
                        </div>
                    </div>
                </div>
                
                <div class="form-container">
                    <div class="form-group">
                        <label for="name">Cardholder Name</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="far fa-user"></i></span>
                            <input type="text" id="name" name="name" placeholder="Full Name as printed on card" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-credit-card"></i></span>
                            <input type="text" id="card-number" name="card-number" placeholder="XXXX XXXX XXXX XXXX" maxlength="19" required>
                        </div>
                    </div>
                    
                    <div class="form-group date-cvv">
                        <div>
                            <label for="expiry">Exp Date</label>
                            <div class="input-group">
                                <span class="input-icon"><i class="far fa-calendar-alt"></i></span>
                                <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" required>
                            </div>
                        </div>
                        
                        <div>
                            <label for="cvv">CVV</label>
                            <div class="input-group">
                                <span class="input-icon"><i class="fas fa-lock"></i></span>
                                <input type="password" id="cvv" name="cvv" placeholder="•••" maxlength="4" required>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="cardBtn">
                        <span class="cover"></span>
                        Confirm Payment
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const nameInput = document.getElementById('name');
        const cardNumberInput = document.getElementById('card-number');
        const expiryInput = document.getElementById('expiry');
        const cvvInput = document.getElementById('cvv');
        const paymentForm = document.getElementById('payment-form');
        
        const cardHolderDisplay = document.getElementById('card-holder-display');
        const cardNumberDisplay = document.getElementById('card-number-display');
        const expiryDisplay = document.getElementById('expiry-display');
        const cardTypeDisplay = document.getElementById('card-type');
        
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
            const firstDigit = number.charAt(0);
            const firstTwoDigits = number.substring(0, 2);
            const firstFourDigits = number.substring(0, 4);
            
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
        nameInput.addEventListener('input', function() {
            cardHolderDisplay.textContent = this.value.toUpperCase() || '';
        });
        
        // Handle card number input
        cardNumberInput.addEventListener('input', function() {
            // Format the input value
            this.value = formatCardNumber(this.value);
            
            // Update the display on the card
            cardNumberDisplay.textContent = this.value || 'xxxx xxxx xxxx xxxx';
            
            // Detect and display card type
            const cardType = detectCardType(this.value.replace(/\D/g, ''));
            cardTypeDisplay.textContent = cardType;
        });
        
        // Handle expiry date input
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
        
        // Form validation before submission
        paymentForm.addEventListener('submit', function(e) {
            let isValid = true;
            const errorMessages = [];
            
            // Basic card number validation (Luhn algorithm would be better)
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
                e.preventDefault();
                alert("Please correct the following errors:\n" + errorMessages.join("\n"));
            }
        });
    </script>
</body>
</html>