<?php
session_start();
require_once "config.php";

// Ensure the teacher is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css"> 
</head>
<body>
    <div class="glass-container">
        <div class="title-block">
            <img src="logo.png" alt="Brainwave Logo" class="logo">
            <h1>Teacher Dashboard</h1>
        </div>

        <!-- You can add teacher-specific content here, such as recent assignments, classes, etc. -->
        <div class="dashboard-cards">
            <!-- Placeholder content, replace with teacher-specific dashboard content -->
            <div class="card">
                <h2>Welcome!</h2>
                <p>Manage your classes, create assignments, and more.</p>
            </div>
        </div>

        <!-- Bottom navigation -->
        <div class="bottom-nav">
            <button onclick="window.location.href='teacher_dashboard.php'" class="active">Dashboard</button>
            <button onclick="window.location.href='teacher_create_assignment.php'">Create Assignment</button>
            <button onclick="window.location.href='teacher_profile.php'">Profile</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</body>
</html>
