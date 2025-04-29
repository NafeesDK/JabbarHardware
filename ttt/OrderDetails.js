function confirmOrderDetailsFormSubmit() {
    const fullName = document.getElementById('fullName').value;
    const address = document.getElementById('address').value;
    const city = document.getElementById('city').value;
    const phoneNo = document.getElementById('phoneNo').value;

    const userId = getCurrentUser Id(); // Ensure this function is defined

    const data = {
        user_id: userId,
        full_name: fullName,
        address: address,
        city: city,
        phone_no: phoneNo
    };

    fetch('/api/submitOrderDetails', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
        closeConfirmOrderDetailsPopup();
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function getCurrentUser Id() {
    return 1; // Replace with your actual logic to get the user ID
}