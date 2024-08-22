<?php
require_once "config.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // check if email domain is admin
    if (strpos($email, '@adminbrainwave.com') !== false) {
        $sql = "INSERT INTO admins (adminName, adminSurname, adminEmail, adminPassword) VALUES (?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $name, $surname, $email, $password);
            if (mysqli_stmt_execute($stmt)) {
                header("location: success.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        if (mysqli_errno($link) == 1062) {
            // 1062 is the error code for a duplicate key violation
            // email is set as unique in the database 
            echo "An account with this email already exists.";
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}
mysqli_close($link);
?>
