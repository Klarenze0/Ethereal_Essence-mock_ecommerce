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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
            // Email exists, check account status
            $user = $result->fetch_assoc();

            // Check if the account is active (status == 1)
            if ($user['status'] == 1) {
                // Account is active, check password
                if (password_verify($password, $user['password'])) {
                    // Password is correct, store user id in session
                    $_SESSION['id'] = $user['id'];

                    if($email == "admin@gmail.com"){
                        echo '<script> window.location.href = "admin.php"; </script>';
                    } else {
                        echo '<script> window.location.href = "index.php"; </script>';
                    }
                } else {
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
                            delayedRedirect("login.php", 1000); // Redirect after 1 second
                        };
                    </script>';
                }
            } else {
                // Account is disabled (status != 1)
                echo '<script>
                    // Show the disabled message
                    window.onload = function() {
                        document.getElementById("disabled").style.display = "inline"; // Show the disabled message
                        delayedRedirect("login.php", 1000); // Redirect after 1 second
                    };
                </script>';
            }
        } else {
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
                    delayedRedirect("login.php", 1000); // Redirect after 1 second
                };
            </script>';
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

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

        <script>
            // Define the delayedRedirect function
            function delayedRedirect(url, delay) {
                // Delay the redirect by the specified time in milliseconds
                setTimeout(function() {
                    window.location.href = url; // Redirect to the URL
                }, delay);
            }
        </script>

    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="CSS JS/Bootstrap/css/bootstrap.min.css" />
    <link
      rel="stylesheet"
      href="CSS JS/Font Awesome/css/font-awesome.min.css"
    />
    <style>
        .input-box label #error{
            color: red;
            display: none;
        }

        .input-box label #disabled{
            color: red;
            display: none;
        }
    </style>
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
    <input type="text"  class="searchbar" name="search" autocomplete = "off" />
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

        <div class="cover">
    
           </div>

    
    <div class="container">
        <div class="wrapper" id="wrapper">
            <!-- login -->
        <div class="bg login" id="login">
            <h2>Login</h2>
            
            <form method="POST" action="" >
                <div class="input-box">
                    <span class="icon fa fa-envelope"></span>
                    <input type="email" name="email" id="" required>
                    <label for="">Email <span for="" id="error" >*Invald Email or Password</span> </label>
                    <label for="">Email <span for="" id="disabled" >*Account Disabled</span> </label>
                    
                </div>
                <div class="input-box">
                    <span class="icon fa fa-envelope"></span>
                    <input type="password" name="password" id="" required>
                    <label for="">Password</label>
                </div>
                <div class="remember-forgot" name = "forgot">
                    <a href="forgetpassword.php">Forgot Password?</a>
                </div>

                <button class="btn" name="login" id="login">Login</button>
                <div class="login-register">
                    <p>Don't have an account?</p>
                    <a href="register.php" class="register-link">Register</a>    
                </div>
            </form>
        </div>


    </div>
    </div>
    
     <!-- <div id="footer"></div> -->
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

    <script src="footer.js"></script>
</body>
</html>