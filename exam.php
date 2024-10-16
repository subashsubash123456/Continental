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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (empty($_POST['student_id'])) {
        $errors[] = "Student ID is required.";
    }
    if (empty($_POST['exam_date'])) {
        $errors[] = "Exam Date is required.";
    }
    
    // Validate GPA format if provided
    if (!empty($_POST['GPA'])) {
        if (!is_numeric($_POST['GPA']) || $_POST['GPA'] < 0 || $_POST['GPA'] > 4.00) {
            $errors[] = "GPA must be a number between 0 and 4.00";
        }
    }

    // If no errors, insert data into the database
    if (empty($errors)) {
        try {
            // Prepare the SQL statement
            $stmt = $pdo->prepare("INSERT INTO exams (student_id, admin_id, exam_date, GPA, failed_subjects, 
                                                    passed_subjects, remarks, parents_view) 
                                 VALUES (:student_id, :admin_id, :exam_date, :GPA, :failed_subjects, 
                                         :passed_subjects, :remarks, :parents_view)");

            // Execute the query with form data
            $stmt->execute([
                'student_id' => $_POST['student_id'],
                'admin_id' => $admin_id,
                'exam_date' => $_POST['exam_date'],
                'GPA' => $_POST['GPA'],
                'failed_subjects' => $_POST['failed_subjects'],
                'passed_subjects' => $_POST['passed_subjects'],
                'remarks' => $_POST['remarks'],
                'parents_view' => $_POST['parents_view']
            ]);

            $success_message = "Exam record submitted successfully!";
            $form_submitted = true;
        } catch (PDOException $e) {
            $errors[] = "Error saving exam record: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Exam Record</title>
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
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 form-container">
            <h2>Submit Exam Record</h2>

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

            <!-- Exam Record Submission Form -->
            <form id="examForm" method="post">
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="number" class="form-control" id="student_id" name="student_id" required>
                </div>
                
                <div class="mb-3">
                    <label for="exam_date" class="form-label">Exam Date</label>
                    <input type="date" class="form-control" id="exam_date" name="exam_date" required>
                </div>
                
                <div class="mb-3">
                    <label for="GPA" class="form-label">GPA (0.00 - 4.00)</label>
                    <input type="number" class="form-control" id="GPA" name="GPA" step="0.01" min="0" max="4.00">
                </div>

                <div class="mb-3">
                    <label for="failed_subjects" class="form-label">Failed Subjects</label>
                    <textarea class="form-control" id="failed_subjects" name="failed_subjects" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="passed_subjects" class="form-label">Passed Subjects</label>
                    <textarea class="form-control" id="passed_subjects" name="passed_subjects" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="parents_view" class="form-label">Parents' View</label>
                    <textarea class="form-control" id="parents_view" name="parents_view" rows="3"></textarea>
                </div>

                <button type="submit" class="btn-submit">Submit Exam Record</button>
            </form>
        </div>
    </div>
</div>

<script>
// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// If form was successfully submitted, clear the form
<?php if ($form_submitted): ?>
document.getElementById('examForm').reset();
<?php endif; ?>

// Prevent multiple form submissions
document.getElementById('examForm').addEventListener('submit', function() {
    // Disable the submit button
    this.querySelector('button[type="submit"]').disabled = true;
});

// Format GPA input to ensure it has exactly 2 decimal places
document.getElementById('GPA').addEventListener('change', function() {
    if (this.value !== '') {
        this.value = parseFloat(this.value).toFixed(2);
    }
});
</script>

</body>
</html>

<?php require_once 'footer.php'; ?>