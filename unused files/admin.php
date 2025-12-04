<?php

include("server.php");

if(isset($_POST['id'])){
    $id = intval($_POST['id']);

    $delete = "DELETE FROM `user_info` WHERE id = $id";
    $delete_run = mysqli_query($conn, $delete);

    if($delete_run){
        echo '<script> alert("Deleted successfully.") </script>';
    } else{
        echo '<script> alert("Error: Could not delete the account.") </script>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ethereal Essence</title>

    <script>
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
    </script>



<link rel="stylesheet" href="CSS JS/Bootstrap/css/bootstrap.min.css" />
<link
  rel="stylesheet"
  href="CSS JS/Font Awesome/css/font-awesome.min.css"
/>

<link rel="stylesheet" href="admin.css">
</head>
<body>
    
    <div id="header"></div>

    <aside id="sidebar">
       
        <ul>
            
               <img src="IMAGES/others/Klarenze.png" alt="" width="100px">
            
            <li class="active">
                <a href="" id="orders">Orders</a>
                <span></span>
            </li>
            <li class="active">
                <a href="" id="products">Products</a>
                <span></span>
            </li>
            <li class="active">
                <a href="" id="feedbacks1">Feedback</a>
                <span></span>
            </li>
            <li class="active">
                <a href="" id="customers_account_list1">Customer's Accounts</a>
                <span></span>
            </li>
        </ul>
    </aside>
    
    <main>
        <div class="content">
            <div class="topvar d-flex">
               <div class="container">
                <div class="search d-flex">
                   
                        <input type="text" placeholder="Search Product/Item" class="bar" />
                        <!-- <span class="fa fa-search"></span> -->
                   
                   
                        <div class="admin">
                            <span>Admin</span>
                        <i class="fa fa-fa-door"></i><a href="">Log out</a>
                        </div>             
               </div>
               </div>
               </div>
              <div class="lists panel">
                <!-- ORDER LIST -->
                <div class="visible" id="order_list">

                    <h2>Order List</h2>

                </div>

                <!-- PRODUCT LIST -->
                <div class="hidden product panel" id="product_list">
                    <h2>Product List</h2>
                    <div class="product_details d-flex">
                        <p><strong>Product ID</strong></p>
                        <p><strong>Product Name</strong></p>
                        <p><strong>Product Description</strong></p>
                        <p><strong>Stocks</strong></p>
                        <p><strong>Price</strong></p>
                        <p><strong>Edit Product</strong></p>
                        
                    </div>
                    <div class="line"></div>
                </div>

                <!-- FEEDBACKS -->
                <div class="hidden panel" id="feedbacks2">
                    <h2>Feedbacks</h2>
                    
                </div>

                <!-- CUSTOMER'S ACCOUNT -->
                <?php
                        $sql = "SELECT id, fname, lname, address, phone_no, email FROM user_info";
                        $result = $conn->query($sql);
                        // <div id="account_details"></div>
                        
                        echo '<div class="hidden customer panel" id="customers_account_list2">
                                 <h2>Customer\'s Accounts</h2>
                                 <div class="account_details d-flex">
                                 <p><strong>Customer ID</strong></p>
                                 <p><strong>Name</strong></p>
                                 <p><strong>Address</strong></p>
                                 <p><strong>Phone No.</strong></p>
                                 <p><strong>Email</strong></p>
                                 <p></p>
                         </div>
                         <div class="line"></div>';
                        while($row = $result-> fetch_assoc()){
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="account_details d-flex">
                                    <div>' . $row['id'] . '</div>
                                    <div>' . $row['fname'] . ' ' . $row['lname'] . '</div>
                                    <div>' . $row['address'] . '</div>
                                    <div>' . $row['phone_no'] . '</div>
                                    <div>' . $row['email'] . '</div>
                                    <form method="POST"> 
                                    <input type="hidden" name="id" value="' . $row['id'] . '">
                                   <button type="submit" class="view-button">X</button>
                                   </form>
                                </div>
                                <div class="line"></div>';
                                
                            }
                        }
                        echo '</div>';
                    ?>
               

                   
                    
                    
                </div>
            </div>
        </div>
        
        
    </main>

    <script src="admin.js"></script>
</body>
</html>