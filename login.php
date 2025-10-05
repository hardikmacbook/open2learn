<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

// Include database connection
require_once "includes/db_connect.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){                     
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            // session_start(); // Already called above

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Redirect to index page
                            header("location: index.php");
                            exit;
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Open2Learn</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="index.css" />
    <style>
        body, html {
            margin: 0;
            padding: 0;
            /* Do not set height 100% to avoid full screen flex */
        }

        .container {
            display: flex;
            justify-content: center; /* horizontal center */
            /* removed height and vertical centering */
            margin-top: 100px; /* some top spacing */
        }

        .wrapper {
            width: 400px;
            padding: 20px;
        }

        input.form-control {
            border: 1px solid #ced4da;
            box-shadow: none;
            text-decoration: none;
        }

        input.form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
            text-decoration: none;
        }

        label {
            text-decoration: none;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .error-text {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Remove underline on header and footer links on hover */
        header a,
        footer a {
            text-decoration: none;
            color: inherit;
            transition: color 0.3s ease;
        }
        header a:hover,
        footer a:hover {
            text-decoration: none; /* no underline on hover */
            color: #007bff; /* bootstrap primary color on hover */
        }
    </style>
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="container">
        <div class="wrapper">
            <h2 class="text-center">Login</h2>
            <p class="text-center">Please fill in your credentials to login.</p>

            <?php 
            if (!empty($login_err)) {
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }        
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>" />
                    <span class="error-text"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" />
                    <span class="error-text"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" value="Login" />
                </div>
                <p class="text-center">Don't have an account? <a href="register.php">Sign up now</a>.</p>
            </form>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</body>
</html>
