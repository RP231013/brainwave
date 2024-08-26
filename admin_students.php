<?php
session_start();
require_once "config.php";

// cheacks if admin is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// get students 
$students_waiting_for_approval = [];
$approved_students = [];

// get only unapproved students
$sql_unapproved_students = "SELECT stuID, name, surname FROM Students WHERE approved = 0";
if ($result = mysqli_query($link, $sql_unapproved_students)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students_waiting_for_approval[] = $row;
    }
    mysqli_free_result($result);
}

// get approved students
$sql_approved_students = "SELECT stuID, name, surname FROM Students WHERE approved = 1";
if ($result = mysqli_query($link, $sql_approved_students)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $approved_students[] = $row;
    }
    mysqli_free_result($result);
}

// deal with approval or decline of students
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $student_id = $_POST['student_id'];

        // update the student status to approved
        $sql_approve = "UPDATE Students SET approved = 1 WHERE stuID = ?";
        if ($stmt = mysqli_prepare($link, $sql_approve)) {
            mysqli_stmt_bind_param($stmt, "i", $student_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    } elseif (isset($_POST['decline'])) {
        $student_id = $_POST['student_id'];
        // delete the student (this will cascade delete related records)
        $sql_delete = "DELETE FROM Students WHERE stuID = ?";
        if ($stmt = mysqli_prepare($link, $sql_delete)) {
            mysqli_stmt_bind_param($stmt, "i", $student_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    // refresh 
    header("location: admin_students.php");
    exit;
}

// close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Students</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <div class="glass-container">
        <div class="title-block">
            <img src="logo.png" alt="Brainwave Logo" class="logo">
            <h1>Admin Dashboard - Students</h1>
        </div>

        <div class="dashboard-cards">
            <div class="students-approval">
                <h2>Waiting for approval</h2>
                <div class="students-list">
                    <?php foreach ($students_waiting_for_approval as $student): ?>
                        <div class="student-card">
                            <h3><?php echo htmlspecialchars($student['name']) . ' ' . htmlspecialchars($student['surname']); ?></h3>
                            <form method="post">
                                <input type="hidden" name="student_id" value="<?php echo $student['stuID']; ?>">
                                <button type="submit" name="approve">Approve</button>
                                <button type="submit" name="decline">Decline</button>
                            </form>
                            <!-- View More Button -->
                            <button onclick="window.location.href='admin_student_detail.php?StuID=<?php echo $student['stuID']; ?>'">View more</button>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($students_waiting_for_approval)): ?>
                        <p>No students awaiting approval.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="students-active">
                <h2>Active Students</h2>
                <div class="students-list">
                    <?php foreach ($approved_students as $student): ?>
                        <div class="student-card">
                            <h3><?php echo htmlspecialchars($student['name']) . ' ' . htmlspecialchars($student['surname']); ?></h3>
                            <!-- View More Button -->
                            <button onclick="window.location.href='admin_student_detail.php?StuID=<?php echo $student['stuID']; ?>'">View more</button>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($approved_students)): ?>
                        <p>No active students.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bottom-nav">
            <button onclick="window.location.href='admin_dashboard.php'">Dashboard</button>
            <button onclick="window.location.href='admin_students.php'" class="active">Students</button>
            <button onclick="window.location.href='admin_teachers.php'">Teachers</button>
            <button onclick="window.location.href='admin_subjects.php'">Subjects</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</body>
</html>
