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
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 80px;
            border-bottom: 1px solid #e5e5e5;
            background-color: white;
        }
        .logo {
            color: #ff385c;
            font-size: 32px;
            font-weight: bold;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            text-decoration: none;
            color: #222;
            font-weight: 500;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .search-bar {
            display: flex;
            justify-content: center;
            padding: 20px 0;
            background-color: #f7f7f7;
        }
        .search-container {
            display: flex;
            align-items: center;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 40px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.05);
        }
        .search-input {
            border: none;
            padding: 14px 24px;
            font-size: 14px;
            outline: none;
            background-color: transparent;
            border-radius: 40px 0 0 40px; /* Rounded corners */
        }
        .search-button {
            background-color: #ff385c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 0 40px 40px 0; /* Rounded corners */
            cursor: pointer;
        }
        .user-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        @media (max-width: 768px) {
            .header {
                padding: 16px 20px;
            }
            .nav-links {
                display: none;
            }
            .search-container {
                flex-direction: column;
                width: 90%;
            }
            .search-input {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #ddd;
            }
        }
        .user-menu-button {
    background-color: #ff385c; /* Primary color for the button */
    color: white; /* Text color */
    display: flex; /* Flex layout for alignment */
    align-items: center; /* Center items vertically */
    border: none; /* Remove default border */
    border-radius: 30px; /* Rounded corners */
    padding: 10px 15px; /* Padding for the button */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Shadow for depth */
    transition: background-color 0.3s, transform 0.2s; /* Smooth transition for hover effects */
}

.user-menu-button:hover {
    background-color: #e63946; /* Darker shade on hover */
    transform: translateY(-2px); /* Lift effect */
}

.user-menu-button:focus {
    outline: none; /* Remove default focus outline */
    box-shadow: 0 0 0 2px rgba(255, 56, 92, 0.5); /* Custom focus outline */
}


.user-photo {
    width: 30px; /* Adjust size of user photo */
    height: 30px;
    border-radius: 50%; /* Circular image */
    margin-right: 8px; /* Spacing between image and name */
}


    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Continental School</div>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="addStudent.php">Add Students</a>
            <a href="add_eca_achievement.php">ECA Achievements</a>
            <a href="add_exam_record.php">Exam Records</a>
        </nav>
        <div class="user-menu">
            <div class="dropdown">
            <div class="dropdown d-flex align-items-center">
    <button class="btn dropdown-toggle user-menu-button" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="<?php echo isset($_SESSION['user_photo']) ? $_SESSION['user_photo'] : 'default-profile.jpg'; ?>" alt="User Photo" class="user-photo">
        <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?>
    </button>
    <ul class="dropdown-menu" aria-labelledby="userMenu">
        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
    </ul>
</div>

        </div>
    </header>
    <div class="search-bar">
        <form class="search-container" method="GET" action="search.php">
            <input type="text" class="search-input" placeholder="Search students" name="query" required aria-label="Search students">
            <button class="search-button" type="submit" aria-label="Search">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 2a9 9 0 100 18 9 9 0 000-18zM22 22l-5.6-5.6" />
</svg>

            </button>
        </form>
    </div>

    <div class="container mt-4">
        <!-- The main content of the page will go here -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
