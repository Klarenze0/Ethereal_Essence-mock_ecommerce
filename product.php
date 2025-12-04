<?php

include ("server.php");

session_start();


$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if (isset($_GET['product_id'])) {
  // Get the product_id from the URL
  $product_id = $_GET['product_id'];
  
  // Optionally, you can store it in a session variable to use across different pages
  $_SESSION['product_id'] = $product_id;
  

  // Echo the product_id for demonstration purposes
//   echo "Product ID: " . $product_id;
//   echo "<br>";
}

if ($userId) {
    // User is logged in, display user ID and show logout link
    // echo "The user ID is: " . $userId;
    // echo "<br>"'
    $profileLink = 'profile.php';
    echo "<script>
    window.onload = function() {
        document.getElementById('logoutLink').style.display = 'block';
    };
  </script>";

  $showNotif = 'notif2'; // Set the ID of the modal to show
} else {
    // User is not logged in, hide logout link
    // echo "User ID is not set or is empty.";
    $profileLink = 'login.php';
    echo "<script>
            window.onload = function() {
                document.getElementById('logoutLink').style.display = 'none';
            };
          </script>";

          $showNotif = 'notif3'; // Set the ID of the modal to show
}

    if (isset($_POST['addtobag'])) {
    // Get product_id from POST data
    $product_id = $_POST['product_id'];
    $purchase_date = date("Y-m-d H:i:s");  // Set current date and time for purchase_date
    $quantity = (int) $_POST['quantity']; // Force the quantity to be an integer

    // Check if the user is logged in
    $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

    // Dynamically set table and SQL for logged-in or guest user
    if ($userId) {
        // User is logged in, check the user_atc table
        $check_sql = "SELECT quantity FROM user_atc WHERE product_id = ? AND id = ?";
        $table = 'user_atc'; // Set the table for logged-in user
    } else {
        // No user logged in, check the guest_atc table
        $check_sql = "SELECT quantity FROM guest_atc WHERE product_id = ?";
        $table = 'guest_atc'; // Set the table for guest user
    }

    // Check if the product already exists in the respective table (user or guest)
    if ($stmt = $conn->prepare($check_sql)) {
        if ($userId) {
            // Bind user ID with product ID for logged-in user
            $stmt->bind_param("ii", $product_id, $userId); 
        } else {
            // Bind only product ID for guest user
            $stmt->bind_param("i", $product_id);  
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Product exists, update the quantity
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $quantity;

            // Update the product in the appropriate cart table
            $update_sql = "UPDATE $table SET quantity = ?, purchase_date = ? WHERE product_id = ?";
            if ($update_stmt = $conn->prepare($update_sql)) {
                if ($userId) {
                    // For logged-in user
                    $update_stmt->bind_param("isi", $new_quantity, $purchase_date, $product_id); 
                } else {
                    // For guest user
                    $update_stmt->bind_param("isi", $new_quantity, $purchase_date, $product_id); 
                }

                if ($update_stmt->execute()) {
                    // Success message or redirection can be handled here
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const notifdiv = document.getElementById('notif');
                            notifdiv.style.display = 'block';
                        });
                    </script>";
                    // echo "<script>alert('addedd.');</script>";
                } else {
                    echo "<script>alert('Failed to update product quantity.');</script>";
                }
            }
        } else {
            // Product does not exist, insert a new row
            $insert_sql = "INSERT INTO $table (product_id, purchase_date, quantity" . ($userId ? ", id" : "") . ") VALUES (?, ?, ?" . ($userId ? ", ?" : "") . ")";
            if ($insert_stmt = $conn->prepare($insert_sql)) {
                
                if ($userId) {
                    // For logged-in user
                    $insert_stmt->bind_param("isii", $product_id, $purchase_date, $quantity, $userId); 
                } else {
                    // For guest user
                    $insert_stmt->bind_param("isi", $product_id, $purchase_date, $quantity); 
                }

                if ($insert_stmt->execute()) {
                    // Success message or redirection can be handled here
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const notifdiv = document.getElementById('notif');
                            notifdiv.style.display = 'block';
                        });
                    </script>";
                    // echo "<script>alert('added.');</script>";
                } else {
                    echo "<script>alert('Failed to add product to cart.');</script>";
                }
            }
        }
    } else {
        // Handle SQL preparation error if statement fails to prepare
        echo "<script>alert('Failed to prepare SQL statement.');</script>";
    }
    }

    if (isset($_POST['buynow'])) {
    
    echo '<script>window.location.href = "guestbuynow.php";</script>';

    $product_id = $_POST['product_id'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    session_start();
    $_SESSION['product_id'] = $product_id;
    $_SESSION['price'] = $price;
    $_SESSION['quantity'] = $quantity;


    }

    // if (isset($_POST['send'])) {
        
    //   $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
    //   $product_id = $_POST['product_id'];

    //   echo $product_id;
    // } 

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
      $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
      $product_id = $_POST['product_id'];
      $feedback = $_POST['feedback']; // Access feedback from the form

    //   echo "User ID: " . $userId . "<br>";
    //   echo "Product ID: " . $product_id . "<br>";
    //   echo "Feedback: " . $feedback . "<br>";

      $sql = "INSERT INTO item_feedback (id, product_id, feedback) VALUES (?, ?, ?)";

        // Prepare and bind the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $userId, $product_id, $feedback); // "i" for integer, "s" for string

        // Execute the query
        if ($stmt->execute()) {
            echo "Feedback submitted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
  } else {
      // This else block will only run if the form hasn't been submitted or the button is not clicked
    //   echo "Form not submitted yet or button not clicked.";
  }


?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ethereal Essence</title>

    <link rel="stylesheet" href="CSS JS/Bootstrap/css/bootstrap.min.css" />
    <link
      rel="stylesheet"
      href="CSS JS/Font Awesome/css/font-awesome.min.css"
    />
    <link rel="stylesheet" href="product.css" />


    <!-- <script>
      document.addEventListener("DOMContentLoaded", function() {
        fetch("header.php")
          .then(response => response.text())
          .then(data => {
            document.getElementById("header").innerHTML = data;
          });
  
        fetch("footer.php")
          .then(response => response.text())
          .then(data => {
            document.getElementById("footer").innerHTML = data;
          });
      });
    </script> -->

  </head>
  <body>
  
    <!-- <div id="header"></div> -->
       <!--header-->
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

        <div class="container">
            <div class="notif" id="notif" style = "display: none;">
              <div class="notif_text">
                Product added to cart successfully!
              </div>
              <div class="notif_btn">
                <button class="btn" id="btn">Okay</button>
              </div>
            </div>
        </div>


        <div class="container">
    <div class="notif2" id="notif2" style="display: none;">
        <!-- Form to send data -->
        <form method="POST">
            <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="text" id="feedback" name="feedback" placeholder="Your feedback" required autocomplete="off">
            <div class="notif_btn2 d-flex">
                <button type="submit" class="btn2" id="btn2" name ="send">Send</button>
                <button type="button" class="btn2" id="cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

        <div class="container">
            <div class="notif3" id="notif3" style = "display: none;">
              <div class="notif_text3">
               Sorry! You must log in first before sending a feedback.
              </div>
              <div class="notif_btn3">
                <button class="btn3" id="btn3">Okay</button>
              </div>
            </div>
      </div>

<div class="cover">
     
    </div>

<!-- header -->
<?php
  

    // Check if product_id is set
    if (isset($_GET['product_id'])) {
      $product_id = $_GET['product_id'];
  
      // Fetch product details based on product_id
      $sql = "SELECT product_id, product_name, description, price, image, ingredients 
        FROM product_info 
        WHERE product_id = ?";

      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $product_id);
      $stmt->execute();
      $result = $stmt->get_result();
      

      if ($result->num_rows > 0) {
          $product = $result->fetch_assoc();
  
          // Display product details
          echo '
          <div class="owo">
              <div class="pic5-wrapper">
                  <img src="' . $product['image'] . '" alt="' . $product['product_name'] . '">
              </div>
              <div class="otherinfo-wrapper">
                  <div class="info-holder">
                      <div class="product-name">' . $product['product_name'] . '</div>
                      <div class="price"><b>₱ ' . number_format($product['price'], 2) . '</b> | 100 ml</div>


                      <div class="quantity-add-to-bag-container d-flex">
                      <form method="POST">
                          <div class="quantity">
                              <!-- Decrement button -->
                              <button class="minus" type="button" aria-label="Decrease" onclick="decrement()">
                                  <span class="minus-symbol">&minus;</span>
                              </button>
        
                              <!-- Visible quantity input (readonly) -->
                              <input type="number" class="input-box" name="quantity" id="quantityInput" value="1" min="1" readonly>

                              <!-- Increment button -->
                              <button class="plus" type="button" aria-label="Increase" onclick="increment()">
                                  <span class="plus-symbol">&plus;</span>
                              </button>
                          </div>
                          <br>
                          <!-- Hidden input to store the actual quantity value -->
                          <input type="hidden" name="quantity" id="hiddenQuantity" value="1">

                          <div class="add-to-bag">
                              <!-- Hidden product ID input -->
                              <input type="hidden" name="product_id" value="' . $product['product_id'] . '">
                              <input type="hidden" name="image" value="' . $product['image'] . '">
                              <input type="hidden" name="product_name" value="' . $product['product_name'] . '">
                              <input type="hidden" name="price" value="' . $product['price'] . '">
                              <!-- Add to bag and Buy Now buttons -->
                              <button type="submit" class="addtobag" name="addtobag">Add to bag</button>


                              <button type="submit" class="buynow" name="buynow">Buy Now</button>
                          </div>
                      </form>
                  </div>
                      

                    

                      <!-- description -->
                      <div class="product-details-border">
                          <div class="product-detail">
                              <h1 class="product">PRODUCT DETAILS</h1>
                              <span style="font-size: 13px;">' . $product['description'] . '</span>
                          </div>
                      </div>
  
                      <!-- ingredients -->
                      <div class="ingredients-border">
                          <div class="product-detail">
                              <h1 class="product">INGREDIENTS</h1>
                              <span style="font-size: 13px;">' . $product['ingredients'] . '</span>
                          </div>
                      </div>
  
                      
                  </div>
              </div>
          </div>';
      } else {
          echo "<p>Product not found.</p>";
      }
  
      $stmt->close();
  } else {
      echo "<p>No product ID provided.</p>";
  }
?>
    <!--  
    <div class="related-container">
      <div class="related">
      <h1>YOU MAY ALSO LIKE</h1>
      </div>
      <div class="product-wrapper d-flex">

       first item
       -->
  <?php
   
    //   // $sql = "SELECT image, product_name, price, description FROM product_info ORDER BY RAND() LIMIT 3";
    //   $result = $conn->query($sql);

    //   if($result->num_rows > 0){
    //     while ($row = $result->fetch_assoc()) {
    //       echo '
    //       <div class="">
    //           <a href="" style = "text-decoration:none; color: black;">
    //               <img src="' . $row['image'] . '" alt="" width="300px" height="400px" style="display: block; margin: 0 auto;" />

    //               <p class="related-brand" style = "font-weight: bold; text-align:center">' . $row['product_name'] . '</p>
    //               <p class="tobaco-desc" style = "width: 400px;  word-wrap: break-word;">' . $row['description'] . '</p>
    //               <p class="price">P ' . number_format($row['price'], 2) . '</p>
    //           </a>
    //           <div class="tobaco-add-to-bag">
    //               <button class="addtobag">Add to bag</button>
    //           </div>
    //       </div>';
    //   }  
    //   } else {
    //     echo "No products available.";
    // }
    
    ?> 


  

  </div>
  </div>

  <div class="review-grid d-flex">

    <button class="write-a-review" id = "feedbackbtn">
      GIVE FEEDBACK 
    </button>
  </div>
  <div class="review-holder">


 
  

  <div class="review">
    FEEDBACKS
  </div>

  <?php

$sql = "SELECT feedback, id, feedback_id FROM item_feedback WHERE product_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id); // Bind the product_id parameter

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Output the feedback for each entry
    while ($row = $result->fetch_assoc()) {
        // Get user ID from feedback
        $userId = $row['id'];
        $feedbackId = $row['feedback_id']; // Store the feedback_id

        // Query to get user's first and last name
        $userSql = "SELECT fname, lname FROM user_info WHERE id = ?";
        $userStmt = $conn->prepare($userSql);
        $userStmt->bind_param("i", $userId); // Bind the user ID to the query
        $userStmt->execute();
        $userResult = $userStmt->get_result();

        // Fetch the user's name
        if ($userResult->num_rows > 0) {
            $userData = $userResult->fetch_assoc();
            $fname = $userData['fname'];
            $lname = $userData['lname'];
        } else {
            // If no user data is found, set default values
            $fname = "Unknown";
            $lname = "User";
        }

        // Query to get admin reply from the admin_feedback table using feedback_id
        $adminSql = "SELECT admin_reply FROM admin_feedback WHERE feedback_id = ?";
        $adminStmt = $conn->prepare($adminSql);
        $adminStmt->bind_param("i", $feedbackId); // Bind the feedback_id to the query
        $adminStmt->execute();
        $adminResult = $adminStmt->get_result();

        // Fetch the admin reply
        if ($adminResult->num_rows > 0) {
            $adminData = $adminResult->fetch_assoc();
            $adminReply = $adminData['admin_reply'];
        } else {
            // If no admin reply is found, set a default message
            $adminReply = "No reply yet.";
        }

        // Display the feedback with the user's name and admin reply
        echo '<div class="review">';
        echo 'From user: ' . htmlspecialchars($fname) . ' ' . htmlspecialchars($lname) . '<p>' . htmlspecialchars($row['feedback']) . '</p>';
        echo 'Admin reply: <p>' . htmlspecialchars($adminReply) . '</p>';
        echo '</div>';

        // Close user and admin statement
        $userStmt->close();
        $adminStmt->close();
    }
} else {
    echo '<div class="review">';
    echo "No feedback available for this product.";
    echo '</div>';
}

// Close the main statement
$stmt->close();

?>
 
  </div>






    
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

    <script src="script.js"></script>

    <script>
    // Increment the quantity
    function increment() {
        const input = document.getElementById('quantityInput');
        const hiddenInput = document.getElementById('hiddenQuantity');
        input.value = parseInt(input.value) + 1; // Increase value by 1
        hiddenInput.value = input.value; // Update hidden input with new value
    }

    // Decrement the quantity
    function decrement() {
        const input = document.getElementById('quantityInput');
        const hiddenInput = document.getElementById('hiddenQuantity');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1; // Decrease value by 1 but not below 1
            hiddenInput.value = input.value; // Update hidden input with new value
        }
    }

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
            const okbtn = document.getElementById('cancel');
            const notifdiv = document.getElementById('notif2');

            if (okbtn) {
                okbtn.addEventListener('click', function () {
                   notifdiv.style.display = 'none';
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const okbtn = document.getElementById('feedbackbtn');
            const notifdiv = document.getElementById('notif2');

            if (okbtn) {
                okbtn.addEventListener('click', function () {
                  var showNotif = "<?php echo $showNotif; ?>";

// Show the appropriate notification
if (showNotif === "notif2") {
    document.getElementById('notif2').style.display = 'block'; // Show notif2
} else if (showNotif === "notif3") {
    document.getElementById('notif3').style.display = 'block'; // Show notif3
}
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

       

</script>

  </body>
</html>