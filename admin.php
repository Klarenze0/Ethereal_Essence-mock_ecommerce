<?php

include("server.php");

session_start();

if(isset($_POST['id'])){
    $id = intval($_POST['id']); 

    $update = "UPDATE `user_info` SET status = 0 WHERE id = $id";
    $update_run = mysqli_query($conn, $update);


}





if (isset($_POST['edit'])) {
    $productId = $_POST['productid'];
    $image = $_POST['image'];

    $_SESSION['productid'] = $productId;
    $_SESSION['image'] = $image;
    header('Location: editproduct.php?id=' . $productId);
    exit();
}

if (isset($_POST['send'])) {

    $productId = $_POST['product_id'];
    $feedbackId = $_POST['feedback_id'];
    $reply = $_POST['feedback'];

    $sql = "INSERT INTO admin_feedback (feedback_id, admin_reply) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $feedbackId, $reply);
    
    if ($stmt->execute()) {
        echo "Reply submitted successfully!";
    } else {
        echo "Error submitting reply.";
    }

    $stmt->close();
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>

<link rel="stylesheet" href="CSS JS/Bootstrap/css/bootstrap.min.css" />
<link
  rel="stylesheet"
  href="CSS JS/Font Awesome/css/font-awesome.min.css"
/>

<link rel="stylesheet" href="admin.css" />
</head>
<body>
    
    <aside id="sidebar" >
       
        <ul style = "margin-top: 100px">
            
            <div class="admin">
                ADMIN
            </div>
            
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
         <a href="logout.php">
            <button class = "logout"> Log out </button>    
            </a>   
    </aside>
    
    <main>
        <div class="content" style = "margin-top: -850px">
              <div class="lists panel">

              
                <div class="visible order" id="order_list">
                <h2 style="margin-top: 50px;">Order History</h2>
                <button style = "width: 150px" id = "orderpdf"> Print PDF </button>
                <a href="admin.php">
                <button style = "width: 150px"> Refesh </button>
                </a>
                <div id='order_content'>
<div class="order_details d-flex">
    <div><strong>Order ID</strong></div>
    <div><strong>Customer ID</strong></div>
    <div><strong>Product Name</strong></div>
    <div><strong>Address</strong></div>
    <div><strong>Date</strong></div>
    <div><strong>Quantity</strong></div>
    <div><strong>Price</strong></div>
    
</div>

<?php
$order_sql = "SELECT order_id, customer_id, product_name, address, date, quantity, price FROM purchase_history";
if ($order_result = $conn->query($order_sql)) {

   
    while ($order_data = $order_result->fetch_assoc()) {
        echo "<div class='order_details d-flex'>";
        echo "<div>" . htmlspecialchars($order_data['order_id']) . "</div>";
        echo "<div>" . htmlspecialchars($order_data['customer_id']) . "</div>";
        echo "<div>" . htmlspecialchars($order_data['product_name']) . "</div>";
        echo "<div>" . htmlspecialchars($order_data['address']) . "</div>";
        echo "<div>" . htmlspecialchars($order_data['date']) . "</div>";
        echo "<div>" . htmlspecialchars($order_data['quantity']) . "</div>";
        echo "<div>" . htmlspecialchars($order_data['price']) . "</div>";
        echo "</div>";
    }
} else {
    echo "<p>No orders found.</p>";
}
?>
</div>
                </div>

                
                <div class="hidden product panel" id="product_list">
                                <h2 style="margin-top: 50px;">Products</h2>
                                <div class = "d-flex" style = "gap: 10px"> 
                                <a href = "addproduct.php">
                                <button style = "width: 150px"> Add product </button>
                                </a>
                                <button style = "width: 150px" id = "productpdf"> Print PDF </button>
                                </div>
                                <div id="product_content">
                                <div class="product_details d-flex">
                                    <div><strong>Product ID</strong></div>
                                    <div><strong>Product Name</strong></div>
                                    <div><strong>Category</strong></div>
                                    <div><strong>Description</strong></div>
                                    <div><strong>Stocks</strong></div>
                                    <div><strong>Price</strong></div>
                                    <div></div>
                                </div>
                                <div class="line"></div>

                
                <?php
                    $query = "SELECT * FROM product_info";
                    $result = mysqli_query($conn, $query);

                    if(mysqli_num_rows($result) > 0) {
                        
                        
                    while($row = mysqli_fetch_assoc($result)) {
                        
                            echo '
                            
                                <form method="POST"> 
                            <div class="product_details sub d-flex">
                            
                                    <div>' . $row['product_id'] . '</div>
                                    <div>' . $row['product_name'] . '</div>
                                    

                                    <div>' . $row['category'] . '</div>
                                    <div>' . $row['description'] . '</div>
                                    <div>' . $row['stocks'] . '</div>
                                    <div>' . $row['price'] . '</div>
                                    
                                    <i class="button-container">
                                        <button type="button" id="edit_' . $row['product_id'] . '" class="view-button edit-btn">View</button>
                                        <input type="hidden" name="productid" value="' . $row['product_id'] . '">
                                        <input type="hidden" name="image" value="' . $row['image'] . '"> 
                                        <button type="submit" id="delete_' . $row['product_id'] . '" class="view-button delete-btn" name="edit">Edit</button>
                                    </i>
                                
                                </div>
                                <div class="line"></div>
                                </form>';
                        }
                        
                        echo '</div>';
                    } else {
                        echo "No products found.";
                    }

                    ?>
    </div>
                
                

                <div class="hidden feedback panel" id="feedbacks2">
                    <h2 style="margin-top: 50px;">Feedbacks</h2>
                    <div class="feedback_details d-flex">
                        <div><strong>Feedback ID</strong></div>
                        <div><strong>Customer ID</strong></div>
                        <div><strong>Customer Name</strong></div>
                        <div><strong>Product ID</strong></div>
                        <div><strong>Feedback</strong></div>
                        <i></i>
                    </div>


                    <?php

$sql = "SELECT * FROM item_feedback";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userId = $row['id']; 
        $userSql = "SELECT fname, lname FROM user_info WHERE id = ?";
        $userStmt = $conn->prepare($userSql);
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();

        if ($userResult->num_rows > 0) {
            $userData = $userResult->fetch_assoc();
            $fname = htmlspecialchars($userData['fname']);
            $lname = htmlspecialchars($userData['lname']);
        } else {
            $fname = "Unknown";
            $lname = "User";
        }

        echo '
        <div class="feedback_details d-flex">
            <div>' . htmlspecialchars($row['feedback_id']) . '</div>
            <div>' . htmlspecialchars($userId) . '</div>
            <div>' . $fname . ' ' . $lname . '</div>
            <div>' . htmlspecialchars($row['product_id']) . '</div>
            <div class="fb">' . htmlspecialchars($row['feedback']) . '</div>
            
            <i class="view-button">
                <button type="button" class="view-button see-btn reply-btn" data-feedback-id="' . htmlspecialchars($row['feedback_id']) . '">Reply</button>
            </i>
        </div>
        <div class="line"></div>
        <div class="notif2" id="notif2_' . htmlspecialchars($row['feedback_id']) . '" style="display: none;">
            <form method="POST">
                <input type="hidden" name="user_id" value="' . htmlspecialchars($userId) . '">
                <input type="hidden" name="product_id" value="' . htmlspecialchars($row['product_id']) . '">
                <input type="hidden" name="feedback_id" value="' . htmlspecialchars($row['feedback_id']) . '">
                <input type="text" id="feedback" name="feedback" placeholder="Reply" required autocomplete="off">
                <div class="notif_btn2 d-flex">
                    <button type="submit" class="btn2" name="send">Send</button>
                    <button type="button" class="btn2 cancel-btn">Cancel</button>
                </div>
            </form>
        </div>';
    }
} else {
    echo '<div class="feedback_details d-flex">No feedback available.</div>';
}

$stmt->close();
$userStmt->close();
?>


                </div>
                
               
                <div class="container">
    <div class="notif2" id="notif2" style="display: none;">
        <form method="POST">
            <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="text" id="feedback" name="feedback" placeholder="Reply" required autocomplete="off">
            <div class="notif_btn2 d-flex">
                <button type="submit" class="btn2" id="btn2" name ="send">Send</button>
                <button type="button" class="btn2" id="cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>
                


        <div class="hidden customer panel" id="customers_account_list2">
                <h2 style="margin-top: 50px;">Customer's Accounts</h2>
                <div class="div">
                <a href = "#guest_account" style = "text-decoration: none;">
                <button id="viewGuestsBtn" style="margin-left: 0px; width: 150px">View Guests</button>
                </a>
                <button style="margin-left: 0px; width: 150px" id = "customerpdf">Print PDF</button>

                </div>
                
                <div id="customer_content">

                <div id="account_details" class="account_details d-flex">
                <p><strong>Customer ID</strong></p>
                <p><strong>Name</strong></p>
                <p><strong>Address</strong></p>
                <p><strong>Phone No.</strong></p>
                <p><strong>Email</strong></p>
                <p></p>
            </div>
            <div class="line"></div>
            <?php
        $sql = "SELECT id, fname, lname, address, phone_no, email FROM user_info";
        $result = $conn->query($sql);

        

        while($row = $result->fetch_assoc()) {
            echo '<div class="account_details d-flex" id="account_details">
                    <div>' . $row['id'] . '</div>
                    <div>' . $row['fname'] . ' ' . $row['lname'] . '</div>
                    <div>' . $row['address'] . '</div>
                    <div>' . $row['phone_no'] . '</div>
                    <div>' . $row['email'] . '</div>
                    <form method="POST"> 
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <button type="submit" name="disable" class="disable" id="disable">Disable</button>
                    </form>
                </div>
                <div class="line"></div>';
        }

        $guest_sql = "SELECT guest_email, guest_name FROM guest_info"; 
        $guest_result = $conn->query($guest_sql);
    
        echo '<div class="guest_account" id="guest_account">
        <h2>Guest\'s Accounts</h2> 
        <div id="account_details" class="guest_details">
            <p><strong>Guest Name</strong></p>
            <p><strong>Email</strong></p>
        </div>
        <div class="line"></div>';

if ($guest_result->num_rows > 0) {
    while ($guest_row = $guest_result->fetch_assoc()) {
        echo '
        <div class="guest_details"> 
            <div class="guestname">' . htmlspecialchars($guest_row['guest_name']) . '</div>
            <div class="email">' . htmlspecialchars($guest_row['guest_email']) . '</div>
        </div>
        <div class="line"></div>';
    }
} else {
    echo '<div class="guest_details">No guests found.</div>';
}

echo '</div>'; 


?>
</div>
                

                
            </div>
        </div>
        
       
    </main>

    <div id="footer"></div>
    <script src="admin.js"></script>

    <script>

document.addEventListener('DOMContentLoaded', function () {
            const okbtn = document.getElementById('btn2');
            const notifdiv = document.getElementById('notif2');

            if (okbtn) {
                okbtn.addEventListener('click', function () {
                   notifdiv.style.display = 'none';
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.reply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const feedbackId = this.getAttribute('data-feedback-id');

            document.querySelectorAll('.notif2').forEach(form => {
                form.style.display = 'none';
            });

            const replyForm = document.getElementById('notif2_' + feedbackId);
            if (replyForm) {
                replyForm.style.display = 'block';
            }
        });
    });

    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function() {
            const replyForm = this.closest('.notif2');
            if (replyForm) {
                replyForm.style.display = 'none';
            }
        });
    });
});

document.getElementById("orderpdf").addEventListener("click", (e) => {
    e.preventDefault(); 

    const orderContent = document.getElementById("order_content");

    var opt = {
    margin:       10,  
    filename:     'order_details.pdf',  
    image:        { type: 'jpeg', quality: 0.98 },  
    html2canvas:  { scale: 2 },  
    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }  
};


    html2pdf().from(orderContent).set(opt).save();
});


document.getElementById("productpdf").addEventListener("click", () => {
    const productContent = document.getElementById("product_content"); 
    const buttons = productContent.querySelectorAll("button");  

    buttons.forEach(button => button.style.display = "none");

    var opt = {
        margin:       10,
        filename:     'product_details.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };

    html2pdf().from(productContent).set(opt).save();

    setTimeout(() => {
        buttons.forEach(button => button.style.display = "");  
    }, 1000); 
});


document.getElementById("customerpdf").addEventListener("click", () => {
    const customerContent = document.getElementById("customer_content");
    const buttons = customerContent.querySelectorAll("button"); 

    buttons.forEach(button => button.style.display = "none");

    var opt = {
        margin:       10,
        filename:     'customer_details.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().from(customerContent).set(opt).save();

    setTimeout(() => {
        buttons.forEach(button => button.style.display = "");  
    }, 1000); 
});






    </script>
</body>
</html>