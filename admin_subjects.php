<?php
session_start();
require_once "config.php";

// Ensure the admin is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Handle adding a new subject
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_subject'])) {
    $sub_name = mysqli_real_escape_string($link, $_POST['sub_name']);
    $sub_code = strtoupper(substr($sub_name, 0, 2) . substr($sub_name, 3, 1)); // Generate subCode
    
    $sql_add = "INSERT INTO Subjects (subName, subCode) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($link, $sql_add)) {
        mysqli_stmt_bind_param($stmt, "ss", $sub_name, $sub_code);
        if (mysqli_stmt_execute($stmt)) {
            header("location: admin_subjects.php");
            exit;
        } else {
            echo "Error adding subject.";
        }
    }
}

// Handle deletion of a subject (cascade delete)
if (isset($_GET['delete_id'])) {
    $sub_id = $_GET['delete_id'];

    $sql_delete = "DELETE FROM Subjects WHERE subID = ?";
    if ($stmt = mysqli_prepare($link, $sql_delete)) {
        mysqli_stmt_bind_param($stmt, "i", $sub_id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: admin_subjects.php");
            exit;
        } else {
            echo "Error deleting subject.";
        }
    }
}

// Fetch all subjects
$sql_subjects = "SELECT * FROM Subjects";
$result_subjects = mysqli_query($link, $sql_subjects);
$subjects = mysqli_fetch_all($result_subjects, MYSQLI_ASSOC);

// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Subjects</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        td {
            background-color: rgba(255, 255, 255, 0.8);
        }
        button {
            background-color: rgba(0, 0, 0, 0.4);
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: rgb(0, 0, 0);
        }
        .add-subject-form {
            margin-top: 20px;
            text-align: left;
        }
        .add-subject-form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .scrollable-container {
            max-height: 30vh !important; 
            overflow-y: auto; 
            margin-top: 20px;
        }

        .scrollable-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .scrollable-container th, 
        .scrollable-container td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .scrollable-container th {
            background-color: #007bff;
            color: white;
        }

        .scrollable-container td {
            background-color: rgba(255, 255, 255, 0.8);
        }
        .bottom-nav button {
            padding: 10px 20px;
            background-color: rgba(255, 255, 255, 0.4) !important;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            width: 18%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-weight: bolder;
        }

        .bottom-nav button:hover {
            background-color: #bbb;
            color: white;
        }

        .bottom-nav button:focus {
            background-color: #4d4d4d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="glass-container">
        <div class="title-block">
            <h1>Admin Dashboard - Subjects</h1>
        </div>

        <h2>All Subjects</h2>
        <div class="scrollable-container">
            <table>
                <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th>Subject Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subName']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subCode']); ?></td>
                                <td>
                                    <a href="admin_subjects.php?delete_id=<?php echo $subject['subID']; ?>" onclick="return confirm('Are you sure you want to delete this subject?');">
                                        <button>Delete</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No subjects available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Subject Form -->
        <div class="add-subject-form">
            <h2>Add a New Subject</h2>
            <form method="POST" action="admin_subjects.php">
                <input type="text" name="sub_name" placeholder="Enter Subject Name" required>
                <button type="submit" name="add_subject">Add Subject</button>
            </form>
        </div>

        <div class="bottom-nav">
            <button onclick="window.location.href='admin_dashboard.php'">Dashboard</button>
            <button onclick="window.location.href='admin_students.php'">Students</button>
            <button onclick="window.location.href='admin_teachers.php'">Teachers</button>
            <button onclick="window.location.href='admin_subjects.php'" class="active">Subjects</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</body>
</html>
