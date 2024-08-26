<?php

require_once "config.php";

// start session
session_start();

// is form submitted check
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get the form data
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $surname = mysqli_real_escape_string($link, $_POST['surname']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    // password hashed for security (encrypted)
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $gender = mysqli_real_escape_string($link, $_POST['gender']);
    $userType = mysqli_real_escape_string($link, $_POST['userType']);
    
    // insert user into table based off of user type
    if ($userType == "student") {
        // insert into Students table
        $sql = "INSERT INTO Students (name, surname, gender, email, password, approved) VALUES (?, ?, ?, ?, ?, 0)"; // approved is set to 0 by default
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssss", $name, $surname, $gender, $email, $password);
            if (mysqli_stmt_execute($stmt)) {
                
                $student_id = mysqli_insert_id($link);

                // insert selected subjects into TakingSubject table
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


                // checks if there are assignments and creates empty grade sets
                foreach ($_POST['subjects'] as $subject_id) {


                    $sql_assignments = "SELECT assignID FROM Assignments WHERE subID = ?";
                    if ($stmt_assignments = mysqli_prepare($link, $sql_assignments)) {
                        mysqli_stmt_bind_param($stmt_assignments, "i", $subject_id);
                        mysqli_stmt_execute($stmt_assignments);
                        $result_assignments = mysqli_stmt_get_result($stmt_assignments);
                
                        
                        while ($assignment = mysqli_fetch_assoc($result_assignments)) {
                            $assignID = $assignment['assignID'];
                            $sql_insert_grade = "INSERT INTO Grades (stuID, assignmentID, subID, grade) VALUES (?, ?, ?, 0)";
                            if ($stmt_grade = mysqli_prepare($link, $sql_insert_grade)) {
                                mysqli_stmt_bind_param($stmt_grade, "iii", $student_id, $assignID, $subject_id);
                                mysqli_stmt_execute($stmt_grade);
                            }
                        }
                        mysqli_stmt_close($stmt_assignments);
                    }
                }

                // redirect
                $_SESSION['email'] = $email;
                header("Location: student_dashboard.php");
                exit;
            } else {
                echo "Something went wrong. Please try again.";
            }
        }
    } elseif ($userType == "teacher") {
        // insert into Teachers table
        $sql = "INSERT INTO Teachers (name, surname, gender, email, password, approved) VALUES (?, ?, ?, ?, ?, 0)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssss", $name, $surname, $gender, $email, $password);
            if (mysqli_stmt_execute($stmt)) {
                // insert teaching subjects
                if (!empty($_POST['subjects'])) {
                    $teacher_id = mysqli_insert_id($link); 
                    foreach ($_POST['subjects'] as $subject_id) {
                        $sub_sql = "INSERT INTO TeachingSubject (teachID, subID) VALUES (?, ?)";
                        if ($sub_stmt = mysqli_prepare($link, $sub_sql)) {
                            mysqli_stmt_bind_param($sub_stmt, "ii", $teacher_id, $subject_id);
                            mysqli_stmt_execute($sub_stmt);
                            mysqli_stmt_close($sub_stmt);
                        }
                    }
                }
                // go to teach dashbaord
                $_SESSION['email'] = $email;
                header("Location: teacher_dashboard.php");
                exit;
            } else {
                echo "Something went wrong. Please try again.";
            }
        }
    } elseif ($userType == "admin") {
        // insert into Admins table
        $sql = "INSERT INTO Admins (adminName, adminSurname, adminEmail, adminPassword) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $name, $surname, $email, $password);
            if (mysqli_stmt_execute($stmt)) {
                // go to admin dash
                $_SESSION['email'] = $email;
                header("Location: admin_dashboard.php");
                exit;
            } else {
                echo "Something went wrong. Please try again.";
            }
        }
    }
    
    // close
    mysqli_stmt_close($stmt);
    mysqli_close($link);
}
?>
