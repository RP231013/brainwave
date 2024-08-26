<?php
// admin_student_detail.php

require_once 'config.php'; 

$student_id = $_GET['StuID']; // Get student ID from the URL

// Fetch student details using mysqli
$sql = "SELECT * FROM students WHERE StuID = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

// Fetch student's subjects
$sql_subjects = "SELECT Subjects.subID, Subjects.subName FROM Subjects 
                 JOIN TakingSubject ON Subjects.subID = TakingSubject.subID 
                 WHERE TakingSubject.stuID = ?";
$stmt_subjects = mysqli_prepare($link, $sql_subjects);
mysqli_stmt_bind_param($stmt_subjects, "i", $student_id);
mysqli_stmt_execute($stmt_subjects);
$result_subjects = mysqli_stmt_get_result($stmt_subjects);
$student_subjects = mysqli_fetch_all($result_subjects, MYSQLI_ASSOC);

// Fetch student's grades from the Grades table using mysqli
$sql_grades = "SELECT Subjects.subName, Assignments.title, Grades.grade 
               FROM Grades
               JOIN Assignments ON Grades.assignmentID = Assignments.assignID
               JOIN Subjects ON Grades.subID = Subjects.subID
               WHERE Grades.stuID = ?";
$stmt_grades = mysqli_prepare($link, $sql_grades);
mysqli_stmt_bind_param($stmt_grades, "i", $student_id);
mysqli_stmt_execute($stmt_grades);
$result_grades = mysqli_stmt_get_result($stmt_grades);
$grades = mysqli_fetch_all($result_grades, MYSQLI_ASSOC);

// Handle form submission for saving edits
if (isset($_POST['save_edits'])) {
    // Update personal information
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];

    $sql_update = "UPDATE students SET name = ?, surname = ?, gender = ?, email = ? WHERE stuID = ?";
    $stmt_update = mysqli_prepare($link, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "ssssi", $name, $surname, $gender, $email, $student_id);
    mysqli_stmt_execute($stmt_update);

    // Redirect to show updated data
    header("Location: admin_student_detail.php?StuID=" . $student_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Detail View</title>
    <link rel="stylesheet" href="admin_dashboard.css"> 
    <style>
        .student-detail {
            width: 100%;
            max-width: 1000px;
            margin: auto;
        }

        form {
            margin-bottom: 30px;
            text-align: left;
        }

        form label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        form input[type="text"],
        form input[type="email"],
        form select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 16px;
        }

        form button {
            background-color: rgba(0,0,0, 0.4);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form button:hover {
            background-color: rgb(0,0,0);
        }

        .grades-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .grades-table th,
        .grades-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .grades-table th {
            background-color: #007bff;
            color: white;
        }

        .grades-table td {
            background-color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body>
    <div class="glass-container student-detail">
        <div class="title-block">
            <h1>Admin Dashboard - Student Details</h1>
        </div>

        <form method="POST" action="admin_student_detail.php?StuID=<?php echo $student_id; ?>">
            <!-- Personal Details -->
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo $student['name']; ?>" required>

            <label for="surname">Surname:</label>
            <input type="text" name="surname" value="<?php echo $student['surname']; ?>" required>

            <label for="gender">Gender:</label>
            <input type="text" name="gender" value="<?php echo $student['gender']; ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo $student['email']; ?>" required>

            <button type="submit" name="save_edits">Save Edits</button>
        </form>

        <!-- Grades Table -->
        <div class="grades-table">
            <h2>Grades</h2>
            <?php if (count($grades) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Assignment Title</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($grade['subName']); ?></td>
                                <td><?php echo htmlspecialchars($grade['title']); ?></td>
                                <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No grade data available yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
