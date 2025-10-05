<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
function checkLogin() {
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }
}

// Function to logout user
function logoutUser() {
    // Initialize the session
    session_start();
    
    // Unset all of the session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to index page instead of login page
    header("location: index.php");
    exit;
}
?>