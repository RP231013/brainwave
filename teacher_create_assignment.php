<?php
session_start();
require_once "config.php";

// checks if teacher logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$teacher_id = $_SESSION['teachID'];

// gets subjects the teacher teaches
$sql_subjects = "
    SELECT Subjects.subID, Subjects.subName 
    FROM Subjects
    JOIN TeachingSubject ON Subjects.subID = TeachingSubject.subID
    WHERE TeachingSubject.teachID = ?";
$stmt_subjects = mysqli_prepare($link, $sql_subjects);
mysqli_stmt_bind_param($stmt_subjects, "i", $teacher_id);
mysqli_stmt_execute($stmt_subjects);
$result_subjects = mysqli_stmt_get_result($stmt_subjects);

$subjects = [];
while ($row = mysqli_fetch_assoc($result_subjects)) {
    $subjects[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subID = $_POST['subID'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $maxMarks = $_POST['maxMarks'];
    $year = $_POST['year'];

    // inserts the assignment into the Assignments table
    $sql_insert = "
        INSERT INTO Assignments (subID, title, description, maxMarks, year)
        VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($link, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "isssi", $subID, $title, $description, $maxMarks, $year);

    if (mysqli_stmt_execute($stmt_insert)) {
        $assignmentID = mysqli_insert_id($link); 

        // get all students taking subject
        $sql_students = "SELECT stuID FROM TakingSubject WHERE subID = ?";
        $stmt_students = mysqli_prepare($link, $sql_students);
        mysqli_stmt_bind_param($stmt_students, "i", $subID);
        mysqli_stmt_execute($stmt_students);
        $result_students = mysqli_stmt_get_result($stmt_students);

        // inserts empty grade as editing grade form needs a value to edit 
        $sql_insert_grade = "INSERT INTO Grades (stuID, assignmentID, subID, grade) VALUES (?, ?, ?, 0)";
        $stmt_insert_grade = mysqli_prepare($link, $sql_insert_grade);

        while ($student = mysqli_fetch_assoc($result_students)) {
            mysqli_stmt_bind_param($stmt_insert_grade, "iii", $student['stuID'], $assignmentID, $subID);
            mysqli_stmt_execute($stmt_insert_grade);
        }

        mysqli_stmt_close($stmt_insert_grade);
        echo "Assignment and grades created successfully!";
        header("Location: teacher_dashboard.php");
        exit;
    } else {
        echo "Something went wrong. Please try again.";
    }

    mysqli_stmt_close($stmt_insert);
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        .create-assignment-form {
            margin: auto;
            max-width: 600px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .create-assignment-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .create-assignment-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .create-assignment-form input[type="text"],
        .create-assignment-form input[type="number"],
        .create-assignment-form select,
        .create-assignment-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .create-assignment-form button {
            width: 100%;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.4);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .create-assignment-form button:hover {
            background-color: rgb(0, 0, 0);
        }
    </style>
</head>
<body>
    <div class="glass-container create-assignment-form">
        <h2>Create New Assignment</h2>
        <form method="POST" action="teacher_create_assignment.php">
            <label for="subID">Subject:</label>
            <select name="subID" required>
                <option value="">Select Subject</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?php echo $subject['subID']; ?>">
                        <?php echo $subject['subName']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="title">Assignment Title:</label>
            <input type="text" name="title" required>

            <label for="description">Assignment Description (optional):</label>
            <textarea name="description"></textarea>

            <label for="maxMarks">Maximum Marks:</label>
            <input type="number" name="maxMarks" required>

            <label for="year">Year:</label>
            <input type="number" name="year" value="<?php echo date("Y"); ?>" required>

            <button type="submit">Create Assignment</button>
        </form>
    </div>
</body>
</html>
