const productContainers = [...document.querySelectorAll('.menu-grid')];
const nxtBtn = [...document.querySelectorAll('.nxt-btn')];
const preBtn = [...document.querySelectorAll('.pre-btn')];

productContainers.forEach((item, i) => {
    let containerDimensions = item.getBoundingClientRect();
    let scrollDistance = (200 * 3) + (15 * 2); 

    nxtBtn[i].addEventListener('click', () => {
        item.scrollLeft += scrollDistance;
    })

    preBtn[i].addEventListener('click', () => {
        item.scrollLeft -= scrollDistance;
    })
})

function enableMouseWheelScrolling(gridId, speedMultiplier = 2) {
    const grid = document.getElementById(gridId);
    if (grid) {
        grid.addEventListener("wheel", (event) => {
            event.preventDefault();
            const delta = Math.abs(event.deltaY) >= Math.abs(event.deltaX) ? event.deltaY : event.deltaX;
            const scrollDistance = (200 * 3) + (15 * 2); 
            grid.scrollBy({
                left: delta > 0 ? scrollDistance : -scrollDistance,
                behavior: "smooth",
            });
        }, { passive: false }); 
    }
}

document.addEventListener('DOMContentLoaded', function() {
    enableMouseWheelScrolling('offer-grid', 3);
    enableMouseWheelScrolling('product-grid', 3);
    
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        card.addEventListener('click', function() {
            this.classList.toggle('active');
            
            cards.forEach(otherCard => {
                if(otherCard !== this) {
                    otherCard.classList.remove('active');
                }
            });
        });
    });
});