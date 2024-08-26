<?php
session_start();
require_once "config.php";

// cheack if student logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// gets current stu ID from session
$student_id = $_SESSION['userID'];

// get stu info
$sql_student = "SELECT name, surname, email, gender FROM Students WHERE stuID = ?";
$stmt_student = mysqli_prepare($link, $sql_student);
mysqli_stmt_bind_param($stmt_student, "i", $student_id);
mysqli_stmt_execute($stmt_student);
$result_student = mysqli_stmt_get_result($stmt_student);
$student = mysqli_fetch_assoc($result_student);

// get stu subs
$sql_grades = "
    SELECT Subjects.subName, Assignments.title, Grades.grade, Assignments.maxMarks
    FROM Grades
    JOIN Assignments ON Grades.assignmentID = Assignments.assignID
    JOIN Subjects ON Assignments.subID = Subjects.subID
    WHERE Grades.stuID = ?";
$stmt_grades = mysqli_prepare($link, $sql_grades);
mysqli_stmt_bind_param($stmt_grades, "i", $student_id);
mysqli_stmt_execute($stmt_grades);
$result_grades = mysqli_stmt_get_result($stmt_grades);

// store grades grouped by subs for graph later
$grades_by_subject = [];
$assignments = [];

while ($row = mysqli_fetch_assoc($result_grades)) {
    if (!in_array($row['title'], $assignments)) {
        $assignments[] = $row['title'];
    }
    $grades_by_subject[$row['subName']][$row['title']] = $row['grade'];
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 20px;
            max-width: 1000px;
            margin: auto;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .dashboard-container h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .dashboard-container p {
            font-size: 18px;
            margin: 10px 0;
        }

        .chart-container {
            margin-top: 30px;
            width: 50vw;
            height: 50vh;
        }

        canvas {
            background-color: rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            padding: 20px;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="glass-container">
        <div class="dashboard-container">
            <h2>Welcome, <?php echo $student['name'] . ' ' . $student['surname']; ?></h2>
            <p><strong>Email:</strong> <?php echo $student['email']; ?></p>
            <p><strong>Gender:</strong> <?php echo $student['gender']; ?></p>

            <div class="chart-container">
                <canvas id="gradesChart"></canvas>
            </div>
        </div>

        <div class="bottom-nav">
            <button class="active" onclick="window.location.href='student_dashboard.php'">Dashboard</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>

    <script>
        // get data ready for charts.js
        const subjects = <?php echo json_encode(array_keys($grades_by_subject)); ?>;
        const assignments = <?php echo json_encode($assignments); ?>;
        const gradeData = <?php echo json_encode($grades_by_subject); ?>;

        
        const datasets = assignments.map((assignment, index) => {
            const data = subjects.map(subject => gradeData[subject][assignment] || 0);
            return {
                label: assignment,
                data: data,
                backgroundColor: `hsl(${index * 50}, 70%, 50%)`,
            };
        });

        
        const ctx = document.getElementById('gradesChart').getContext('2d');
        const gradesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: subjects, 
                datasets: datasets 
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Grades by Subject and Assignment'
                    }
                },
                scales: {
                    x: {
                        stacked: true 
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        stacked: true 
                    }
                }
            }
        });
    </script>
</body>
</html>
