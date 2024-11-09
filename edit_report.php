<?php
// Include database connection
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

// Check if report ID is provided
if (!isset($_GET['report_id'])) {
    echo 'Report ID is missing.';
    exit;
}

$report_id = $_GET['report_id'];

// Fetch the existing report details
try {
    $stmt = $pdo->prepare("SELECT * FROM student_reports WHERE id = :id");
    $stmt->execute(['id' => $report_id]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
        echo 'Report not found.';
        exit;
    }
} catch (PDOException $e) {
    echo 'Error fetching report: ' . $e->getMessage();
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (empty($_POST['student_id'])) {
        $errors[] = "Student ID is required.";
    }
    if (empty($_POST['report_date'])) {
        $errors[] = "Report Date is required.";
    }

    // Handle the picture upload
    $picture = $report['picture']; // Keep the current picture by default
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == UPLOAD_ERR_OK) {
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['picture']['type'], $allowed_types)) {
            $errors[] = "Invalid file type. Only JPEG, PNG, and GIF images are allowed.";
        } else {
            // Check file size (limit to 5MB)
            if ($_FILES['picture']['size'] > 5 * 1024 * 1024) {
                $errors[] = "File is too large. Maximum size is 5MB.";
            } else {
                $picture = file_get_contents($_FILES['picture']['tmp_name']);
            }
        }
    }

    // If no errors, update the report in the database
    if (empty($errors)) {
        try {
            // Prepare the SQL statement
            $stmt = $pdo->prepare("UPDATE student_reports 
                                   SET student_id = :student_id, admin_id = :admin_id, report_date = :report_date,
                                       admin_notes = :admin_notes, picture = :picture, visit_report = :visit_report 
                                   WHERE id = :id");

            // Execute the query with form data
            $stmt->execute([
                'student_id' => $_POST['student_id'],
                'admin_id' => $admin_id,  // Update with the logged-in admin ID
                'report_date' => $_POST['report_date'],
                'admin_notes' => $_POST['admin_notes'],
                'picture' => $picture,
                'visit_report' => $_POST['visit_report'],
                'id' => $report_id
            ]);

            $success_message = "Student report updated successfully!";
            $form_submitted = true;
        } catch (PDOException $e) {
            $errors[] = "Error updating report: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Report</title>
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
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        #imagePreview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-top: 10px;
            display: none;
        }
        .image-preview-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 form-container">
            <h2>Edit Student Report</h2>

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

            <!-- Student Report Edit Form -->
            <form id="reportForm" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="number" class="form-control" id="student_id" name="student_id" required 
                           value="<?php echo htmlspecialchars($report['student_id']); ?>">
                </div>
                
                <div class="mb-3">
                    <label for="report_date" class="form-label">Report Date</label>
                    <input type="date" class="form-control" id="report_date" name="report_date" required
                           value="<?php echo htmlspecialchars($report['report_date']); ?>">
                </div>

                <div class="mb-3">
                    <label for="admin_notes" class="form-label">Admin Notes</label>
                    <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4"><?php echo htmlspecialchars($report['admin_notes']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="picture" class="form-label">Picture</label>
                    <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                    <small class="text-muted">Max file size: 5MB. Allowed formats: JPEG, PNG, GIF</small>
                    <div class="image-preview-container">
                        <?php if ($report['picture']): ?>
                            <img id="imagePreview" class="img-fluid" alt="Preview" src="data:image/jpeg;base64,<?php echo base64_encode($report['picture']); ?>">
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="visit_report" class="form-label">Visit Report</label>
                    <textarea class="form-control" id="visit_report" name="visit_report" rows="4"><?php echo htmlspecialchars($report['visit_report']); ?></textarea>
                </div>

                <button type="submit" class="btn-submit">Update Report</button>
            </form>
        </div>
    </div>
</div>

<script>
// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// Prevent multiple form submissions
document.getElementById('reportForm').addEventListener('submit', function() {
    // Disable the submit button
    this.querySelector('button[type="submit"]').disabled = true;
});

// Image preview functionality
document.getElementById('picture').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    const file = e.target.files[0];
    
    if (file) {
        // Check file size
        if (file.size > 5 * 1024 * 1024) {
            alert('File is too large. Maximum size is 5MB.');
            this.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Check file type
        if (!file.type.match('image.*')) {
            alert('Only image files are allowed');
            this.value = '';
            preview.style.display = 'none';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Set default date to today
document.getElementById('report_date').valueAsDate = new Date();
</script>

</body>
</html>

<?php require_once 'footer.php'; ?>
