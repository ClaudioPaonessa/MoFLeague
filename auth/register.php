<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: /");
    exit;
}
 
// Include config file
require '../db/pdo.php';
 
// Define variables and initialize with empty values
$username = $password = $display_name = "";
$username_err = $password_err = $display_name_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["display_name"]))){
        $display_name_err = "Please enter your display name.";
    } else{
        $display_name = trim($_POST["display_name"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err) && empty($display_name_err)){
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = 'INSERT INTO accounts (account_name, display_name, account_passwd) VALUES (:username, :display_name, :passwd)';
        $values = [':username' => $username, ':display_name' => $display_name, ':passwd' => $hash];

        try
        {
            $res = $pdo->prepare($sql);
            $res->execute($values);

            header("location: /auth/login");
        }
        catch (PDOException $e)
        {
            echo 'Query error.';
            die();
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MoF - Register</title>
    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous" />
    <!-- Custom styles for this template -->
    <link href="../custom.css" rel="stylesheet" />
    
    <style type="text/css">
        body {
            background-color: #4D4D4D;
        }
    </style>
</head>
<body>
    <div id="login">
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form id="login-form" form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <h3 class="text-center text-info">Registration</h3>
                            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                <label for="username" class="text-info">Username:</label><br>
                                <input type="text" name="username" id="username" class="form-control" value="<?php echo $username; ?>">
                                <span class="help-block"><?php echo $username_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($display_name_err)) ? 'has-error' : ''; ?>">
                                <label for="display_name" class="text-info">Username:</label><br>
                                <input type="text" name="display_name" id="display_name" class="form-control" value="<?php echo $display_name; ?>">
                                <span class="help-block"><?php echo $display_name_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                <label for="password" class="text-info">Password:</label><br>
                                <input type="text" name="password" id="password" class="form-control">
                                <span class="help-block"><?php echo $password_err; ?></span>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" class="btn btn-info btn-md" value="Register">
                            </div>
                            <div id="register-link" class="text-right">
                                <span>Already have an account? <a href="/auth/register" class="text-info">Login</a>.</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
