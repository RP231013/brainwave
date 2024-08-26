<?php
require_once "config.php";

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // get domain name for user type checking
    $email_domain = substr(strrchr($email, "@"), 1);

    // if admin
    if ($email_domain == "adminbrainwave.com") {
        $sql = "SELECT adminID, adminPassword FROM Admins WHERE adminEmail = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $userID, $hashed_password);

                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["userID"] = $userID;

                            //go to admin dashboard
                            header("Location: admin_dashboard.php");
                            exit;
                        } else {
                            echo "Invalid password.";
                        }
                    }
                } else {
                    echo "No account found with that email.";
                }
            } else {
                echo "Error executing query.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // if student
    elseif ($email_domain == "studentbrainwave.com") {
        $sql = "SELECT stuID, password FROM Students WHERE email = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $userID, $hashed_password);

                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["userID"] = $userID;

                            // go to student dashboard
                            header("Location: student_dashboard.php");
                            exit;
                        } else {
                            echo "Invalid password.";
                        }
                    }
                } else {
                    echo "No account found with that email.";
                }
            } else {
                echo "Error executing query.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // if teacher
    elseif ($email_domain == "teacherbrainwave.com") {
        $sql = "SELECT teachID, password FROM Teachers WHERE email = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $userID, $hashed_password);

                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION['teachID'] = $userID;

                            // go to teach dash
                            header("Location: teacher_dashboard.php");
                            exit;
                        } else {
                            echo "Invalid password.";
                        }
                    }
                } else {
                    echo "No account found with that email.";
                }
            } else {
                echo "Error executing query.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // invalid email
    else {
        echo "Invalid email domain.";
    }
}

mysqli_close($link);
?>
