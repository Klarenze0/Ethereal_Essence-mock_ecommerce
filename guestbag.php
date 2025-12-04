<?php

include ("server.php");

session_start();


// Check if the user is logged in and handle visibility of logout link
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if ($userId) {
    // User is logged in, display user ID and payment options
    $profileLink = 'profile.php';
    echo "<script>
        window.onload = function() {
            document.getElementById('logoutLink').style.display = 'block';
        };
    </script>";
    // Show the payment options
    $paymentOptionsVisible = true;
  } else {
    // User is not logged in, hide logout link and show default payment option (COD)
    $profileLink = 'login.php';
    echo "<script>
            window.onload = function() {
                document.getElementById('logoutLink').style.display = 'none';
            };
          </script>";
    // Show only default payment option (COD)
    $paymentOptionsVisible = false;
  }
  

// Handle item removal (remove from either user_atc or guest_atc)
if (isset($_POST['remove'])) {
    // Get the product_id from POST data
    $product_id = $_POST['product_id'];

    // Check if the user is logged in (session exists)
    if ($userId) {
        // User is logged in, delete from user_atc table
        $delete_sql = "DELETE FROM user_atc WHERE product_id = ? AND id = ?";
    } else {
        // No user logged in, delete from guest_atc table
        $delete_sql = "DELETE FROM guest_atc WHERE product_id = ?";
    }

    // Prepare and execute the delete query
    if ($stmt = $conn->prepare($delete_sql)) {
        if ($userId) {
            // If user is logged in, bind user ID and product ID
            $stmt->bind_param("ii", $product_id, $userId);  
        } else {
            // If no user logged in, bind only product ID
            $stmt->bind_param("i", $product_id);  
        }

        // Execute the query
        if ($stmt->execute()) {
            // Success message

            echo "<script>
                              document.addEventListener('DOMContentLoaded', function () {
                                  const notifdiv = document.getElementById('notif');
                                  if (notifdiv) {
                                      notifdiv.style.display = 'block';
                                  }
    
                                  
                              });
                          </script>";
            // Optionally, refresh or redirect the page to update the cart view
        } else {
            // Failure to remove the product
            echo "<script>alert('Failed to remove product from cart.');</script>";
        }
    } else {
        // Handle preparation failure
        echo "<script>alert('Failed to prepare the SQL query.');</script>";
    }
}

if (isset($_POST['confirm-btn'])) {
    // Check if the user is logged in
    $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

    // Choose the correct table and variables based on session
    if ($userId) {
        $table = 'user_atc'; // Logged in user, use user_atc table
    } else {
        $table = 'guest_atc'; // No user, use guest_atc table
    }

    // SQL query to fetch all products and quantities from the selected table (user_atc or guest_atc)
    $fetch_sql = "SELECT product_id, quantity FROM $table";
    $result = $conn->query($fetch_sql);

    $is_stock_sufficient = true; // Flag to check stock sufficiency

    if ($result->num_rows > 0) {
        // Loop through each product in the cart
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];

            // Fetch the current stock, product name, and price for the product
            $stock_sql = "SELECT product_name, price, stocks FROM product_info WHERE product_id = ?";
            if ($stock_stmt = $conn->prepare($stock_sql)) {
                $stock_stmt->bind_param("i", $product_id);
                $stock_stmt->execute();
                $stock_result = $stock_stmt->get_result();
                $stock_data = $stock_result->fetch_assoc();

                if ($quantity > $stock_data['stocks']) {
                    // If requested quantity exceeds available stock, set flag to false and show error

                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const notifdiv = document.getElementById('notif2');
                                    if (notifdiv) {
                                        notifdiv.style.display = 'block';
                                    }
        
                                    // Delay the redirect by 3 seconds (3000 milliseconds)
                                    setTimeout(function() {
                                        window.location.href = 'index.php';
                                    }, 3000);
                                });
                            </script>";
                    $is_stock_sufficient = false; // Mark as insufficient stock
                    break; // Exit the loop as one product already fails
                }

                // Store product details (name, price, quantity) for later use in purchase history
                $product_name = $stock_data['product_name'];
                $price = $stock_data['price'];
                $total_price = $price * $quantity; // Calculate total price for this product
                $date = date("Y-m-d"); // Current timestamp
                $address = $_POST['address']; // Assuming it's passed in the form

                // Insert into purchase history for each product
$history_sql = "INSERT INTO purchase_history (customer_id, product_id, product_name, address, date, price, quantity) 
VALUES (?, ?, ?, ?, ?, ?, ?)";

if ($history_stmt = $conn->prepare($history_sql)) {
    $history_stmt->bind_param("iisssii", $userId, $product_id, $product_name, $address, $date, $price, $quantity);

    if ($history_stmt->execute()) {
        // Success, you can display a success message or redirect the user
        // echo "<script>alert('Product added to purchase history.');</script>";
    } else {
        // echo "<script>alert('Failed to insert into purchase history.');</script>";
    }
} else {
    // echo "<script>alert('Error preparing the insert query for purchase history.');</script>";
}

            }
        }

        if ($is_stock_sufficient) {
            // Proceed to update stocks and clear the cart if stock is sufficient for all items
            $result->data_seek(0); // Reset the result pointer to reprocess the rows

            while ($row = $result->fetch_assoc()) {
                $product_id = $row['product_id'];
                $quantity = $row['quantity'];

                // Update the product_info table to subtract the quantity from stocks
                $update_sql = "UPDATE product_info SET stocks = stocks - ? WHERE product_id = ?";
                if ($update_stmt = $conn->prepare($update_sql)) {
                    $update_stmt->bind_param("ii", $quantity, $product_id);
                    $update_stmt->execute();
                }
            }

            // After updating stocks, delete all products from the appropriate cart table
            $delete_sql = "DELETE FROM $table WHERE purchase_id IS NOT NULL"; // Adjust condition as necessary
            if ($stmt = $conn->prepare($delete_sql)) {
                if ($stmt->execute()) {
                    // Cart cleared successfully, show confirmation message
                    echo "<script>
                              document.addEventListener('DOMContentLoaded', function () {
                                  const notifdiv = document.getElementById('notif3');
                                  if (notifdiv) {
                                      notifdiv.style.display = 'block';
                                  }
    
                                  // Delay the redirect by 3 seconds (3000 milliseconds)
                                  setTimeout(function() {
                                      window.location.href = 'index.php';
                                  }, 3000);
                              });
                          </script>";
                } else {
                    echo '<script>alert("Failed to clear the cart.");</script>';
                }
            }
        }
    }
}




   // Check if the "plus" or "minus" button was clicked
if (isset($_POST['plus']) || isset($_POST['minus'])) {

    // Get the current quantity and product ID from the form
    $current_quantity = $_POST['qtty'];
    $product_id = $_POST['product_id'];

    // If the "plus" button was clicked, increment the quantity
    if (isset($_POST['plus'])) {
        $new_quantity = $current_quantity;
    }
    // If the "minus" button was clicked, decrement the quantity
    elseif (isset($_POST['minus'])) {
        // Ensure the quantity doesn't go below 1
        $new_quantity = max($current_quantity, 1);
    }

    // Determine which table to update based on session status
    $table = isset($_SESSION['id']) ? 'user_atc' : 'guest_atc';

    // Prepare the SQL statement to update the quantity in the appropriate table
    $update_sql = "UPDATE $table SET quantity = ? WHERE product_id = ?";

    // Prepare and execute the SQL statement
    if ($stmt = $conn->prepare($update_sql)) {
        $stmt->bind_param("ii", $new_quantity, $product_id);
        
        // Execute the query
        if ($stmt->execute()) {
            // echo "Quantity updated successfully.";
        } else {
            // echo "Error updating quantity: " . $stmt->error;
        }
    } else {
        // echo "Error preparing SQL statement: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email and fullname values from the form
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
  
    // Now you can use these variables to do further processing
    // echo "Full Name: " . htmlspecialchars($fullname) . "<br>";
    // echo "Email: " . htmlspecialchars($email);
  
    // Check if the email already exists in the guest_info table
    $check_email_sql = "SELECT guest_email FROM guest_info WHERE guest_email = ?";
    
    if ($check_stmt = $conn->prepare($check_email_sql)) {
        // Bind the email parameter to the prepared statement
        $check_stmt->bind_param("s", $email);
        
        // Execute the query to check if the email exists
        $check_stmt->execute();
        $check_stmt->store_result();
  
        // If the email exists, stop the insertion and show an alert
        if ($check_stmt->num_rows > 0) {
            // echo "<br>Error: The email is already registered.";
        } else {
            // Insert the new guest info if the email doesn't exist
            $sql = "INSERT INTO guest_info (guest_email, guest_name) VALUES (?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                // Bind the parameters to the prepared statement
                $stmt->bind_param("ss", $email, $fullname);  // "ss" indicates two string parameters
  
                // Execute the query
                if ($stmt->execute()) {
                    // echo "<br>Guest information has been successfully saved.";
                } else {
                    echo "<br>Error: " . $stmt->error;
                }
  
                // Close the statement
                $stmt->close();
            } else {
                echo "<br>Error preparing the SQL query: " . $conn->error;
            }
        }
  
        // Close the check statement
        $check_stmt->close();
    } else {
        echo "<br>Error preparing the email check query: " . $conn->error;
    }
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ethereal Essence</title>


    <link rel="stylesheet" href="CSS JS/Bootstrap/css/bootstrap.min.css" />
<link
  rel="stylesheet"
  href="CSS JS/Font Awesome/css/font-awesome.min.css"
/>

<link rel="stylesheet" href="guestbag.css">
</head>
<body>


    <!-- HEADER -->
    <div class="navbar" id="navbar"> 
        <div class="container d-flex">
          <a href="index.php" class="imgcont">
            <img
              src="IMAGES/others/logo.png"
              alt="Ethereal Essence"
              width="100px" 
              class="image"
            />
          </a>
  
          <a href="index.php" class="text">
            <div class="name" id="name1">Ethereal Essence</div>
            <div class="name" id="name2">Scented Dreams</div>
          </a>
  
          <form method="GET" action="search.php">
  <div class="search">
    <a href="" class="btn"> <i class="fa fa-search"></i> </a>
    <input type="text"  class="searchbar" name="search" autocomplete = "off" />
    <button type="submit" class="btn">Search</button>
  </div>
</form>
  
          <div class="menus d-flex">
            <a href="men.php">MEN</a>
            <a href="collection.php">PERFUME COLLECTION</a>
            <a href="women.php">WOMEN</a>
          </div>
  
          <div class="icons d-flex">
          <a id="profileLink" href="<?php echo $profileLink; ?>"> <i class="fa fa-user-o"></i> </a>
            <a href=""> <i class="fa fa-shopping-bag"></i> </a>
            <a href="logout.php" id="logoutLink" style="display: block;" name="logout">Log out</a>
          </div>
        </div>
    </div>

    
    <div class="container">
            <div class="notif" id="notif" style = "display: none;">
              <div class="notif_text">
                Product removed from cart successfully!
              </div>
              <div class="notif_btn">
                <button class="btn" id="btn">Okay</button>
              </div>
            </div>
      </div>

      <div class="container">
            <div class="notif2" id="notif2" style = "display: none;">
              <div class="notif_text2">
              Error! Insufficient stock at the moment
              </div>
              <div class="notif_btn2">
                <button class="btn2" id="btn2">Okay</button>
              </div>
            </div>
      </div>

      <div class="container">
            <div class="notif3" id="notif3" style = "display: none;">
              <div class="notif_text3">
               Order complete!
              </div>
              <div class="notif_btn3">
                <button class="btn3" id="btn3">Okay</button>
              </div>
            </div>
      </div>
    
    <div class="container">
       <div class="wrapper">
        <h1>Shopping Cart</h1>
        <div class="line"></div>
        <!-- Cart Header -->
         
        <div class="cart-header d-flex">
            
          <div class="header-items" id="img_cont">Image</div>
       

          <div class="header-items" id="product_cont">Product</div>
       


          <div class="header-items" id="qtty_cont">Quantity</div>
         


          <div class="header-items" id="price_cont">Price</div>
        
          
          <div class="header-items" id="sub_cont">Subtotal</div>
    

          <div class="header-items" id="remove_cont">Remove Item</div>
          
        </div>

        <div class="placehd">
</div>
          
    <?php

   // Check if the user is logged in
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if ($userId) {
    // User is logged in, get details from 'user_atc' table
    $sql = "SELECT 
                product_info.image,
                product_info.product_name,
                product_info.price,
                user_atc.quantity,
                user_atc.product_id
            FROM 
                product_info
            JOIN
                user_atc ON product_info.product_id = user_atc.product_id
            WHERE 
                user_atc.id = ?"; // Use user_id to get logged-in user's cart details

    // Prepare the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userId); // Bind the user ID parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Display cart items for logged-in user
            while ($row = $result->fetch_assoc()) {
                $subtotal = $row['price'] * $row['quantity']; // Calculate subtotal for each item
                echo '
                    <div class="cart-item d-flex">
                        <div class="item-cart">
                            <img src="' . $row['image'] . '" alt="Product" width="80px" height="80px">
                        </div>
                        <div class="product_cont">
                            <div class="item-cart" id="product-name">' . $row['product_name'] . '</div>
                        </div>
                        <form method="POST">
                            <div class="item-cart quantity-controls" id="quantity-controls">
                                <button class="minus-btn" name="minus">-</button>
                                <input type="text" name="qtty" value="' . $row['quantity'] . '" readonly class="quantity-input" style="width: 30px; text-align: center; margin: 5px">       
                                <button class="plus-btn" name="plus">+</button>
                                <input type="hidden" name="product_id" value="' . $row['product_id'] . '">
                            </div>
                        </form>
                        <div class="item-cart price">₱' . number_format($row['price'], 2) . '</div>
                        <div class="item-cart subtotal">₱' . number_format($subtotal, 2) . '</div>
                        <div class="item-cart">
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="' . $row['product_id'] . '">
                                <button type="submit" name="remove" class="remove">Remove</button>
                            </form>
                        </div>
                    </div>
                    <div class="line"></div>';
            }
        } else {
            echo "<h3>No products in your cart.</h3>";
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('checkoutBtn');
            btn.disabled = true;  // Disable the button
            btn.style.backgroundColor = 'gray';  // Change the background color to gray
            });
          </script>";

        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    // User is not logged in, get details from 'guest_atc' table
    $sql = "SELECT 
                product_info.image,
                product_info.product_name,
                product_info.price,
                guest_atc.quantity,
                guest_atc.product_id
            FROM 
                product_info
            JOIN
                guest_atc ON product_info.product_id = guest_atc.product_id";

    if ($result = $conn->query($sql)) {
        if ($result->num_rows > 0) {
            // Display cart items for guest user
            while ($row = $result->fetch_assoc()) {
                $subtotal = $row['price'] * $row['quantity']; // Calculate subtotal for each item
                echo '
                    <div class="cart-item d-flex">
                        <div class="item-cart">
                            <img src="' . $row['image'] . '" alt="Product" width="80px" height="80px">
                        </div>
                        <div class="product_cont">
                            <div class="item-cart" id="product-name">' . $row['product_name'] . '</div>
                        </div>
                        <form method="POST">
                            <div class="item-cart quantity-controls" id="quantity-controls">
                                <button class="minus-btn" name="minus">-</button>
                                <input type="text" name="qtty" value="' . $row['quantity'] . '" readonly class="quantity-input" style="width: 30px; text-align: center; margin: 5px">       
                                <button class="plus-btn" name="plus">+</button>
                                <input type="hidden" name="product_id" value="' . $row['product_id'] . '">
                            </div>
                        </form>
                        <div class="item-cart price">₱' . number_format($row['price'], 2) . '</div>
                        <div class="item-cart subtotal">₱' . number_format($subtotal, 2) . '</div>
                        <div class="item-cart">
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="' . $row['product_id'] . '">
                                <button type="submit" name="remove" class="remove">Remove</button>
                            </form>
                        </div>
                    </div>
                    <div class="line"></div>';
            }
        } else {
            echo "<h3>No products in your cart.</h3>";
            echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
           const btn = document.getElementById('checkoutBtn');
            btn.disabled = true;  // Disable the button
            btn.style.backgroundColor = 'gray';  // Change the background color to gray
        });
      </script>";
        }
    } else {
        echo "Error: " . $conn->error;
    }
}


    
    ?>

    
</div>
    
    <!-- DAPAT MADISABLE YUNG MGA BUTTONS KAPAG NAG CHECK OUT -->
    <?php


    echo '<div class="cart-summary">
    <p id="total-price">Total: </p>
    <p>Cart Summary</p>
        <!-- Button to trigger checkout -->
        
        
        <button type = "submit" name="proceed_checkout" class="checkout-btn" id="checkoutBtn">Proceed to Checkout</button>
       
      
    </div>


            <!-- Checkout Form (Initially hidden) -->
       <div class="checkout-form" id="checkoutForm" style="display: none;">
        <h2>Checkout</h2>
      
       <form id="checkoutDetails" method = "POST">
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" name="fullname" required><br>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required><br>

        <label for="address">Address</label>
        <input type="text" id="address" name="address" required><br>

        <label for="phone">Phone Number</label>
        <input type="text" id="phone" name="phone" required><br>

        <label for="account">Account Number</label>
        <input type="text" id="account" name="account" required><br>
         <div class = "btn-holder d-flex" style = "gap: 20px"> 

         <!-- Payment Options -->
         <label for="paymentMethod">Payment Method:</label>
                <select id="paymentMethod" name="paymentMethod" required onchange =  "toggleAccountInput()">
                    <?php if ($paymentOptionsVisible): ?>
                        <option value="gcash">Gcash</option>
                        <option value="cod">Cash on Delivery (COD)</option>
                    <?php endif; ?>
                    <option value="paypal">Paypal</option> <!-- Default payment option -->
                </select><br>


        <form method="POST" action=""> 
         <input type="hidden" name="product_id" value="1">
        <button type="submit" name = "confirm-btn" id="confirm-btn">Confirm / Buy</button>
        </form>
        <button type="button" id="cancel-btn">Cancel</button>
        </div>
       </form>
       </div>';
    ?>
       
</div>

        <!-- FOOTER -->

        <div class="footer">
        <div class="container d-flex" >
          <div class="footer-info">
            <a href="index.php">Home</a> <br />
            <br />
            <a href="index.php">About us</a> <br />
            <br />
            
          </div>
  
          <div class="footer-info">
            <div class="contact">Contact</div>
            <br />
            
            
            <i class="fa fa-envelope"></i>
            sample@gmail.com <br/> <br/>
  
            <a href="https://www.facebook.com/people/Ethereal-Essence/61570225152678/?mibextid=ZbWKwL" class="fa fa-facebook"></a>
            <a href="https://www.facebook.com/people/Ethereal-Essence/61570225152678/?mibextid=ZbWKwL">Facebook</a><br />
            <br />
            <a href="" class="fa fa-instagram"></a>
            <a href="">Instagram</a><br />
            <br />
            <i class="fa fa-globe"> </i>
            Carillo Hagonoy Bulacan
          </div>
  
          <div class="footer-info">
            
            <div class="btnholder">
              <a href = "collection.php">
              <button class="shopnow">SHOP NOW</button>
        </a>
            </div> <br>
            <div class="cta">
              <a href="#navbar">
                <button>Back to top</button>
              </a>
            </div>
          </div>
        </div>
  
        <div class="line"></div>
  
        <div class="credits">© 2024 Ethereal Essence. All rights reserved.</div>
      </div>

          <script src="guestbag.js"></script>

          <script>
        document.addEventListener('DOMContentLoaded', function () {
            const okbtn = document.getElementById('btn');
            const notifdiv = document.getElementById('notif');

            if (okbtn) {
                okbtn.addEventListener('click', function () {
                   notifdiv.style.display = 'none';
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const okbtn = document.getElementById('btn2');
            const notifdiv = document.getElementById('notif2');

            if (okbtn) {
                okbtn.addEventListener('click', function () {
                   notifdiv.style.display = 'none';
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const okbtn = document.getElementById('btn3');
            const notifdiv = document.getElementById('notif3');

            if (okbtn) {
                okbtn.addEventListener('click', function () {
                   notifdiv.style.display = 'none';
                });
            }
        });

        function toggleAccountInput() {
        const paymentMethod = document.getElementById('paymentMethod');
        const accountInput = document.getElementById('account');
        
        // Check if 'Cash on Delivery' is selected
        if (paymentMethod.value === 'cod') {
         
            accountInput.disabled = true;  // Disable account input for COD
            accountInput.value = '';
            accountInput.style.backgroundColor = '#f0f0f0';  // Gray color for disabled input
        } else {
            accountInput.disabled = false;  // Enable account input for other methods
            accountInput.style.backgroundColor = '';  // Reset background color
            
        }
    }
      </script>
</body>
</html>