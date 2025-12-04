
document.addEventListener('DOMContentLoaded', function () {
   

    function updateCart() {
        let total = 0;  
        const cartItems = document.querySelectorAll('.cart-item');  

        cartItems.forEach(function(item) {
            const price = parseFloat(item.querySelector('.price').textContent.replace('₱', '').replace(',', '').trim());
            const quantity = parseInt(item.querySelector('.quantity-input').value);

            const subtotal = price * quantity;

            item.querySelector('.subtotal').textContent = '₱' + subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); 
            
            total += subtotal;
        });

        document.getElementById('total-price').textContent = 'Total: ₱' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); 
    }

    const minusButtons = document.querySelectorAll('.minus-btn');
    minusButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const quantityInput = button.nextElementSibling;  
            let quantity = parseInt(quantityInput.value);
            if (quantity > 1) {
                quantity--;  
                quantityInput.value = quantity;
                updateCart();  
            }
        });
    });

    const plusButtons = document.querySelectorAll('.plus-btn');
    plusButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const quantityInput = button.previousElementSibling;  
            let quantity = parseInt(quantityInput.value);
            quantity++;  
            quantityInput.value = quantity;
            updateCart(); 
        });
    });

    updateCart();


    const checkoutButton = document.querySelector('.checkout-btn');
    const checkoutFormDiv = document.getElementById('checkoutForm'); 
    const cancelButton = document.getElementById('cancel-btn');

    function disableCartButtons() {
        const cartItems = document.querySelectorAll('.cart-item'); 
        cartItems.forEach(item => {
            item.querySelector('.minus-btn').disabled = true;
            item.querySelector('.plus-btn').disabled = true;
            item.querySelector('.remove').disabled = true;
        });
    }

    function enableCartButtons() {
        const cartItems = document.querySelectorAll('.cart-item');

        cartItems.forEach(item => {
            item.querySelector('.minus-btn').disabled = false;
            item.querySelector('.plus-btn').disabled = false;
            item.querySelector('.remove').disabled = false;
        });
    }

    checkoutButton.addEventListener('click', function () {
        checkoutFormDiv.style.display = 'block'; 
        document.querySelector('.cart-summary').style.display = 'block'; 
        disableCartButtons(); 
    });

    cancelButton.addEventListener('click', function () {
        checkoutFormDiv.style.display = 'none';  
        document.querySelector('.cart-summary').style.display = 'block';  
        enableCartButtons(); 
    });


    const button = document.getElementById("checkoutBtn");

    button.addEventListener("click", function (event) {
        event.preventDefault(); 
    
        const checkoutForm = document.getElementById("checkoutForm");
    
        if (checkoutForm.style.display === "none") {
            checkoutForm.style.display = "block";
        }
    
        checkoutForm.scrollIntoView({ behavior: "smooth" });
    });
    
 
   

});
