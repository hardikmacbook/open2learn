<?php
// Include database connection
require_once "includes/db_connect.php";

// Define variables and initialize with empty values
$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        // Check if email is valid
        if (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email address.";
        } else {
            // Check if email already exists
            $sql = "SELECT id FROM users WHERE email = ?";
            
            if ($stmt = $conn->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("s", $param_email);
                
                // Set parameters
                $param_email = trim($_POST["email"]);
                
                if ($stmt->execute()) {
                    // Store result
                    $stmt->store_result();
                    
                    if ($stmt->num_rows == 1) {
                        $email_err = "This email is already registered.";
                    } else {
                        $email = trim($_POST["email"]);
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                $stmt->close();
            }
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_username, $param_email, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page
                header("location: login.php");
                exit;
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
    <title>Sign Up - Open2Learn</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="index.css" />
    <style>
        body, html {
            margin: 0; 
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            margin-top: 100px;
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

        /* Remove underline on header and footer links */
        header a,
        footer a {
            text-decoration: none;
            color: inherit;
            transition: color 0.3s ease;
        }

        header a:hover,
        footer a:hover {
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="container">     
        <div class="wrapper">
            <h2 class="text-center mb-4">Sign Up</h2>
            <p class="text-center">Please fill this form to create an account.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input 
                        type="text" 
                        name="username" 
                        class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" 
                        value="<?php echo htmlspecialchars($username); ?>" 
                    />
                    <span class="error-text"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group">
                    <label>Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" 
                        value="<?php echo htmlspecialchars($email); ?>" 
                    />
                    <span class="error-text"><?php echo $email_err; ?></span>
                </div>  
                <div class="form-group">
                    <label>Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                        value=""
                    />
                    <span class="error-text"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" 
                        value=""
                    />
                    <span class="error-text"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" value="Submit" />
                    <input type="reset" class="btn btn-secondary ml-2" value="Reset" />
                </div>
                <p class="text-center">Already have an account? <a href="login.php">Login here</a>.</p>
            </form>
        </div>    
    </div>

    <?php include "includes/footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
