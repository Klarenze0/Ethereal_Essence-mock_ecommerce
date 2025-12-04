<?php

include ("server.php");


session_start();

$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if ($userId) {
    // echo "The user ID is: " . $userId;
    echo "<script>
    // Use window.onload to ensure the document is fully loaded
    window.onload = function() {
        document.getElementById('logoutLink').style.display = 'block';
    };
  </script>";
} else {
    // echo "User ID is not set or is empty.";
    echo "<script>
            window.onload = function() {
                document.getElementById('logoutLink').style.display = 'none';
            };
          </script>";
}


    // Registration Logic
    if (isset($_POST['register'])) {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $address = $_POST['address'];
        $number = $_POST['number'];
        $email = $_POST['email'];
        $password = $_POST['password'];


        // if(isset($_POST['g-recaptcha-response']) && !empty(($_POST['g-recaptcha-response']))) {

        //     var_dump($_POST);

        //     $secretkey = '6LdByoEqAAAAAMpjuOE5jbOm7rouN3WTp6zIUYZr';

        //     $ip = $_SERVER['REMOTE_ADDR'];

        //     $captcha = $_POST['g-recaptcha-response'];

        //     $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretkey&response=$captcha&remoteip=$ip");

        //     var_dump($response);
        //     $arr = json_decode($response, true);

            
        //     if ($arr['success']) {
        //         // CAPTCHA is valid
        //         // Proceed with PHPMailer or other actions
        //         $mail = new PHPMailer(true);
        //     }
        //     // $verifyRespone = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretkey.'&response='.($_POST['g-recaptcha-response']));

        //     // $response = json_decode($verifyRespone);

        //     // if($response->success){
        //     //     $mail = new PHPMailer(true);
        //     // }
        // } else{
        //     echo 'reCAPTCHA verification failed. Please try again.';
        //     exit;
    
        // }

        // Check if email already exists
        $sql = "SELECT * FROM user_info WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email); // Bind the email as a parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email already exists
            echo '<script>
                    // Define the delayedRedirect function
                    function delayedRedirect(url, delay) {
                        setTimeout(function() {
                            window.location.href = url;
                        }, delay);
                    }

                    // Display the error message and trigger the delayed redirect
                    window.onload = function() {
                        document.getElementById("error").style.display = "inline"; // Show the error message
                        delayedRedirect("register.php", 1000); // Redirect after 3 seconds
                    };
                </script>';


                // echo '<script> alert("Email already exist."); </script>';
        } else {
            // Email doesn't exist, proceed with registration
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

            $sql = "INSERT INTO user_info (fname, lname, address, phone_no, email, password) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $fname, $lname, $address, $number, $email, $hashedPassword);

            if ($stmt->execute()) {
                // Registration successful

                echo '<script>
                // Define the delayedRedirect function
                function delayedRedirect(url, delay) {
                    setTimeout(function() {
                        window.location.href = url;
                    }, delay);
                }

                // Display the error message and trigger the delayed redirect
                window.onload = function() {
                    document.getElementById("notif").style.display = "inline"; // Show the error message
                    delayedRedirect("login.php", 3000); // Redirect after 3 seconds
                };
            </script>';
                // echo '<script> alert("Registration successful."); window.location.href = "login.php"; </script>';
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

            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <link rel="stylesheet" href="CSS JS/Bootstrap/css/bootstrap.min.css" />
    <link
      rel="stylesheet"
      href="CSS JS/Font Awesome/css/font-awesome.min.css"
    />
        <link rel="stylesheet" href="register.css">
        <!-- <link rel="stylesheet" href="login.css"> -->

        <script>

        function enablebtn() {
             document.getElementById("register").disabled = false;
        }

      </script>
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
                <a href="register.php"> <i class="profile fa fa-user-o"></i> </a>
                <a href="guestbag.php"> <i class="fa fa-shopping-bag"></i> </a>
                <a href="logout.php" id="logoutLink" style="display: block;" name="logout">Log out</a>
                
            </div>
            </div>
        </div>

<div class="cover">
     
    </div>

        <div class="container">
                <div class="notif" id="notif" style = "display: none;">
                <div class="notif_text">
                Registration successful.
                </div>
                <div class="notif_btn">
                    <button class="btn" id="btn">Okay</button>
                </div>
                </div>
        </div>
        
    <div class="container">
        <div class="wrapper" id="wrapper">


        <!-- register -->
        <div class="bg register">
            <h2>Register</h2>
            <form  method= "POST" action="" >
                <div class="name d-flex">
                    <div class="input-box fname">
                        <span class="icon fa fa-user"></span>
                        <input type="text" name="fname" id="" required>
                        <label for="">First Name</label>
                    </div>
                    
                        <div class="input-box lname">
                        <span class="icon fa fa-user"></span>
                        <input type="text" name="lname" id="" required>
                        <label for="">Last Name</label>
                    </div>
                </div>
                <div class="input-box">
                    <span class="icon fa fa-globe"></span>
                    <input type="text" name="address" id="" required>
                    <label for="">Address</label>
                </div>
                <div class="input-box">
                    <span class="icon fa fa-phone"></span>
                    <input type="text" name="number" id="" required>
                    <label for="">Phone Number</label>
                </div>
                <div class="input-box">
                    <span class="icon fa fa-envelope"></span>
                    <input type="email" name="email" id="" required>
                    <label for="">Email <span class = "error" id="error" style = "display: none; color:red;">*Email already exist</span></label>
                </div>
                <div class="input-box">
                    <span class="icon fa fa-envelope"></span>
                    <input type="password" name="password" id="" required>
                    <label for="">Password</label>
                </div>

                <div class="captcha-btn d-flex">
                <div class="g-recaptcha" data-sitekey="6LdByoEqAAAAAPXF2E1jJKhdpnO3As0bdhtrbjNR" data-callback="enablebtn"></div>
                    <button class="btn" name= "register" disabled = "disabled" id= "register">Register</button>
                </div>
                <div class="login-register">
                    <p>Already have an account?</p>
                    <a href="login.php" class="login-link">Login</a>    
                </div>
                
            </form>
        </div>
    </div>
    </div>
    
    <div id="footer"></div>

    <script src="footerheader.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const okbtn = document.getElementById('btn');
            const notifdiv = document.getElementById('notif');

            if (okbtn) {
                okbtn.addEventListener('click', function () {
                   notifdiv.style.display = 'none';
                   window.location.href = "login.php";
                });
            }
        });
      </script>
</body>
</html>