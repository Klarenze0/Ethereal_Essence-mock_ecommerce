// ARALIN MO TO TANGA

document.addEventListener('DOMContentLoaded', function () {
   

    // Function to update the subtotal and total price
    function updateCart() {
        let total = 0;  // Initialize total price
        const cartItems = document.querySelectorAll('.cart-item');  

        cartItems.forEach(function(item) {
            // Get price and quantity for each item
            const price = parseFloat(item.querySelector('.price').textContent.replace('₱', '').replace(',', '').trim());
            const quantity = parseInt(item.querySelector('.quantity-input').value);

            // Calculate the subtotal for this item
            const subtotal = price * quantity;

            // Update the subtotal display
            item.querySelector('.subtotal').textContent = '₱' + subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); 
            
            // Add to the total price
            total += subtotal;
        });

        // Update the total price in the cart-summary
        document.getElementById('total-price').textContent = 'Total: ₱' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); 
    }

    // Event listener for the minus button
    const minusButtons = document.querySelectorAll('.minus-btn');
    minusButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const quantityInput = button.nextElementSibling;  // Find the input field
            let quantity = parseInt(quantityInput.value);
            if (quantity > 1) {
                quantity--;  // Decrease quantity
                quantityInput.value = quantity;
                updateCart();  // Update cart totals
            }
        });
    });

    // Event listener for the plus button
    const plusButtons = document.querySelectorAll('.plus-btn');
    plusButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const quantityInput = button.previousElementSibling;  // Find the input field
            let quantity = parseInt(quantityInput.value);
            quantity++;  // Increase quantity
            quantityInput.value = quantity;
            updateCart();  // Update cart totals
        });
    });

    // Initialize the cart when the page loads
    updateCart();


    const checkoutButton = document.querySelector('.checkout-btn');
    const checkoutFormDiv = document.getElementById('checkoutForm'); 
    const cancelButton = document.getElementById('cancel-btn');

    // Disable buttons for all items
    function disableCartButtons() {
        // Get all cart items
        const cartItems = document.querySelectorAll('.cart-item'); 
        cartItems.forEach(item => {
            // Disable minus, plus, and remove buttons for each item
            item.querySelector('.minus-btn').disabled = true;
            item.querySelector('.plus-btn').disabled = true;
            item.querySelector('.remove').disabled = true;
        });
    }

    // Enable buttons for all items
    function enableCartButtons() {
        const cartItems = document.querySelectorAll('.cart-item');

        cartItems.forEach(item => {
            item.querySelector('.minus-btn').disabled = false;
            item.querySelector('.plus-btn').disabled = false;
            item.querySelector('.remove').disabled = false;
        });
    }

    // When the "Proceed to Checkout" button is clicked, display the checkout form and disable buttons
    checkoutButton.addEventListener('click', function () {
        checkoutFormDiv.style.display = 'block';  // Show the checkout form
        document.querySelector('.cart-summary').style.display = 'block';  // Hide the cart summary
        disableCartButtons();  // Disable the buttons
    });

    // When the "Cancel" button is clicked, hide the checkout form and enable buttons
    cancelButton.addEventListener('click', function () {
        checkoutFormDiv.style.display = 'none';  // Hide the checkout form
        document.querySelector('.cart-summary').style.display = 'block';  // Show the cart summary
        enableCartButtons();  // Re-enable the buttons
    });


    const button = document.getElementById("checkoutBtn");

    button.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent the form from submitting if it's part of a form
    
        const checkoutForm = document.getElementById("checkoutForm");
    
        // Ensure the form is visible before scrolling
        if (checkoutForm.style.display === "none") {
            checkoutForm.style.display = "block";
        }
    
        // Smooth scroll to the form
        checkoutForm.scrollIntoView({ behavior: "smooth" });
    });
    
 
   

});
