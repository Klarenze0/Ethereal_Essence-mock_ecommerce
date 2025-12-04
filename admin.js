//sidebar ids
const orders = document.getElementById("orders");
const products = document.getElementById("products")
const feedbacks1 = document.getElementById("feedbacks1");
const customers_account_list1 = document.getElementById("customers_account_list1");

//constent pane ids
const order_list = document.getElementById("order_list");
const product_list = document.getElementById("product_list");
const feedbacks2 = document.getElementById("feedbacks2");
const customers_account_list2 = document.getElementById("customers_account_list2");

orders.addEventListener('click', (e) =>{

    e.preventDefault();

    order_list.classList.remove('hidden');
    feedbacks2.classList.add('hidden');
    customers_account_list2.classList.add('hidden');
    product_list.classList.add('hidden');
});


products.addEventListener('click', (e) =>{

    e.preventDefault();

    order_list.classList.add('hidden');
    feedbacks2.classList.add('hidden');
    customers_account_list2.classList.add('hidden');

    
    product_list.classList.remove('hidden');
});

feedbacks1.addEventListener('click', (e) =>{

    e.preventDefault();

    order_list.classList.add('hidden');
    feedbacks2.classList.remove('hidden');
    customers_account_list2.classList.add('hidden');
    product_list.classList.add('hidden');
});

customers_account_list1.addEventListener('click', (e) =>{

    e.preventDefault();

    order_list.classList.add('hidden');
    feedbacks2.classList.add('hidden');
    customers_account_list2.classList.remove('hidden');
    product_list.classList.add('hidden');
});


//user retrieval
fetch('admin.php')
    .then(response => response.json()) // Parse the response as JSON
    .then(users => {
        if (users.length === 0) {
            console.log("No users found");
        } else {
            const userContainer = document.getElementById("account_details");

            // Clear the container before adding new data
            userContainer.innerHTML = '';

            users.forEach(user => {
                const userDiv = document.createElement('div');
                userDiv.classList.add('user');

                // Add user information to the div
                userDiv.innerHTML = `
                    <span>ID: ${user.id}</span>
                    <span>Name: ${user.name}</span>
                    <span>Address: ${user.address}</span>
                    <span>Number: ${user.number}</span>
                    <span>Email: ${user.email}</span>
                `;
                userContainer.appendChild(userDiv);
            });
        }
    })
    .catch(error => console.error('Error:', error));


//button expand
// const btn = document.getElementById("order_btn");
// const orderDetails = document.querySelectorAll('.order_details div');


// btn.addEventListener('click', function(e){

//     orderDetails.forEach(function(div) {
//         div.style.whiteSpace = (div.style.whiteSpace === 'nowrap') ? 'normal' : 'nowrap'; 
//     });

// });


const editButtons = document.querySelectorAll(".edit-btn");
const inputfields = document.getElementById("description-input");

// Store the currently open product for comparison
let openProductDetails = null;

editButtons.forEach(button => {
    button.addEventListener("click", function () {
        // Find the closest parent .product_details for the clicked button
        const productDetails = this.closest(".product_details");

        // If another product was already opened, close it
        if (openProductDetails && openProductDetails !== productDetails) {
            openProductDetails.style.whiteSpace = 'nowrap'; // Close the previously opened product
        }

        // Toggle the white-space style for the clicked product
        if (productDetails.style.whiteSpace === 'nowrap' || !productDetails.style.whiteSpace) {
            productDetails.style.whiteSpace = 'normal'; // Open the clicked product
        } else {
            productDetails.style.whiteSpace = 'nowrap'; // Close it again if it's already open
        }

        // Update the reference to the currently open product
        openProductDetails = productDetails;

        console.log(`Toggled white-space for Product ID: ${this.id.split('_')[1]}`);

        inputFields.disabled = true;
    });
});

// Attach event listeners to all feedback buttons

let openFeedbackText = null;

// Attach event listeners to all feedback buttons
document.querySelectorAll(".see-btn").forEach(button => {
    button.addEventListener("click", function() {
        // Find the closest .feedback_details parent to the clicked button
        const feedbackDetails = this.closest(".feedback_details");
        
        // Find the .fb div inside this specific feedback_details
        const feedbackText = feedbackDetails.querySelector('.fb');
        
        // If there is a previously opened feedback, collapse it
        if (openFeedbackText && openFeedbackText !== feedbackText) {
            openFeedbackText.style.whiteSpace = 'nowrap'; // Collapse the previous feedback
        }
        
        // Toggle the white-space between normal and nowrap for the clicked feedback
        if (feedbackText.style.whiteSpace === 'normal') {
            feedbackText.style.whiteSpace = 'nowrap'; // Collapse the text
        } else {
            feedbackText.style.whiteSpace = 'normal'; // Expand the text
        }

        // Update the reference to the currently opened feedback
        openFeedbackText = feedbackText;
    });
});



document.getElementById("pdf").addEventListener("click", () => {
    const orderContent = document.getElementById("order_content");

    // Use html2pdf to convert the content of the div into a PDF
    html2pdf().from(orderContent).save();
});




