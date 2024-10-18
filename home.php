<?php
// Include the database connection file
include 'db.php';
include 'header.php';

// Fetch student data from the database
try {
    // Join student table with class table to get class names instead of IDs
    $stmt = $pdo->query("
        SELECT s.*, c.class_name
        FROM student s
        JOIN class c ON s.class_id = c.class_id
    ");
    $students = $stmt->fetchAll(); // Fetch all results as an associative array
} catch (PDOException $e) {
    echo "Error fetching student data: " . $e->getMessage(); // Handle errors
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin: 15px; /* Add margin to each card */
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center; /* Center the text inside the card */
            height: 340px; /* Adjusted height of the card */
        }
        .card img {
            border-radius: 50%; /* Make the image round */
            height: 75px; /* Smaller fixed height for the image */
            width: 75px; /* Set a fixed width for the rounded image */
            object-fit: cover; /* Maintain aspect ratio */
            margin: 15px auto; /* Center the image and add margin */
        }
        .card-header {
            border-radius: 20px;
            background-color: #ff385c; /* Match header color */
            color: white; /* Text color for the header */
            font-weight: bold; /* Bold text */
            height: 50px; /* Decreased height for the header */
            line-height: 50px; /* Center text vertically */
        }
        h2 {
            color: #ff385c;
            text-align: center;
            margin-bottom: 20px;
        }
        .edit-button {
            margin-top: 10px; /* Space above the button */
            background-color: #ff385c; /* Match button color */
            color: white; /* Text color */
            border: none; /* Remove default border */
            border-radius: 5px; /* Rounded corners */
            padding: 8px 12px; /* Padding for the button */
            cursor: pointer; /* Change cursor to pointer */
            transition: background-color 0.3s; /* Transition for hover effect */
            text-decoration: none; /* Remove underline from text */
            display: inline-block; /* Align the button */
        }
        .edit-button:hover {
            background-color: #e0304e; /* Darker shade on hover */
        }
        .card-body {
            padding: 5px; /* Adjust padding to fit content */
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
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <?php foreach ($students as $student): ?>
            <div class="col-md-4 col-sm-6"> <!-- Responsive columns: 3 on md and 2 on sm -->
                <div class="card">
                    <div class="card-header">
                        <?php echo htmlspecialchars($student['full_name']); ?>
                    </div>
                    <?php if ($student['profile_image']): ?>
                        <img src="<?php echo htmlspecialchars($student['profile_image']); ?>" alt="Profile Image">
                    <?php else: ?>
                        <img src="placeholder-image.jpg" alt="No Image"> <!-- Placeholder for no image -->
                    <?php endif; ?>
                    <div class="card-body">
                        <p class="card-text"><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone_number']); ?></p>
                        <p class="card-text"><strong>Class:</strong> <?php echo htmlspecialchars($student['class_name']); ?></p>
                        <p class="card-text"><strong>Address:</strong> <?php echo htmlspecialchars($student['address']); ?></p>
                        <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" class="edit-button">Edit Details</a>
                        
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include 'footer.php';
?>