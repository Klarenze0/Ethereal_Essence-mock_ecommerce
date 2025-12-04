<?php

include ("server.php");

session_start();

$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if ($userId) {
    echo "The user ID is: " . $userId;
    echo "<script>
    // Use DOMContentLoaded to ensure the document is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('logoutLink').style.display = 'block';
    });
  </script>";
} else {
    echo "User ID is not set or is empty.";
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('logoutLink').style.display = 'none';
    });
  </script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ethereal Essence</title>

    <link rel="stylesheet" href="collection.css">
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
            <a href="login.php"> <i class="profile fa fa-user-o"></i> </a>
            <a href="guestbag.php"> <i class="fa fa-shopping-bag"></i> </a>
            <a href="logout.php" id="logoutLink" style="display: block;" name="logout">Log out</a>
            
          </div>
        </div>
      </div>


</body>
</html>