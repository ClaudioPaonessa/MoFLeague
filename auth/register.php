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
$email = $password = $confirm_password = $display_name = $mtg_arena_name = "";
$email_err = $password_err = $confirm_password_err = $display_name_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $email = $_POST["email"];

    // Check if email is empty
    if(empty(trim($email))){
        $email_err = "Please enter email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email address";
    } else{
        $email = trim($email);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);

        if($password !== trim($_POST["confirm_password"])){
            $confirm_password_err = "Confirmation password does not match.";
        }
    }

    // Check if password is empty
    if(empty(trim($_POST["display_name"]))){
        $display_name_err = "Please enter your display name.";
    } else{
        $display_name = trim($_POST["display_name"]);
    }

    $mtg_arena_name = trim($_POST["mtg_arena_name"]);
    
    // Validate credentials
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($display_name_err)){
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = 'INSERT INTO accounts (email, display_name, mtg_arena_name, account_passwd) VALUES (:email, :display_name, :mtg_arena_name, :passwd)';
        $values = [':email' => $email, ':display_name' => $display_name, ':mtg_arena_name' => $mtg_arena_name, ':passwd' => $hash];

        try
        {
            $res = $pdo->prepare($sql);
            $res->execute($values);

            header("location: /auth/login");
        }
        catch (PDOException $e)
        {
            $email_err = "Email " . $email . " already taken";
            $email = "";
        }
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
        <title>MoF - Registration</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat&display=swap" rel="stylesheet">
    </head>
    <body>
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            
                            <div class="col-lg-7">
                                <div class="card card-special mt-5">
                                    <div class="card-header"><h3>Create Account</h3></div>
                                    <div class="card-body">
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                                                        <label class="small mb-1" for="email">Email address</label>
                                                        <input class="form-control py-4" name="email" id="email" type="email" value="<?php echo $email; ?>" placeholder="Enter email address" />
                                                        <span class="help-block"><?php echo $email_err; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group <?php echo (!empty($display_name_err)) ? 'has-error' : ''; ?>">
                                                        <label class="small mb-1" for="display_name">Display Name</label>
                                                        <input class="form-control py-4" name="display_name" id="display_name" type="text" value="<?php echo $display_name; ?>" placeholder="Enter display name (real name)" />
                                                        <span class="help-block"><?php echo $display_name_err; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="mtg_arena_name">MTG Arena Name</label>
                                                        <input class="form-control py-4" name="mtg_arena_name" id="mtg_arena_name" type="text" value="<?php echo $mtg_arena_name; ?>" placeholder="Enter MTG arena name" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="password">Password</label>
                                                        <input class="form-control py-4" name="password" id="password" type="password" placeholder="Enter password" />
                                                        <span class="help-block"><?php echo $password_err; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="confirm_password">Confirm Password</label>
                                                        <input class="form-control py-4" name="confirm_password" id="confirm_password" type="password" placeholder="Confirm password" />
                                                        <span class="help-block"><?php echo $confirm_password_err; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mt-4 mb-0"><input type="submit" name="submit" class="btn btn-primary btn-block" value="Create Account"></div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="small"><a href="/auth/login">Have an account? Go to login</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-dark mt-auto">
                    <div class="container-fluid">
                        <div class="row small align-items-center">
                            <div class="col-xl-6">
                                <p>Copyright &copy; Mansion of Fire 2021</p>
                                <p class="text-muted small">Portions of Mansion of Fire are unofficial Fan Content permitted under the Wizards of the Coast Fan Content Policy. 
                                    The literal and graphical information presented on this site about Magic: The Gathering, including card images, 
                                    the mana symbols, and Oracle text, is copyright Wizards of the Coast, LLC, a subsidiary of Hasbro, 
                                    Mansion of Fire is not produced by, endorsed by, supported by, or affiliated with Wizards of the Coast.</p>
                            </div>
                            <div class="col-xl-6 text-right">
                                <a href="/privacy.html">Privacy Policy</a>
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
