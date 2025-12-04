<?php

include ("server.php");

session_start(); // Start the session

// Unset all session variables
// session_unset();

// Destroy the session
session_destroy();

// Redirect to a login page or homepage
header("Location: index.php"); // Change "login.php" to your desired redirect page
exit();

?>