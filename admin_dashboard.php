<?php
session_start();
require_once "config.php";

// Ensure the admin is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Fetch counts for students and teachers awaiting approval and active users
$unapproved_students_count = 0;
$unapproved_teachers_count = 0;
$approved_students_count = 0;
$approved_teachers_count = 0;

$sql_students = "SELECT COUNT(*) FROM Students WHERE approved = 0";
$sql_teachers = "SELECT COUNT(*) FROM Teachers WHERE approved = 0";
$sql_approved_students = "SELECT COUNT(*) FROM Students WHERE approved = 1";
$sql_approved_teachers = "SELECT COUNT(*) FROM Teachers WHERE approved = 1";

// Get unapproved students count
if ($stmt = mysqli_prepare($link, $sql_students)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $unapproved_students_count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Get unapproved teachers count
if ($stmt = mysqli_prepare($link, $sql_teachers)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $unapproved_teachers_count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Get approved students count
if ($stmt = mysqli_prepare($link, $sql_approved_students)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $approved_students_count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Get approved teachers count
if ($stmt = mysqli_prepare($link, $sql_approved_teachers)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $approved_teachers_count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Close database connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <div class="glass-container">
        <div class="title-block">
            <img src="logo.png" alt="Brainwave Logo" class="logo">
            <h1>Admin Dashboard</h1>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h2>Waiting for approval</h2>
                <p><?php echo $unapproved_students_count; ?> - Students</p>
                <p><?php echo $unapproved_teachers_count; ?> - Teachers</p>
                <button onclick="window.location.href='admin_students.php'">Approve Students</button>
                <button onclick="window.location.href='admin_teachers.php'">Approve Teachers</button>
            </div>
            <div class="card">
                <h2>Active Students</h2>
                <p><?php echo $approved_students_count; ?></p>
                <button onclick="window.location.href='admin_students.php'">View Students</button>
            </div>
            <div class="card">
                <h2>Active Teachers</h2>
                <p><?php echo $approved_teachers_count; ?></p>
                <button onclick="window.location.href='admin_teachers.php'">View Teachers</button>
            </div>
        </div>

        <div class="bottom-nav">
            <button onclick="window.location.href='admin_dashboard.php'">Dashboard</button>
            <button onclick="window.location.href='admin_students.php'">Students</button>
            <button onclick="window.location.href='admin_teachers.php'">Teachers</button>
            <button onclick="window.location.href='admin_subjects.php'">Subjects</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</body>
</html>
