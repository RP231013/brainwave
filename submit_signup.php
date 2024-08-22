<?php

require_once "config.php";

// Start session
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get the form data
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $surname = mysqli_real_escape_string($link, $_POST['surname']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $gender = mysqli_real_escape_string($link, $_POST['gender']);
    $userType = mysqli_real_escape_string($link, $_POST['userType']);
    
    // Insert user data into the correct table based on userType
    if ($userType == "student") {
        // Insert into Students table
        $sql = "INSERT INTO Students (name, surname, gender, email, password, approved) VALUES (?, ?, ?, ?, ?, 0)"; // approved is set to 0 by default
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssss", $name, $surname, $gender, $email, $password);
            if (mysqli_stmt_execute($stmt)) {
                // Get the last inserted student ID
                $student_id = mysqli_insert_id($link);

                // Insert selected subjects into TakingSubject table
                if (!empty($_POST['subjects'])) {
                    foreach ($_POST['subjects'] as $subject_id) {
                        $sub_sql = "INSERT INTO TakingSubject (stuID, subID) VALUES (?, ?)";
                        if ($sub_stmt = mysqli_prepare($link, $sub_sql)) {
                            mysqli_stmt_bind_param($sub_stmt, "ii", $student_id, $subject_id);
                            mysqli_stmt_execute($sub_stmt);
                            mysqli_stmt_close($sub_stmt);
                        }
                    }
                }
                // Redirect to the student dashboard
                $_SESSION['email'] = $email;
                header("Location: student_dashboard.php");
                exit;
            } else {
                echo "Something went wrong. Please try again.";
            }
        }
    } elseif ($userType == "teacher") {
        // Insert into Teachers table
        $sql = "INSERT INTO Teachers (name, surname, gender, email, password, approved) VALUES (?, ?, ?, ?, ?, 0)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssss", $name, $surname, $gender, $email, $password);
            if (mysqli_stmt_execute($stmt)) {
                // Insert subjects into TeachingSubject table
                if (!empty($_POST['subjects'])) {
                    $teacher_id = mysqli_insert_id($link); // Get the last inserted teacher's ID
                    foreach ($_POST['subjects'] as $subject_id) {
                        $sub_sql = "INSERT INTO TeachingSubject (teachID, subID) VALUES (?, ?)";
                        if ($sub_stmt = mysqli_prepare($link, $sub_sql)) {
                            mysqli_stmt_bind_param($sub_stmt, "ii", $teacher_id, $subject_id);
                            mysqli_stmt_execute($sub_stmt);
                            mysqli_stmt_close($sub_stmt);
                        }
                    }
                }
                // Redirect to the teacher dashboard
                $_SESSION['email'] = $email;
                header("Location: teacher_dashboard.php");
                exit;
            } else {
                echo "Something went wrong. Please try again.";
            }
        }
    } elseif ($userType == "admin") {
        // Insert into Admins table
        $sql = "INSERT INTO Admins (adminName, adminSurname, adminEmail, adminPassword) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $name, $surname, $email, $password);
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to the admin dashboard
                $_SESSION['email'] = $email;
                header("Location: admin_dashboard.php");
                exit;
            } else {
                echo "Something went wrong. Please try again.";
            }
        }
    }
    
    // Close statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($link);
}
?>
