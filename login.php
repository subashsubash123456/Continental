<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
require 'db.php'; // Ensure this path is correct

// Initialize error messages
$loginError = '';

// Process the form when it is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Prepare SQL statement to check the user's credentials
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE phone = ? AND role = ?");
    $stmt->execute([$phone, $role]);
    $user = $stmt->fetch();

    // Check if user exists and verify the password
    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        session_start();
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        $_SESSION['user_name'] = $user['name']; // Store user name
        $_SESSION['role'] = $user['role']; // Store the role
        $_SESSION['user_photo'] = $user['image']; // Store user photo if available
        
        header("Location: header.php"); // Redirect to header page
        exit();
    } else {
        $loginError = 'Invalid phone number, role, or password.';
    }
}

// Close the database connection
$pdo = null; // This will close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center">Login</h2>
        <?php if ($loginError): ?>
            <div class="alert alert-danger">
                <?php echo $loginError; ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="">Select Role</option>
                    <option value="Class Teacher">Class Teacher</option>
                    <option value="Principal">Principal</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <div class="text-center mt-3">
            <a href="signup.php">Don't have an account? Sign up</a>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
