<?php
session_start(); // Start the session to access user data

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) { // Adjust 'user_id' to your session variable
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Optionally, you can include the database connection here if needed
require 'db.php'; // Ensure this path is correct

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>School Management</title>
    <style>
        /* Custom CSS to style user profile image */
        .user-photo {
            width: 40px; /* Adjust the size as needed */
            height: 40px;
            border-radius: 50%; /* Make it circular */
            margin-right: 10px; /* Spacing between image and name */
        }
        .navbar {
            align-items: center; /* Center items vertically in navbar */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">School System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_student.php">Add New Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_eca_achievement.php">Add ECA Achievements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_exam_record.php">Add Exam Records</a>
                    </li>
                </ul>
                <form class="d-flex me-auto" method="GET" action="search.php">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="query" required>
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>

                <!-- User Profile Section -->
                <div class="dropdown d-flex align-items-center">
                    <!-- Display the user photo -->
                    <img src="<?php echo isset($_SESSION['user_photo']) ? $_SESSION['user_photo'] : 'default-profile.jpg'; ?>" alt="User Photo" class="user-photo">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- The main content of the page will go here -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
