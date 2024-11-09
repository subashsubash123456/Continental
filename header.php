<?php
session_start(); // Start the session to access user data

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) { // Adjust 'user_id' to your session variable
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Optionally, you can include the database connection here if needed
require 'db.php'; // Ensure this path is correct

// Initialize ECA data session variable
if (!isset($_SESSION['eca_data'])) {
    $_SESSION['eca_data'] = []; // Initialize as an empty array
}
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
            flex-direction: column;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #e5e5e5;
            background-color: white;
        }
        .logo {
            color: #ff385c;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 12px;
        }
        .nav-links {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .nav-links a {
            text-decoration: none;
            color: #222;
            font-weight: 500;
            font-size: 14px;
            position: relative;
            padding: 6px 12px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .nav-links a:hover,
        .nav-links a.active {
            background-color: #ff385c;
            color: white;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 12px;
        }
        .search-bar {
    display: flex;
    justify-content: center;
    padding: 8px 0;
    background-color: #f7f7f7;
    width: 100%; /* Ensure it takes full width */
}

.search-container {
    display: flex;
    align-items: center;
    background-color: white;
    border: 1px solid black;
    border-radius: 30px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08), 0 4px 12px rgba(0, 0, 0, 0.05);
    width: 100%; /* Ensure it takes full width */
    max-width: 400px; /* Maintain a max width */
}

.search-input {
    border-radius: 30px 0 0 30px;
    padding: 8px 16px;
    font-size: 14px;
    outline: none;
    background-color: transparent;
    flex: 1; /* Allow it to take the available space */
    min-width: 0; /* Prevents the input from overflowing */
}

.search-button {
    background-color: #ff385c;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 0 30px 30px 0;
    cursor: pointer;
}

@media (min-width: 769px) {
    .search-bar {
        margin-left: auto; /* Align it to the right on larger screens */
        margin-right: 16px; /* Add some space from the right */
    }
}

        .user-photo {
            width: 40px;  /* Increased size */
            height: 40px; /* Increased size */
            border-radius: 50%;
            object-fit: cover;
        }
        .logout-button {
            background-color: #ff385c;
            color: white;
            display: flex;
            align-items: center;
            border: none;
            border-radius: 30px;
            padding: 8px 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s, transform 0.2s;
            font-size: 14px;
            text-decoration: none;
        }
        .logout-button:hover {
            background-color: #e63946;
            transform: translateY(-2px);
        }
        .logout-button:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 56, 92, 0.5);
        }
        @media (min-width: 769px) {
            .header {
                flex-direction: row;
                justify-content: space-between;
            }
            .logo {
                margin-bottom: 0;
            }
            .nav-links {
                justify-content: flex-start;
            }
            .user-menu {
                margin-top: 0;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Continental School</div>
        <nav class="nav-links">
            <a href="home.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'home.php') ? 'active' : ''; ?>">Home</a>
            <a href="addStudent.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'addStudent.php') ? 'active' : ''; ?>">Add Students</a>
            <a href="add_achivement.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'add_achivement.php') ? 'active' : ''; ?>">ECA Achievements</a>
            <a href="exam.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'exam.php') ? 'active' : ''; ?>">Exam Records</a>
            <a href="report.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'report.php') ? 'active' : ''; ?>">Class Teacher Report</a>
            
        </nav>
        <div class="user-menu">
    <div class="d-flex align-items-center">
        <!-- Wrap the user photo with a link to profile.php -->
        <a href="profile.php">
            <img src="<?php echo isset($_SESSION['user_photo']) ? $_SESSION['user_photo'] : 'default-profile.jpg'; ?>" 
                 alt="User Photo" 
                 class="user-photo me-2">
        </a>
        <span class="me-2"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?></span>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</div>

    </header>
    <div class="search-bar">
        <form class="search-container" method="GET" action="search.php">
            <input type="text" class="search-input" placeholder="Search students" name="query" required aria-label="Search students">
            <button class="search-button" type="submit" aria-label="Search">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
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
