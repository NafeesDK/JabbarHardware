<?php
session_start();
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
            background: rgba(0, 0, 0, 0.80);
            border-radius: 10px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
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
            width: 340px !important;
            height: 200px !important;
            background: linear-gradient(45deg, #009688, #003f38) !important;
            border-radius: 15px !important;
            padding: 25px !important;
            position: relative !important;
            color: white !important;
            margin-bottom: 20px !important;
            flex: none !important;
        }
        
        .card-logo {
            position: absolute !important;
            right: 25px !important;
            top: 25px !important;
            font-size: 26px !important;
            font-weight: bold !important;
        }
        
        .card-number {
            font-size: 22px !important;
            margin-top: 60px !important;
            letter-spacing: 2px !important;
        }
        
        .card-details {
            display: flex !important;
            justify-content: space-between !important;
            margin-top: 40px !important;
        }
        
        .card-holder {
            font-size: 12px !important;
        }
        
        .card-holder-name {
            font-size: 16px !important;
            text-transform: uppercase !important;
        }
        
        .expiry {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
        }
        
        .expiry-title {
            font-size: 12px !important;
        }
        
        .expiry-date {
            font-size: 16px !important;
        }
        
        .form-container {
            width: 100% !important;
            max-width: 400px !important;
        }
        
        .form-group {
            margin-bottom: 20px !important;
        }
        
        label {
            display: block !important;
            margin-bottom: 8px !important;
            margin-left: 10px !important;
            color: white !important;
        }
        
        .input-group {
            position: relative !important;
        }
        
        .input-icon {
            position: absolute !important;
            left: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #009688 !important;
        }
        
        input {
            width: 100% !important;
            padding: 12px 12px 12px 35px !important;
            border: 2px solid rgba(0, 150, 136, 0.3) !important;
            border-radius: 25px !important;
            font-size: 1rem !important;
            background-color: rgba(20, 20, 20, 0.8) !important;
            color: white !important;
            transition: all 0.3s ease !important;
        }
        input:focus {
            outline: none !important;
            border-color: #009688 !important;
            box-shadow: 0 0 0 3px rgba(0, 150, 136, 0.2) !important;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }
                
        .date-cvv {
            display: flex !important;
            gap: 20px !important;
        }
        
        .date-cvv > div {
            flex: 1 !important;
        }
        
        .cardBtn {
            position: relative !important;
            padding: 12px 20px !important;
            border: 2px solid #009688 !important;
            cursor: pointer !important;
            background-color: transparent !important;
            color: white !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
            border-radius: 25px !important;
            width: 100% !important;
            overflow: hidden !important;
            transition: all 0.3s ease !important;
            margin-top: 10px !important;
        }
        .cover {
            background: #009688 !important;
            height: 100% !important;
            width: 0% !important;
            border-radius: 25px !important;
            position: absolute !important;
            left: 0 !important;
            bottom: 0 !important;
            z-index: -1 !important;
            transition: width 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
        }
        .cardBtn:hover .cover {
            width: 100% !important;
        }
        
        .cardBtn:hover {
            color: white !important;
            box-shadow: 0 5px 15px rgba(0, 121, 107, 0.3) !important;
            transform: translateY(-2px) !important;
        }
        
        .alert {
            width: 100% !important;
            padding: 15px !important;
            margin-bottom: 20px !important;
            border-radius: 5px !important;
            text-align: center !important;
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

        .card-type {
            position: absolute !important;
            right: 25px !important;
            top: 25px !important;
            font-size: 26px !important;
            font-weight: bold !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="close-btn" onclick="document.getElementById('payment-popup').style.display='none'">
            <i class="fas fa-times"></i>
        </div>
        
        <form method="POST" action="payment_process.php" id="payment-form">
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
</body>
</html>