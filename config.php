<?php
// config.php

// Database credentials
$host = "localhost";
$username = "root";
$password = "";
$dbname = "brainwave";

// Connect to MySQL
$link = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
