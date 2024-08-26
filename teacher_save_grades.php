<?php
require_once "config.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // get stuID
    $student_id = $_POST['student_id'];

    // check if there are graeds to update
    if (!empty($_POST['grades'])) {

        // update query
        $sql = "UPDATE Grades SET grade = ? WHERE gradeID = ? AND stuID = ?";
        $stmt = mysqli_prepare($link, $sql);

        // update each grade trhough loop
        foreach ($_POST['grades'] as $gradeID => $newGrade) {
            mysqli_stmt_bind_param($stmt, "iii", $newGrade, $gradeID, $student_id);
            mysqli_stmt_execute($stmt);
        }

       
        mysqli_stmt_close($stmt);
        echo "Grades updated successfully!";
    } else {
        echo "No grades to save.";
    }
}


mysqli_close($link);

// redirect
header("Location: teacher_dashboard.php");
exit;
?>
