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
    .then(response => response.json())
    .then(users => {
        if (users.length === 0) {
            console.log("No users found");
        } else {
            const userContainer = document.getElementById("account_details");

            userContainer.innerHTML = '';

            users.forEach(user => {
                const userDiv = document.createElement('div');
                userDiv.classList.add('user');

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




const editButtons = document.querySelectorAll(".edit-btn");
const inputfields = document.getElementById("description-input");

let openProductDetails = null;

editButtons.forEach(button => {
    button.addEventListener("click", function () {
        const productDetails = this.closest(".product_details");

        if (openProductDetails && openProductDetails !== productDetails) {
            openProductDetails.style.whiteSpace = 'nowrap'; 
        }

        if (productDetails.style.whiteSpace === 'nowrap' || !productDetails.style.whiteSpace) {
            productDetails.style.whiteSpace = 'normal'; 
        } else {
            productDetails.style.whiteSpace = 'nowrap'; 
        }

        openProductDetails = productDetails;

        console.log(`Toggled white-space for Product ID: ${this.id.split('_')[1]}`);

        inputFields.disabled = true;
    });
});


let openFeedbackText = null;

document.querySelectorAll(".see-btn").forEach(button => {
    button.addEventListener("click", function() {
        const feedbackDetails = this.closest(".feedback_details");
        
        const feedbackText = feedbackDetails.querySelector('.fb');
        
        if (openFeedbackText && openFeedbackText !== feedbackText) {
            openFeedbackText.style.whiteSpace = 'nowrap'; 
        }
        
        if (feedbackText.style.whiteSpace === 'normal') {
            feedbackText.style.whiteSpace = 'nowrap'; 
        } else {
            feedbackText.style.whiteSpace = 'normal';
        }

        openFeedbackText = feedbackText;
    });
});



document.getElementById("pdf").addEventListener("click", () => {
    const orderContent = document.getElementById("order_content");

    html2pdf().from(orderContent).save();
});




