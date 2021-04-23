<?php

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    //header("location: /auth/login.php");
    header("HTTP/1.1 401 Unauthorized");
    die();
}

?>