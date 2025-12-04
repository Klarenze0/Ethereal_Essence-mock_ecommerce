// Sample cart items
const cartItems = [
  { id: 1, name: "Product A Name", price: 10.99, quantity: 1, image: "https://via.placeholder.com/80" },
  { id: 2, name: "Product B Name", price: 20.49, quantity: 1, image: "https://via.placeholder.com/80" },
  { id: 3, name: "Product C Name", price: 15.75, quantity: 1, image: "https://via.placeholder.com/80" },
];

// Render the cart
function renderCart() {
  const cartContainer = document.querySelector(".cart-items-container");
  const subtotalElement = document.getElementById("subtotal");
  cartContainer.innerHTML = "";
  let totalSubtotal = 0;

  cartItems.forEach((item) => {
    const itemSubtotal = item.price * item.quantity;
    totalSubtotal += itemSubtotal;

    const itemDiv = document.createElement("div");
    itemDiv.className = "cart-item";
    itemDiv.innerHTML = `
      <img src="${item.image}" alt="${item.name}">
      <div class="item-info">
        <h3>${item.name}</h3>
      </div>
      <div class="item-controls">
        <button onclick="updateQuantity(${item.id}, -1)">-</button>
        <input class="quantity" type="text" value="${item.quantity}" readonly>
        <button onclick="updateQuantity(${item.id}, 1)">+</button>
      </div>
      <p>$${item.price.toFixed(2)}</p>
      <p>$${itemSubtotal.toFixed(2)}</p>
      <button class="remove-btn" onclick="removeItem(${item.id})">Remove</button>
    `;
    cartContainer.appendChild(itemDiv);
  });

  subtotalElement.textContent = totalSubtotal.toFixed(2);
}

// Update quantity of items
function updateQuantity(id, change) {
  const item = cartItems.find((item) => item.id === id);
  if (item) {
    item.quantity += change;
    if (item.quantity <= 0) {
      removeItem(id);
    } else {
      renderCart();
    }
  }
}

// Remove item from the cart
function removeItem(id) {
  const index = cartItems.findIndex((item) => item.id === id);
  if (index !== -1) {
    cartItems.splice(index, 1);
    renderCart();
  }
}

// Proceed to checkout (Display payment options)
function proceedToCheckout() {
  // Disable cart item interactions (buttons, quantity inputs)
  const cartItemsElements = document.querySelectorAll('.cart-item');
  cartItemsElements.forEach(item => {
    const removeButton = item.querySelector('.remove-btn');
    const quantityInput = item.querySelector('.quantity');
    const minusButton = item.querySelector('button:first-child');
    const plusButton = item.querySelector('button:last-child');

    if (removeButton) removeButton.disabled = true; // Disable remove button
    if (quantityInput) quantityInput.disabled = true; // Disable quantity input
    if (minusButton) minusButton.disabled = true; // Disable minus button
    if (plusButton) plusButton.disabled = true; // Disable plus button
  });

  // Hide the checkout button and cart summary
  document.getElementById('checkout-btn').style.display = 'none';
  document.querySelector('.cart-summary').style.display = 'none';

  // Show the payment options
  document.getElementById('paymentOptions').style.display = 'block';
}

// Cancel payment (Re-enable cart editing)
function cancelPayment() {
  // Show the cart summary and checkout button again
  document.getElementById('checkout-btn').style.display = 'inline-block';
  document.querySelector('.cart-summary').style.display = 'block';

  // Hide the payment options
  document.getElementById('paymentOptions').style.display = 'none';

  // Enable cart items editing again
  const cartItemsElements = document.querySelectorAll('.cart-item');
  cartItemsElements.forEach(item => {
    const removeButton = item.querySelector('.remove-btn');
    const quantityInput = item.querySelector('.quantity');
    const minusButton = item.querySelector('button:first-child');
    const plusButton = item.querySelector('button:last-child');

    if (removeButton) removeButton.disabled = false; // Re-enable remove button
    if (quantityInput) quantityInput.disabled = false; // Re-enable quantity input
    if (minusButton) minusButton.disabled = false; // Re-enable minus button
    if (plusButton) plusButton.disabled = false; // Re-enable plus button
  });
}

// Event listener for Confirm Payment
document.getElementById("confirm-payment-btn").addEventListener("click", function () {
  // Get customer details from checkout form (if form fields are still visible)
  const fullname = document.getElementById("fullname")?.value || "N/A";
  const email = document.getElementById("email")?.value || "N/A";
  const address = document.getElementById("address")?.value || "N/A";
  const phone = document.getElementById("phone")?.value || "N/A";

  // Get the selected payment method
  const paymentMethod = document.querySelector('input[name="payment-method"]:checked')?.value;

  if (!paymentMethod) {
    alert("Please select a payment method!");
    return;
  }

  // Hide payment options and display confirmation details
  document.getElementById("paymentOptions").style.display = "none";
  const confirmationDetails = document.getElementById("confirmationDetails");
  confirmationDetails.style.display = "block";

  // Populate customer details
  const customerDetails = document.getElementById("customerDetails");
  customerDetails.innerHTML = `
    <p><strong>Name:</strong> ${fullname}</p>
    <p><strong>Email:</strong> ${email}</p>
    <p><strong>Address:</strong> ${address}</p>
    <p><strong>Phone:</strong> ${phone}</p>
    <p><strong>Payment Method:</strong> ${paymentMethod}</p>
  `;

  // Populate purchased items
  const purchasedItems = document.getElementById("purchasedItems");
  purchasedItems.innerHTML = "";
  cartItems.forEach((item) => {
    const itemDiv = document.createElement("div");
    itemDiv.className = "purchased-item";
    itemDiv.innerHTML = `
      <p><strong>${item.name}</strong> (x${item.quantity}) - $${(item.price * item.quantity).toFixed(2)}</p>
    `;
    purchasedItems.appendChild(itemDiv);
  });

  // Update the confirmation total as well
  const confirmationTotal = document.getElementById("confirmationTotal");
  let totalSubtotal = 0;
  cartItems.forEach((item) => {
    totalSubtotal += item.price * item.quantity;
  });
  confirmationTotal.textContent = totalSubtotal.toFixed(2);
});

// Event listener for Cancel Payment
document.getElementById("cancel-payment-btn").addEventListener("click", function () {
  // Reset the form and show payment options again
  document.getElementById("paymentForm").reset();
  document.getElementById("paymentOptions").style.display = "block";
  document.getElementById("confirmationDetails").style.display = "none";
});

// Event listener for checkout button
document.getElementById("checkout-btn").addEventListener("click", proceedToCheckout);

// Initialize the cart by rendering it
document.addEventListener('DOMContentLoaded', function() {
  renderCart();
});
