<?php include 'header.php'; ?>
<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Achievement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .form-container h2 {
            color: #ff385c;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-label {
            font-weight: 600;
            color: #ff385c;
        }
        .btn-submit {
            width: 100%;
            margin-top: 20px;
            background-color: #ff385c;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #e0304e;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 form-container">
            <h2>Add Achievement</h2>

            <!-- Error and Success Messages -->
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Initialize an array to hold error messages
                $errors = [];

                // Validate required fields
                function validateRequired($field, $fieldName) {
                    global $errors;
                    if (empty($_POST[$field])) {
                        $errors[] = "$fieldName is required.";
                    }
                }

                // Validate the date format
                function validateDate($date, $fieldName) {
                    global $errors;
                    if (!DateTime::createFromFormat('Y-m-d', $date)) {
                        $errors[] = "$fieldName must be a valid date.";
                    }
                }

                validateRequired('title', 'Achievement Title');
                validateRequired('date_of_achievement', 'Date of Achievement');
                validateRequired('awarded_by', 'Awarded By');
                validateRequired('student_id', 'Student ID');

                // Validate date of achievement
                validateDate($_POST['date_of_achievement'], 'Date of Achievement');

                // Check for photo upload
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = "Photo upload failed.";
                }

                // If there are no errors, proceed to insert the data
                if (empty($errors)) {
                    $title = $_POST['title'];
                    $date_of_achievement = $_POST['date_of_achievement'];
                    $awarded_by = $_POST['awarded_by'];
                    $student_id = $_POST['student_id'];

                    // Handle photo upload
                    $photo = null;
                    if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                        $photo = file_get_contents($_FILES['photo']['tmp_name']); // Read the photo file
                    }

                    // Prepare the SQL statement
                    $stmt = $conn->prepare("INSERT INTO achievement (title, date_of_achievement, awarded_by, photo, student_id) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssi", $title, $date_of_achievement, $awarded_by, $photo, $student_id);

                    // Execute the statement
                    if ($stmt->execute()) {
                        // Redirect to header.php after successful submission
                        header("Location: header.php");
                        exit();
                    } else {
                        echo "Error: " . $stmt->error;
                    }

                    // Close the statement
                    $stmt->close();
                } else {
                    // Handle errors and display them
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                }
            }
            ?>

            <!-- Achievement Form -->
            <form method="post" action="addAchievement.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Achievement Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="date_of_achievement" class="form-label">Date of Achievement</label>
                    <input type="date" class="form-control" id="date_of_achievement" name="date_of_achievement" required>
                </div>
                <div class="mb-3">
                    <label for="awarded_by" class="form-label">Awarded By</label>
                    <input type="text" class="form-control" id="awarded_by" name="awarded_by" required>
                </div>
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="number" class="form-control" id="student_id" name="student_id" required>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Photo</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-submit">Submit Achievement</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
