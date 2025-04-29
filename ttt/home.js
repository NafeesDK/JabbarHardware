function toggleMenu() {
    const menu = document.getElementById('menu');
    menu.classList.toggle('active');
}

    document.addEventListener('DOMContentLoaded', function() {
        const cartContainer = document.querySelector('.cart-container');
    
        let isDragging = false;
        let currentY;
        let initialY;
        let yOffset = 0;
    
        // Set the default position to the middle of the screen
        setInitialPosition();
    
        // Add event listeners for both mouse and touch
        cartContainer.addEventListener('mousedown', dragStart);
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', dragEnd);
    
        cartContainer.addEventListener('touchstart', dragStart);
        document.addEventListener('touchmove', drag);
        document.addEventListener('touchend', dragEnd);
    
        function setInitialPosition() {
            const windowHeight = window.innerHeight;
            const headerHeight = document.querySelector('header').offsetHeight;
            const cartHeight = cartContainer.offsetHeight;
    
            // Calculate the default Y position to center the cart button
            const defaultY = (windowHeight - headerHeight) / 2 - (cartHeight / 2);
            cartContainer.style.position = 'absolute';
            cartContainer.style.top = `${defaultY}px`;
        }
    
        function dragStart(e) {
            initialY = (e.type === 'touchstart' ? e.touches[0].clientY : e.clientY) - yOffset;
            isDragging = true;
        }
    
        function drag(e) {
            if (!isDragging) return;
    
            e.preventDefault();
    
            currentY = (e.type === 'touchmove' ? e.touches[0].clientY : e.clientY) - initialY;
    
            // Calculate boundaries
            const windowHeight = window.innerHeight;
            const headerHeight = document.querySelector('header').offsetHeight;
            const cartHeight = cartContainer.offsetHeight;
    
            const topBoundary = headerHeight; // Start below the header
            const bottomBoundary = windowHeight - cartHeight; // Stay above the bottom of the screen
    
            // Constrain vertical movement
            if (currentY < topBoundary) currentY = topBoundary;
            if (currentY > bottomBoundary) currentY = bottomBoundary;
    
            yOffset = currentY;
    
            // Adjust position
            cartContainer.style.position = 'absolute';
            cartContainer.style.top = `${currentY}px`;
        }
    
        function dragEnd() {
            isDragging = false;
        }
});