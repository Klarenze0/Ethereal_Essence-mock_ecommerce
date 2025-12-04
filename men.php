<?php

include ("server.php");

session_start();

$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if ($userId) {
    // User is logged in, display user ID and show logout link
    // echo "The user ID is: " . $userId;
    $profileLink = 'profile.php';
    echo "<script>
    window.onload = function() {
        document.getElementById('logoutLink').style.display = 'block';
    };
  </script>";
} else {
    // User is not logged in, hide logout link
    // echo "User ID is not set or is empty.";
    $profileLink = 'login.php';
    echo "<script>
            window.onload = function() {
                document.getElementById('logoutLink').style.display = 'none';
            };
          </script>";
}

if (isset($_POST['addtobag'])) {
    // Get product_id from POST data
    $product_id = $_POST['product_id'];
    $purchase_date = date("Y-m-d");  // Set current date and time for purchase_date

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
            $stmt->bind_param("ii", $product_id, $userId);  // Bind user ID with product ID for logged-in user
        } else {
            $stmt->bind_param("i", $product_id);  // Bind only product ID for guest user
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Product exists, update the quantity
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + 1;

            // Update the product in the appropriate cart table
            $update_sql = "UPDATE $table SET quantity = ?, purchase_date = ? WHERE product_id = ?";
            if ($update_stmt = $conn->prepare($update_sql)) {
                if ($userId) {
                    $update_stmt->bind_param("isi", $new_quantity, $purchase_date, $product_id); // For logged-in user
                } else {
                    $update_stmt->bind_param("isi", $new_quantity, $purchase_date, $product_id); // For guest user
                }

                if ($update_stmt->execute()) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const notifdiv = document.getElementById('notif');
                            notifdiv.style.display = 'block';
                        });
                    </script>";
                } else {
                    // Handle failure to update
                    echo "<script>alert('Failed to add product quantity.');</script>";
                }
            }
        } else {
            // Product does not exist, insert a new row
            $insert_sql = "INSERT INTO $table (product_id, purchase_date, quantity" . ($userId ? ", id" : "") . ") VALUES (?, ?, ?" . ($userId ? ", ?" : "") . ")";
            if ($insert_stmt = $conn->prepare($insert_sql)) {
                $quantity = 1; // Set initial quantity to 1
                if ($userId) {
                    $insert_stmt->bind_param("isii", $product_id, $purchase_date, $quantity, $userId); // For logged-in user
                } else {
                    $insert_stmt->bind_param("isi", $product_id, $purchase_date, $quantity); // For guest user
                }

                if ($insert_stmt->execute()) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const notifdiv = document.getElementById('notif');
                            notifdiv.style.display = 'block';
                        });
                    </script>";
                } else {
                    // Handle failure to insert
                    echo "<script>alert('Failed to add product to cart.');</script>";
                }
            }
        }
    }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ethereal Essence</title>

    <link rel="stylesheet" href="men.css">
    <link rel="stylesheet" href="CSS JS/Bootstrap/css/bootstrap.min.css" />
    <link
    rel="stylesheet"
    href="CSS JS/Font Awesome/css/font-awesome.min.css"
    />

</head>
<body>
    
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
          <!-- <a id="profileLink" href="register.php"> <i class="fa fa-user-o"></i> </a> -->

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

      <!-- Men's Perfume -->
      
      <div class="topfavorite">
        <div class="container">
          <div class="favorite">Men's Perfumes</div>
        </div>
       
      </div>

      <div class="mensperfume" style = "height: 2500px;">
        <div class="grid-container">
        <div class="line"></div>
          <div class="container" style = "max-height: 2250px; overflow-y:scroll;
    overflow-x: hidden;">


          <?php
          // Query to fetch product data
          $sql = "SELECT product_id, image, product_name, price FROM product_info WHERE category = 'male'";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo '
                  <div class="items">
                      <a href="product.php?product_id=' . $row['product_id'] . '">
                          <img src="' . $row['image'] . '" alt="" width="175px" height="200px" />
                          <p class="perfname">' . $row['product_name'] . '</p>
                          <p class="price">P ' . number_format($row['price'], 2) . '</p>
                      </a>
                    <form method="POST">
                <!-- Hidden Inputs to pass product data -->
                <input type="hidden" name="product_id" value="' . $row['product_id'] . '">
                <input type="hidden" name="image" value="' . $row['image'] . '">
                <input type="hidden" name="product_name" value="' . $row['product_name'] . '">
                <input type="hidden" name="price" value="' . $row['price'] . '">
                
                <div class="btnholder">
                    <button type="submit" class="addtobag" name="addtobag">Add to bag</button>
                </div>
            </form>
                  </div>';
              }
          } else {
              echo "No products available.";
          }

          ?>
          
        </div>
        </div>
      </div>

          

      <!-- Footer -->

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


      <script src="products.js"></script>

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
      </script>
</body>
</html>