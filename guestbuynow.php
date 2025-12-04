<?php

include ("server.php");

session_start();
$product_id = $_SESSION['product_id'];
$price = $_SESSION['price'];
$quantity = $_SESSION['quantity'];


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



if (isset($_POST['confirm-btn'])) {
  // Fetch the current stock for the selected product
  $stock_sql = "SELECT product_name, price, stocks, product_id FROM product_info WHERE product_id = ?";
  if ($stock_stmt = $conn->prepare($stock_sql)) {
      $stock_stmt->bind_param("i", $product_id);
      $stock_stmt->execute();
      $stock_result = $stock_stmt->get_result();
      $stock_data = $stock_result->fetch_assoc();

      if (!$stock_data) {
          echo "<script>alert('Product not found.');</script>";
          exit;
      }

      // Check if requested quantity exceeds available stock
      if ($quantity > $stock_data['stocks']) {
          echo "<script>
              document.addEventListener('DOMContentLoaded', function () {
                  const notifdiv = document.getElementById('kulang');
                  notifdiv.style.display = 'block';
              });
          </script>";
      } else {
          // Update the product_info table to subtract the quantity from stocks
          $update_sql = "UPDATE product_info SET stocks = stocks - ? WHERE product_id = ?";
          if ($update_stmt = $conn->prepare($update_sql)) {
              $update_stmt->bind_param("ii", $quantity, $product_id);
              if ($update_stmt->execute()) {
                  // After updating stock, insert the purchase details into purchase_history
                  $product_name = $stock_data['product_name'];
                  $price = $stock_data['price'];
                  $total_price = $price * $quantity; // Calculate total price
                  $date = date("Y-m-d"); // Current timestamp
                  $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
                  $address = $_POST['address']; // Assuming it's passed in the form

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

                  $history_sql = "INSERT INTO purchase_history (customer_id, product_id, product_name, address, date, price, quantity) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)";

                  if ($history_stmt = $conn->prepare($history_sql)) {
                      // Bind the variables to the prepared statement
                      $history_stmt->bind_param("iisssii", $userId, $product_id, $product_name, $address, $date, $price, $quantity);

                      if ($history_stmt->execute()) {
                          // Success, you can display a success message or redirect the user
                          // echo "<script>alert('Purchase recorded successfully.');</script>";
                      } else {
                          // echo "<script>alert('Failed to insert into purchase_history.');</script>";
                      }
                  } else {
                      echo "<script>alert('Error preparing the insert query for purchase_history.');</script>";
                  }
              } else {
                  echo "<script>alert('Failed to update stock.');</script>";
              }
          } else {
              echo "<script>alert('Error preparing the stock update query.');</script>";
          }
      }
  } else {
      echo "<script>alert('Error fetching product details.');</script>";
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
<link rel="stylesheet" href="guestbuynow.css">
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
    <input type="text"  class="searchbar" name="search" autocomplete = "off"/>
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
            <a href="guestbag.php"> <i class="fa fa-shopping-bag"></i> </a>
            <a href="logout.php" id="logoutLink" style="display: block;" name="logout">Log out</a>
          </div>
        </div>
    </div>

    

      <!-- KULANG STOCKS -->
      <div class="container">
            <div class="kulang" id="kulang" style = "display: none;">
              <div class="notif_text2">
              Error! Insufficient stock at the moment
              </div>
              <div class="notif_btn2">
                <button class="btn2" id="btn2">Okay</button>
              </div>
            </div>
      </div>

      <!-- ORDER COMPLETE -->
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
        <h1>Check out</h1>
        <div class="line"></div>
        
        <!-- Checkout Form (Initially hidden) -->
        <div class="checkout-form" id="checkoutForm" style="display: block;">
    
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
        
         <!-- Payment Options -->
         <label for="paymentMethod">Payment Method:</label>
                <select id="paymentMethod" name="paymentMethod" required onchange =  "toggleAccountInput()">
                    <?php if ($paymentOptionsVisible): ?>
                        <option value="gcash">Gcash</option>
                        <option value="cod">Cash on Delivery (COD)</option>
                    <?php endif; ?>
                    <option value="paypal">Paypal</option> <!-- Default payment option -->
                </select><br>


        <div class = "btn-holder d-flex" style = "gap: 20px"> 
        <form method="POST" action=""> 
        <input type="hidden" name="product_id" value="1">
        
        
        <button type="submit" name = "confirm-btn" id="confirm-btn">Confirm / Buy</button>
        </form>
        </form>
      
        <button type="submit" id="cancel-btn" name="cancel">Cancel</button>
        </div>
        
        
      
        </div>
        </div>
    
</div>

        <!-- FOOTER -->

        <div class="footer">
        <div class="container d-flex">
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
            const notifdiv = document.getElementById('kulang');

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

        document.getElementById('btn3').addEventListener('click', function() {
        window.location.href = 'index.php';  // Redirect to index.php
        });

        document.getElementById('cancel-btn').addEventListener('click', function () {
            // Go back to the previous page
            window.history.back();
        });

        // Get the payment method select element and account input field
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

    // Run the function on page load to set the initial state
  
      </script>
</body>
</html>