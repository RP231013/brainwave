<?php
// db config
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'brainwave');

// connect to MySQL
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

//  connection check
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
