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
$username = $password = "";
$username_err = $password_err = "";
 
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
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $query = 'SELECT * FROM accounts WHERE (account_name = :name)';
        $values = [':name' => $username];

        try
        {
            $res = $pdo->prepare($query);
            $res->execute($values);
        }
        catch (PDOException $e)
        {
            echo 'Query error.';
            die();
        }

        $row = $res->fetch(PDO::FETCH_ASSOC);

        if (is_array($row))
        {
            if (password_verify($password, $row['account_passwd']))
            {
                // Password is correct, so start a new session
                session_start();
                            
                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $row['account_id'];
                $_SESSION["username"] = $username;
                $_SESSION["displayName"] = $row['display_name'];
                $_SESSION["admin"] = $row['admin_privilege'];                       
                
                // Redirect user to main page
                header("location: /");
                exit;
            }
        }

        // Not logged in
        $password_err = "Username or password wrong";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>MoF - Login</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                                    <div class="card-body">
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                                <label class="small mb-1" for="username">Username</label>
                                                <input class="form-control py-4" name="username" id="username" type="text" value="<?php echo $username; ?>" placeholder="Enter username" />
                                                <span class="help-block"><?php echo $username_err; ?></span>
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="password">Password</label>
                                                <input class="form-control py-4" name="password" id="password" type="password" placeholder="Enter password" />
                                                <span class="help-block"><?php echo $password_err; ?></span>
                                            </div>
                                            <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="#">Forgot Password?</a>
                                                <input type="submit" name="submit" class="btn btn-primary" value="Login">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="small"><a href="/auth/register">Need an account? Sign up!</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Mansion of Fire 2021</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
    </body>
</html>
