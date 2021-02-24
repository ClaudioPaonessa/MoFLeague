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
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in the form to register.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($display_name_err)) ? 'has-error' : ''; ?>">
                <label>Display name</label>
                <input type="text" name="display_name" class="form-control" value="<?php echo $display_name; ?>">
                <span class="help-block"><?php echo $display_name_err; ?></span>
            </div>  
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Register">
            </div>
            <p>Already have an account? <a href="/auth/login">Login</a>.</p>
        </form>
    </div>    
</body>
</html>
