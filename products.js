// document.querySelectorAll('.addtobag').forEach(button => {
//     button.addEventListener('click', function() {
//         // Get the product_id from data attributes
//         const productId = this.getAttribute('data-product-id');
        
//         // Send the product_id to the server via AJAX
//         fetch('men.php', {
//             method: 'POST',
//             // headers: {
//             //     'Content-Type': 'application/x-www-form-urlencoded',
//             // },
//             body: `product_id=${productId}`
//         })
//         .then(response => response.text())
//         .then(data => {
//             alert(data);  // Show the response from the PHP script
//         })
//         .catch(error => {
//             console.error('Error:', error);
//         });
//     });
// });