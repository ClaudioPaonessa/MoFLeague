<?php
session_start();
unset($_SESSION["loggedin"]);
unset($_SESSION["id"]);
unset($_SESSION["email"]);
header("location: /auth/login.php");
?>