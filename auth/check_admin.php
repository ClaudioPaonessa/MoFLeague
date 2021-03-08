<?php

if (!isset($_SESSION["admin"]) || boolval($_SESSION["admin"]) !== true) {
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

?>