<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$teacher_id = $_SESSION['teachID'];

// get teacher info
$sql_teacher = "SELECT name, surname, email, gender FROM Teachers WHERE teachID = ?";
$stmt_teacher = mysqli_prepare($link, $sql_teacher);
mysqli_stmt_bind_param($stmt_teacher, "i", $teacher_id);
mysqli_stmt_execute($stmt_teacher);
$result_teacher = mysqli_stmt_get_result($stmt_teacher);
$teacher = mysqli_fetch_assoc($result_teacher);

// get teachers subjets
$sql_subjects = "
    SELECT Subjects.subName 
    FROM Subjects
    JOIN TeachingSubject ON Subjects.subID = TeachingSubject.subID
    WHERE TeachingSubject.teachID = ?";
$stmt_subjects = mysqli_prepare($link, $sql_subjects);
mysqli_stmt_bind_param($stmt_subjects, "i", $teacher_id);
mysqli_stmt_execute($stmt_subjects);
$result_subjects = mysqli_stmt_get_result($stmt_subjects);

// array of subjects taught 
$subjects = [];
while ($row = mysqli_fetch_assoc($result_subjects)) {
    $subjects[] = $row['subName'];
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        .profile-container {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-container h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .profile-container p {
            font-size: 18px;
            margin: 10px 0;
        }

        .subjects-list {
            margin-top: 20px;
        }

        .subjects-list h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .subjects-list ul {
            list-style-type: none;
            padding: 0;
        }

        .subjects-list ul li {
            background-color: rgba(255, 255, 255, 0.4);
            margin: 5px 0;
            padding: 10px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="glass-container">
        <div class="profile-container">
            <h2>Teacher Profile</h2>
            <p><strong>Name:</strong> <?php echo $teacher['name'] . ' ' . $teacher['surname']; ?></p>
            <p><strong>Email:</strong> <?php echo $teacher['email']; ?></p>
            <p><strong>Gender:</strong> <?php echo $teacher['gender']; ?></p>

            <div class="subjects-list">
                <h3>Subjects You Teach</h3>
                <ul>
                    <?php foreach ($subjects as $subject): ?>
                        <li><?php echo $subject; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="bottom-nav">
            <button onclick="window.location.href='teacher_dashboard.php'">Dashboard</button>
            <button onclick="window.location.href='teacher_create_assignment.php'">Create Assignment</button>
            <button class="active" onclick="window.location.href='teacher_profile.php'">Profile</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</body>
</html>
