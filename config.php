<?php

// database credentials
$host = "localhost";
$username = "root";
$password = "";
$dbname = "brainwave";

// conncet to MySQL
$link = mysqli_connect($host, $username, $password, $dbname);

// check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
