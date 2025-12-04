<?php

include ("server.php");


$loginError = "";
$registrationError = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Registration Logic
    if (isset($_POST['register'])) {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $address = $_POST['address'];
        $number = $_POST['number'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if email already exists
        $sql = "SELECT * FROM user_info WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email); // Bind the email as a parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email already exists
            
        } else {
            // Email doesn't exist, proceed with registration
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

            $sql = "INSERT INTO user_info (fname, lname, address, phone_no, email, password) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $fname, $lname, $address, $number, $email, $hashedPassword);

            if ($stmt->execute()) {
                // Registration successful
                echo '<script> alert("Registration successful."); window.location.href = "registersignin.php"; </script>';
            } else {
                // Registration failed
                echo '<script> alert("Registration failed. Please try again."); </script>';
            }
        }
    }

    // Login Logic
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if email exists in the database
        $sql = "SELECT * FROM user_info WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email); // Bind the email as a parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email exists, check password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password is correct
                echo '<script> window.location.href = "profile.php"; </script>';
            } else {
                $loginError = "Invalid email or password.";
            }
        } else {
            // Email does not exist
            $loginError = "Invalid email or password.";
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

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
      <link rel="stylesheet" href="registersignin.css">
</head>
<body onload="loaded()">

    <div id="header"></div>

    <div class="cover">
    </div>

    <div class="container">
        <div class="wrapper" id="wrapper">
            <!-- login -->
        <div class="bg login" id="login">
            <h2>Login</h2>
            <?php if ($loginError): ?>
                    <p style="text-align: center; color: red;" class="login_error"><?= $loginError ?></p>
                <?php endif; ?>
            <form method="POST" action="" >
                <div class="input-box">
                    <span class="icon fa fa-envelope"></span>
                    <input type="email" name="email" id="" required>
                    <label for="">Email</label>
                </div>
                <div class="input-box">
                    <span class="icon fa fa-envelope"></span>
                    <input type="password" name="password" id="" required>
                    <label for="">Password</label>
                </div>
                <div class="remember-forgot">
                    <label for=""><input type="checkbox" name="" id=""> Remember me</label>
                    <a href="#">Forgot Password?</a>
                </div>
                <button class="btn" name="login" id="login">Login</button>
                <div class="login-register">
                    <p>Don't have an account?</p>
                    <a href="#" class="register-link">Register</a>    
                </div>
            </form>
        </div>

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
                    <span class="icon fa fa-envelope"></span>
                    <input type="text" name="number" id="" required>
                    <label for="">Phone Number</label>
                </div>
                <div class="input-box">
                    <span class="icon fa fa-envelope"></span>
                    <input type="email" name="email" id="" required>
                    <label for="">Email <span class = "error" id="error">*Email already exist</span></label>
                </div>
                <div class="input-box">
                    <span class="icon fa fa-envelope"></span>
                    <input type="password" name="password" id="" requiredd>
                    <label for="">Password</label>
                </div>

                <div class="captcha-btn d-flex">
                    <div class="g-recaptcha" data-sitekey="6Lfhn30qAAAAAOqDDzsL6DiTm7hKfySu1WitvRUt"></div>
                    <button class="btn" name= "register" id= "register">Register</button>
                </div>
                <div class="login-register">
                    <p>Already have an account?</p>
                    <a href="#" class="login-link">Login</a>    
                </div>
                
            </form>
        </div>
    </div>
    </div>
    
    <script src="registersignin.js"></script>
</body>
</html>