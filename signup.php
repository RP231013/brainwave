<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function toggleUserType() {
            var userType = document.getElementById('userType').value;
            document.getElementById('student-form').style.display = (userType === 'student') ? 'block' : 'none';
            document.getElementById('teacher-form').style.display = (userType === 'teacher') ? 'block' : 'none';
            document.getElementById('admin-form').style.display = (userType === 'admin') ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="glass-container">

        <div class="title-block">
            <img src="logo.png" alt="Brainwave Logo" class="logo">
            <h1>Sign Up</h1>
        </div>

        <form action="submit_signup.php" method="post" class="glass-form">
            <!-- Left Column: Fields -->
            <div class="left-column">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <label for="userType">User Type:</label>
                <select id="userType" name="userType" onchange="toggleUserType()" required>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            
            <div class="right-column">
                
                <div id="student-form" style="display:none;">
                    <h3>Select Subjects (Select at least 7)</h3>
                    <?php
                    require_once "config.php";
                    $sql = "SELECT subID, subName FROM Subjects";
                    $result = mysqli_query($link, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $checked = ($row['subName'] == "Afrikaans" || $row['subName'] == "English") ? "checked" : "";
                            echo '<label><input type="checkbox" name="subjects[]" value="' . $row['subID'] . '" ' . $checked . '> ' . $row['subName'] . '</label><br>';
                        }
                    } else {
                        echo "<p>No subjects available</p>";
                    }
                    ?>
                </div>

                
                <div id="teacher-form" style="display:none;">
                    <h3>Select Subjects You'll Be Teaching</h3>
                    <?php
                    
                    $result = mysqli_query($link, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<label><input type="checkbox" name="subjects[]" value="' . $row['subID'] . '"> ' . $row['subName'] . '</label><br>';
                        }
                    }
                    ?>
                </div>

                
                <div id="admin-form" style="display:block;">
                    <h3>Admin Registration</h3>
                    <p>No extra fields for admin users.</p>
                </div>
            </div>

            
            <div class="submit-row">
                <button type="submit">Sign Up</button>
                <p>Already have an account? <a href="index.php">Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
