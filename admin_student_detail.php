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
$sql_subjects = "SELECT Subjects.SubID, Subjects.subName FROM Subjects 
                 JOIN TakingSubject ON Subjects.SubID = TakingSubject.SubID 
                 WHERE TakingSubject.StuID = ?";
$stmt_subjects = mysqli_prepare($link, $sql_subjects);
mysqli_stmt_bind_param($stmt_subjects, "i", $student_id);
mysqli_stmt_execute($stmt_subjects);
$result_subjects = mysqli_stmt_get_result($stmt_subjects);
$student_subjects = mysqli_fetch_all($result_subjects, MYSQLI_ASSOC);

// Fetch student's marks from the marks table using mysqli
$sql_marks = "SELECT Subjects.subName, marks.term, marks.mark
              FROM marks
              JOIN Subjects ON marks.SubID = Subjects.SubID
              WHERE marks.StuID = ?";
$stmt_marks = mysqli_prepare($link, $sql_marks);
mysqli_stmt_bind_param($stmt_marks, "i", $student_id);
mysqli_stmt_execute($stmt_marks);
$result_marks = mysqli_stmt_get_result($stmt_marks);
$marks = mysqli_fetch_all($result_marks, MYSQLI_ASSOC);


// Handle form submission for saving edits
if (isset($_POST['save_edits'])) {
    // Update personal information
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];

    $sql_update = "UPDATE students SET name = ?, surname = ?, gender = ?, email = ? WHERE StuID = ?";
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
            <h1>Admin Dashboard - Students</h1>
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

            <!-- Subject Selection as Dropdowns -->
            <button type="submit" name="save_edits">Save Edits</button>
        </form>

        <!-- Grades Table -->
        <div class="grades-table">
            <h2>Grades</h2>
            <?php if (count($marks) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Term 1</th>
                            <th>Term 2</th>
                            <th>Term 3</th>
                            <th>Term 4</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Initialize an empty array to store subjects and their marks for each term
                        $subject_grades = [];

                        foreach ($marks as $mark) {
                            $subject_grades[$mark['subName']]['T' . $mark['Term']] = $mark['Mark'];
                        }

                        foreach ($subject_grades as $subject_name => $grades): ?>
                            <tr>
                                <td><?php echo $subject_name; ?></td>
                                <td><?php echo $grades['T1'] ?? '-'; ?></td>
                                <td><?php echo $grades['T2'] ?? '-'; ?></td>
                                <td><?php echo $grades['T3'] ?? '-'; ?></td>
                                <td><?php echo $grades['T4'] ?? '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No grade data yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
