<?php

include ("server.php");

$fname = $_POST['fname'];
$lname = $_POST['lname'];
$address = $_POST['address'];
$number = $_POST['number'];
$email = $_POST['email'];
$password = $_POST['password'];


if(isset($_POST['register'])){

    //normal setter
    //notif pag nakaapg register na
    //notif pag may kamukang email
    // Check if the email already exists

    $sql = "SELECT * FROM user_info WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // Bind the email as a string parameter
    $stmt->execute();
    $result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Email exists, show alert
    echo '<script>
        const text = document.getElementById("error");
        text.style.display = "inline";
    </script>';
    echo '<script> alert("Email already exist."); </script>';
      
    exit();
} else {
    // Email doesn't exist, proceed with registration
    $sql = "INSERT INTO user_info (fname, lname, address, phone_no, email, password)
            VALUES ('$fname', '$lname', '$address', '$number', '$email', '$password')";

    if ($conn->query($sql) === true) {
        echo '<script> alert("Registration successful."); </script>';
        header("Location: registersignin.php");
        exit();
    } else {
        echo '<script> alert("Registration failed."); </script>';
        header("Location: registersignin.html?wrapper=active#");
        exit();
    }
}

$email = mysqlo_query($conn, "SELECT * FROM user_info WHERE email = $email");



}

    //login
    //notif pag wrong password/email
    if(isset($_POST['login'])){
        if(isset($_POST['email'], $_POST['password'])){

            $sql = "SELECT * FROM user_info
            WHERE email = '$email' && password = '$password'";

        $result = $conn->query($sql);
        
        if($result->num_rows > 0){
         echo  '<script> window.location.href = "profile.php" </script>';
         exit();
        } else {
            
        //  echo '<script> alert("Login Failed (Change this later)") </script>';
        echo '<script>
                    document.querySelector(".login_error").style.display = "inline";
                    alert("Login Failed (Change this later)");
                    // Delay the redirect by 2 seconds (2000 milliseconds)
                    setTimeout(function() {
                        window.location.href = "registersignin.php";
                    }, 2000);  // 2 seconds delay
             </script>';
            exit();
        //  echo '<script>
        //             document.querySelector(".login_error").style.display = "inline";
        //           </script>';
         
        //  echo  '<script> window.location.href = "registersignin.php" </script>';
        //  exit();
        }
        }
        
    }

    // prepared statement
//     $sql = "INSERT INTO user_info (fname, lname, address, phone_no, email, password)
//     VALUES (?, ?, ?, ?, ?, ?)";

//     $stmt = mysqli_stmt_init($conn);

//     if (! mysqli_stmt_init($stmt, $sql)){
//         die(mysql_error($conn));
//     }
//     mysqli_stmt_bind_param($stmt, "ssssss", $fname, $lname, $address, $number, $email, $hashed_password);
//     mysqli_stmt_execute($stmt);
//     echo "Registration successful!";



?>