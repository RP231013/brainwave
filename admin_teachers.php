<?php
session_start();
require_once "config.php";

// Ensure the admin is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Fetch teachers waiting for approval and approved teachers
$teachers_waiting_for_approval = [];
$approved_teachers = [];

// Fetch unapproved teachers
$sql_unapproved_teachers = "SELECT teachID, name, surname FROM Teachers WHERE approved = 0";
if ($result = mysqli_query($link, $sql_unapproved_teachers)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $teachers_waiting_for_approval[] = $row;
    }
    mysqli_free_result($result);
}

// Fetch approved teachers and their subjects
$sql_approved_teachers = "
    SELECT Teachers.teachID, Teachers.name, Teachers.surname, GROUP_CONCAT(Subjects.subName SEPARATOR ', ') AS subjects
    FROM Teachers
    LEFT JOIN TeachingSubject ON Teachers.teachID = TeachingSubject.teachID
    LEFT JOIN Subjects ON TeachingSubject.subID = Subjects.subID
    WHERE Teachers.approved = 1
    GROUP BY Teachers.teachID
";
if ($result = mysqli_query($link, $sql_approved_teachers)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $approved_teachers[] = $row;
    }
    mysqli_free_result($result);
}

// Handle approval or decline of teachers
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $teacher_id = $_POST['teacher_id'];

        // Update the teacher's status to approved
        $sql_approve = "UPDATE Teachers SET approved = 1 WHERE teachID = ?";
        if ($stmt = mysqli_prepare($link, $sql_approve)) {
            mysqli_stmt_bind_param($stmt, "i", $teacher_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

    } elseif (isset($_POST['decline'])) {
        $teacher_id = $_POST['teacher_id'];
        // Delete the teacher
        $sql_delete = "DELETE FROM Teachers WHERE teachID = ?";
        if ($stmt = mysqli_prepare($link, $sql_delete)) {
            mysqli_stmt_bind_param($stmt, "i", $teacher_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Refresh the page after action
    header("location: admin_teachers.php");
    exit;
}

// Close database connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Teachers</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <div class="glass-container">
        <div class="title-block">
            <img src="logo.png" alt="Brainwave Logo" class="logo">
            <h1>Admin Dashboard - Teachers</h1>
        </div>

        <div class="dashboard-cards">
            <div class="students-approval">
                <h2>Waiting for Approval</h2>
                <div class="students-list">
                    <?php foreach ($teachers_waiting_for_approval as $teacher): ?>
                        <div class="student-card">
                            <h3><?php echo htmlspecialchars($teacher['name']) . ' ' . htmlspecialchars($teacher['surname']); ?></h3>
                            <form method="post">
                                <input type="hidden" name="teacher_id" value="<?php echo $teacher['teachID']; ?>">
                                <button type="submit" name="approve">Approve</button>
                                <button type="submit" name="decline">Decline</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($teachers_waiting_for_approval)): ?>
                        <p>No teachers awaiting approval.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="students-active">
                <h2>Active Teachers</h2>
                <div class="students-list">
                    <?php foreach ($approved_teachers as $teacher): ?>
                        <div class="student-card">
                            <h3><?php echo htmlspecialchars($teacher['name']) . ' ' . htmlspecialchars($teacher['surname']); ?></h3>
                            <p><strong>Subjects:</strong> <?php echo htmlspecialchars($teacher['subjects']); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($approved_teachers)): ?>
                        <p>No active teachers.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bottom-nav">
            <button onclick="window.location.href='admin_dashboard.php'">Dashboard</button>
            <button onclick="window.location.href='admin_students.php'">Students</button>
            <button onclick="window.location.href='admin_teachers.php'" class="active">Teachers</button>
            <button onclick="window.location.href='admin_subjects.php'">Subjects</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</body>
</html>
