<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="index.css"> 
</head>
<body>
    <div class="glass-container">
    <div class="title-block">
            <img src="logo.png" alt="Brainwave Logo" class="logo">
        </div>
        <h1>Login</h1>
        <form action="submit_login.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <a href="signup.php">Don't have an account? Sign Up</a>
    </div>
</body>
</html>
