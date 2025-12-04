<?php

include ("server.php");

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
  
          <div class="search">
            <a href="" class="btn"> <i class="fa fa-search"></i> </a>
            <input type="text" placeholder="Search..." class="searchbar" />
          </div>
  
          <div class="menus d-flex">
            <a href="men.php">MEN</a>
            <a href="collection.php">PERFUME COLLECTION</a>
            <a href="women.php">WOMEN</a>
          </div>
  
          <div class="icons d-flex">
            <a href="register.php"> <i class="fa fa-user-o"></i> </a>
            <a href=""> <i class="fa fa-shopping-bag"></i> </a>
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
          $sql = "SELECT image, product_name, price FROM product_info WHERE category = 'male'";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo '
                  <div class="items">
                      <a href="">
                          <img src="' . $row['image'] . '" alt="" width="175px" height="200px" />
                          <p class="perfname">' . $row['product_name'] . '</p>
                          <p class="price">P ' . number_format($row['price'], 2) . '</p>
                      </a>
                      <div class="btnholder">
                          <button class="addtobag">Add to bag</button>
                      </div>
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
            <a href="">Home</a> <br />
            <br />
            <a href="">About us</a> <br />
            <br />
            <a href="">FAQs</a> <br />
            <br />
            <a href="">Privacy and Policy</a> <br />
            <br />
            <a href="">Terms and Conditions</a> <br />
            <br />
          </div>
  
          <div class="footer-info">
            <div class="contact">Contact</div>
            <br />
            <i class="fa fa-phone"></i>
              09123456789 <br /> <br />
            
            <i class="fa fa-envelope"></i>
            sample@gmail.com <br/> <br/>
  
            <a href="" class="fa fa-facebook"></a>
            <a href="">Facebook</a><br />
            <br />
            <i class="fa fa-globe"> </i>
            Carillo Hagonoy Bulacan
          </div>
  
          <div class="footer-info">
            <label for="">SiGN UP:</label> <br>
            <input type="text" class="signup" />
            <div class="btnholder">
              <button class="shopnow">SHOP NOW</button>
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
</body>
</html>