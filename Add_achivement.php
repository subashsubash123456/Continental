<?php
ob_start(); // Start output buffering

// Include necessary files
include 'header.php';
include 'db.php';

// Check if admin is logged in
if (isset($_SESSION['user_id'])) {
    $admin_id = $_SESSION['user_id'];
} else {
    echo 'User is not logged in. Please log in again.';
    exit;
}

$errors = [];
$success_message = '';
$form_submitted = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (empty($_POST['title'])) {
        $errors[] = "Title is required.";
    }
    if (empty($_POST['date_of_achievement'])) {
        $errors[] = "Date of Achievement is required.";
    }
    if (empty($_POST['student_id'])) {
        $errors[] = "Student ID is required.";
    }

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['photo']['type'];
        $file_size = $_FILES['photo']['size'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        }
        if ($file_size > $max_size) {
            $errors[] = "File size must be less than 5MB.";
        }

        if (empty($errors)) {
            // Read file content as BLOB data
            $photo_data = file_get_contents($_FILES['photo']['tmp_name']);
        }
    }

    // If no errors, insert data into the database
    if (empty($errors)) {
        try {
            // Prepare the SQL statement
            $stmt = $pdo->prepare("INSERT INTO achievement (title, date_of_achievement, awarded_by, photo, student_id, admin_id) 
                                   VALUES (:title, :date_of_achievement, :awarded_by, :photo, :student_id, :admin_id)");

            // Execute the query with form data
            $stmt->execute([
                'title' => $_POST['title'],
                'date_of_achievement' => $_POST['date_of_achievement'],
                'awarded_by' => $_POST['awarded_by'],
                'photo' => isset($photo_data) ? $photo_data : null,
                'student_id' => $_POST['student_id'],
                'admin_id' => $admin_id,
            ]);

            $success_message = "Achievement submitted successfully!";
            $form_submitted = true;

            // Redirect to avoid form resubmission
            header("Location: home.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Error saving achievement: " . $e->getMessage();
        }
    }
}
ob_end_flush(); // End output buffering and flush output
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Achievement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
        .image-preview-container {
            width: 200px;
            height: 200px;
            margin: 10px auto;
            border: 2px dashed #ccc;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .image-preview {
            max-width: 100%;
            max-height: 100%;
            display: none;
        }
        .preview-text {
            color: #6c757d;
            text-align: center;
            padding: 10px;
        }
        .photo-upload-container {
            position: relative;
            margin-bottom: 20px;
        }
        .photo-upload-container .form-control {
            padding-right: 110px;
        }
        .clear-image {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 form-container">
            <h2>Submit Achievement</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <!-- Achievement Submission Form -->
            <form id="achievementForm" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                
                <div class="mb-3">
                    <label for="date_of_achievement" class="form-label">Date of Achievement</label>
                    <input type="date" class="form-control" id="date_of_achievement" name="date_of_achievement" required>
                </div>
                
                <div class="mb-3">
                    <label for="awarded_by" class="form-label">Awarded By</label>
                    <input type="text" class="form-control" id="awarded_by" name="awarded_by">
                </div>

                <!-- Photo upload section -->
                <div class="mb-3">
                    <label class="form-label">Photo</label>
                    <div class="image-preview-container">
                        <img id="preview" class="image-preview" alt="Image Preview">
                        <div class="preview-text">Preview will appear here</div>
                    </div>
                    <div class="photo-upload-container">
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <button type="button" class="clear-image" id="clearImage">Clear</button>
                    </div>
                    <small class="text-muted">Maximum file size: 5MB. Accepted formats: JPG, PNG, GIF</small>
                </div>

                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="number" class="form-control" id="student_id" name="student_id" required>
                </div>

                <button type="submit" class="btn-submit">Submit Achievement</button>
            </form>
        </div>
    </div>
</div>

<script>
// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// Image preview functionality
const photoInput = document.getElementById('photo');
const imagePreview = document.getElementById('preview');
const previewText = document.querySelector('.preview-text');
const clearButton = document.getElementById('clearImage');

photoInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.style.display = 'block';
            imagePreview.src = e.target.result;
            previewText.style.display = 'none';
            clearButton.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

clearButton.addEventListener('click', function() {
    photoInput.value = '';
    imagePreview.style.display = 'none';
    imagePreview.src = '';
    previewText.style.display = 'block';
    clearButton.style.display = 'none';
});
</script>
</body>
</html>
<?php include 'footer.php'; ?>