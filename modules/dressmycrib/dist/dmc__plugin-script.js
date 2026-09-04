document.addEventListener('DOMContentLoaded', function () {
    // Wait until the gallery is fully loaded
    const productGallery = document.querySelector('.woocommerce-product-gallery');

    if (productGallery) {
        // Move the button inside the gallery and show it
        const customButton = document.getElementById('cpib-button');
        if (customButton) {
            productGallery.style.position = 'relative'; // Ensure the gallery is positioned
            customButton.style.display = 'block'; // Make the button visible
            productGallery.appendChild(customButton); // Append the button to the gallery
        }
    }
});
