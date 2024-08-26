<?php
session_start();
require_once "config.php";

// checks logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}


$teacher_id = $_SESSION['teachID'];

// gets students who have subject(s) teacher teaches
$sql_students = "
    SELECT Students.stuID, Students.name, Students.surname, Subjects.subName, AVG(Grades.grade) AS subject_avg, Assignments.assignID, Assignments.title, Grades.grade, Grades.gradeID
    FROM Students
    JOIN TakingSubject ON Students.stuID = TakingSubject.stuID
    JOIN Subjects ON TakingSubject.subID = Subjects.subID
    JOIN TeachingSubject ON Subjects.subID = TeachingSubject.subID
    LEFT JOIN Assignments ON Subjects.subID = Assignments.subID
    LEFT JOIN Grades ON Grades.assignmentID = Assignments.assignID AND Grades.stuID = Students.stuID
    WHERE TeachingSubject.teachID = ?
    GROUP BY Students.stuID, Subjects.subID, Assignments.assignID
    ORDER BY Students.stuID, Assignments.assignID
";
$stmt_students = mysqli_prepare($link, $sql_students);
mysqli_stmt_bind_param($stmt_students, "i", $teacher_id);
mysqli_stmt_execute($stmt_students);
$result_students = mysqli_stmt_get_result($stmt_students);
$students = [];
while ($row = mysqli_fetch_assoc($result_students)) {
    $students[$row['stuID']]['name'] = $row['name'];
    $students[$row['stuID']]['surname'] = $row['surname'];
    $students[$row['stuID']]['subjects'][$row['subName']]['subject_avg'] = $row['subject_avg'];
    $students[$row['stuID']]['subjects'][$row['subName']]['assignments'][] = [
        'gradeID' => $row['gradeID'], 
        'title' => $row['title'],
        'grade' => $row['grade']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        .student-mark-card {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 20px;
            width: 30%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }

        .student-mark-card h3 {
            font-size: 20px;
            color: #333;
        }

        .assignment-marks {
            margin: 10px 0;
        }

        .assignment-marks input {
            width: 50px;
            padding: 5px;
            margin-right: 5px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .save-button {
            margin-top: 10px;
            background-color: rgba(0, 0, 0, 0.4);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .save-button:hover {
            background-color: rgb(0, 0, 0);
        }
    </style>
</head>
<body>
    <div class="glass-container">
        <div class="title-block">
            <h1>Teacher Dashboard</h1>
        </div>

        <div class="dashboard-cards">
            <?php foreach ($students as $student_id => $student): ?>
                <div class="student-mark-card">
                    <h3><?php echo $student['name'] . ' ' . $student['surname']; ?></h3>
                    <?php foreach ($student['subjects'] as $subject_name => $subject_data): ?>
                        <p><strong>Subject:</strong> <?php echo $subject_name; ?></p>
                        
                        

                        <div class="assignment-marks">
                            <form method="POST" action="teacher_save_grades.php">
                                <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                                <?php foreach ($subject_data['assignments'] as $assignment): ?>
                                    <label><?php echo $assignment['title']; ?>:</label>
                                    <input type="number" name="grades[<?php echo $assignment['gradeID']; ?>]" value="<?php echo intval($assignment['grade']); ?>" min="0" step="1">
                                    <br>
                                <?php endforeach; ?>
                                <button type="submit" class="save-button">Save Changes</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="bottom-nav">
            <button onclick="window.location.href='teacher_dashboard.php'" class="active">Dashboard</button>
            <button onclick="window.location.href='teacher_create_assignment.php'">Create Assignment</button>
            <button onclick="window.location.href='teacher_profile.php'">Profile</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</body>
</html>
