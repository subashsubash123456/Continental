<?php
ob_start();  // Start output buffering

include "header.php"; 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure session is started

// Include the database connection file
require 'db.php'; // Ensure this path is correct

// Initialize error messages
$errors = [];

// Only allow Admin to access the signup form
if ($_SESSION['role'] != 'Admin') {
    // Redirect if the user is not an admin
    header("Location: home.php");
    exit();
}

// Process the form when it is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $image = $_FILES['image']['name'];

    // Validate phone number
    if (!preg_match('/^98\d{8}$/', $phone)) {
        $errors[] = "Phone number must start with 98 and be 10 digits long.";
    }

    // Validate password
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters long, and include uppercase, lowercase, number, and special character.";
    }

    // Handle file upload and circular image display
    if (empty($errors)) {
        if (!empty($image)) {
            $targetDir = "uploads/";
            $targetFilePath = $targetDir . basename($image);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                // Log success
                error_log("File uploaded successfully: " . $targetFilePath);
            } else {
                $errors[] = "Error uploading file.";
            }
        } else {
            $targetFilePath = null; // No image uploaded
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement using PDO
        $stmt = $pdo->prepare("INSERT INTO admin (name, role, password, phone, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $role, $hashedPassword, $phone, $targetFilePath]);

        // Check if the insert was successful
        if ($stmt) {
            // Redirect to home page after a successful signup
            header("Location: home.php");
            exit(); // Make sure to exit after redirecting
        } else {
            echo "<div class='alert alert-danger'>Error: Could not insert the record.</div>";
        }
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
    <title>Signup Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .signup-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .circular-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid #007bff; /* Bootstrap primary color */
            object-fit: cover;
            display: none; /* Hide initially */
            margin: 10px auto; /* Center the image */
        }
        .error {
            color: red;
        }
        .file-input-label {
            display: block;
            text-align: center; /* Center the label */
            margin-bottom: 10px; /* Space between label and input */
        }
        .file-input-container {
            text-align: center; /* Center align the container */
        }
        /* Styling the file input to hide the default input */
        .custom-file-upload {
            display: inline-block;
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid #007bff;
            border-radius: 4px;
            background-color: #ffffff; /* White background */
            color: #007bff; /* Bootstrap primary color */
            transition: background-color 0.3s, color 0.3s;
        }
        /* Hover effect for the file input button */
        .custom-file-upload:hover {
            background-color: #007bff; /* Change background on hover */
            color: white; /* Change text color on hover */
        }
        /* Hide the default file input */
        input[type="file"] {
            display: none; /* Hide the file input */
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2 class="text-center">Register Form</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Only show this form if the user is an Admin -->
        <?php if ($_SESSION['role'] == 'Admin'): ?>
            <form action="signup.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

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
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>

                <div class="form-group file-input-container">
                    <label class="file-input-label" for="image">Profile Image:</label>
                    <label for="image" class="custom-file-upload">
                        Choose File
                    </label>
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                    <img id="imagePreview" class="circular-image" alt="Profile Image Preview">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Signup</button>
            </form>
        <?php else: ?>
            <p class="text-center">You do not have permission to access this page. Only admins can sign up new users.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function previewImage(event) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.src = URL.createObjectURL(event.target.files[0]);
            imagePreview.style.display = 'block'; // Show the image preview
        }
    </script>
</body>
</html>
