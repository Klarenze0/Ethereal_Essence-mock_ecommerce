<?php

include ("server.php");



session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php"); // Redirect to login if user is not logged in
    exit;
}

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

$userId = $_SESSION['id'];

// Fetch user details from the database based on the user_id
$sql = "SELECT * FROM user_info WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId); // Bind user_id as an integer parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch user data
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

if(isset($_POST['save'])){
  // Get updated user information from the form
  $firstName = $_POST['fname'];
  $lastName = $_POST['lname'];
  $address = $_POST['address'];
  $email = $_POST['email'];
  $phone = $_POST['phone_no'];
  $image = $_POST['image'];

  // Update user details in the database
  $sql = "UPDATE user_info SET fname = ?, lname = ?, address = ?, email = ?, phone_no = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssssi", $firstName, $lastName, $address, $email, $phone, $userId); // Bind parameters

  if ($stmt->execute()) {
      // Successfully updated the profile
      echo '<script> window.location.href = "profile.php";</script>';
  } else {
      // Failed to update
      // echo '<script>alert("Failed to update profile.");</script>';
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="collection.css">
    <link rel="stylesheet" href="CSS JS/Bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="CSS JS/Font Awesome/css/font-awesome.min.css" />
    <title>Customer Profile</title>
</head>
<body>


  <!-- Header -->
  <div class="navbar" id="navbar">
      <div class="container d-flex">
        <a href="index.php" class="imgcont">
          <img src="IMAGES/others/logo.png" alt="Ethereal Essence" width="100px" class="image" />
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
  

  <!-- Profile Container -->
  <div class="container profile-container mt-5">
    <div class = "containerbg" id= "containerbg">
    <!-- Profile Section -->
    <div class="row">
      <!-- Profile Photo (Left) -->

      <div class="col-md-4 text-center taas" id="dp">
        <div class="image-container">
          <img id="profileImage" src="default-image.jpg" alt="Profile Image">
        </div>
            <div class="button-container">
            <input type="file" id="uploadFile" accept="image/*" />
            <label for="uploadFile" id="uploadLabel">Upload Photo</label>
    
            <button class="btn" id="editProfileButton">Edit Profile</button>
            </div>
      </div>

      <!-- Profile Information (Right) -->
      <div class="col-md-8 taas" id="info">
        
        <div class="profile-info">

        <form method="POST">
        <!--  -->
        <div class="form-group">
            <label for="firstName">First Name:</label>
            <p id="firstName" class="username"><?php echo htmlspecialchars($user['fname']); ?></p>
            <input type="text" id="firstNameEdit" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" style="display: none;">
        </div>

        <div class="form-group">
            <label for="lastName">Last Name:</label>
            <p id="lastName"><?php echo htmlspecialchars($user['lname']); ?></p>
            <input type="text" id="lastNameEdit" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" style="display: none;">
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <p id="address"><?php echo htmlspecialchars($user['address']); ?></p>
            <input type="text" id="addressEdit" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" style="display: none;">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <p id="email"><?php echo htmlspecialchars($user['email']); ?></p>
            <input type="email" id="emailEdit" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" style="display: none;">
        </div>

        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <p id="phone"><?php echo htmlspecialchars($user['phone_no']); ?></p>
            <input type="text" id="phoneEdit" name="phone_no" value="<?php echo htmlspecialchars($user['phone_no']); ?>" style="display: none;">
        </div>
          <!--  -->

          <!-- Save and Cancel Buttons -->
          
          <div id="saveCancelBtns" style="display: none;">
            <button type = "submit" class="btn btn-success" id="saveChangesButton" name = "save">Save Changes</button>
            <button class="btn btn-secondary" id="cancelButton">Cancel</button>
          </div>
          </form>

        </div>
      </div>
    </div>

    <!-- Purchase History -->
    <div class="purchase-history mt-5">


      <h1>Purchase History</h1>
      <?php

$history_sql = "SELECT product_id, product_name, date FROM purchase_history WHERE customer_id = ?";

// Prepare the statement
if ($history_stmt = $conn->prepare($history_sql)) {
    // Bind the userId as the parameter to the query
    $history_stmt->bind_param("i", $userId);
    
    // Execute the query
    $history_stmt->execute();
    
    // Get the result
    $history_result = $history_stmt->get_result();

    // Check if there are any rows returned
    if ($history_result->num_rows > 0) {
        // Loop through all the rows and display them
        while ($history_data = $history_result->fetch_assoc()) {
            $product_id = $history_data['product_id']; // Fetch the product_id
            $product_name = $history_data['product_name'];
            $date = $history_data['date'];

            // Display each product with the feedback button
            echo "
            <div class='history-item'>
                <div class='product_name'>$product_name</div>
                <p>Purchased on: $date</p>
                <a href='product.php?product_id=$product_id'>Give Feedback</a>
            </div>";
        }
    } else {
        echo "<p>No purchase history found for this ID.</p>";
    }
} else {
    echo "<p>Error fetching purchase history data.</p>";
}

      ?>

    </div>

    <!-- Reviews Section
    <div class="reviews mt-5">
      <h5>Reviews/Feedback</h5>
      <div class="review-item">
        <div>Product Name #1</div>

        <p>Review: Great product! Highly recommend it.</p>
      </div>
      <div class="review-item">
        <div>Product Name #2</div>
        <p>Review: Very good, but the scent doesn't last long enough.</p>
      </div>
    </div> -->

  </div> <!-- End of Profile Container -->
</div>
  <script src="profilescript.js"></script>
</body>
</html>
