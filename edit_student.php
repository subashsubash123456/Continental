<?php
ob_start(); // Start output buffering

// Include database connection
require_once 'db.php';
require_once 'header.php';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the logged-in user ID (admin)
} else {
    // Handle the case when user_id is not in the session
    echo 'User is not logged in. Please log in again.';
    exit; // Stop further execution
}

// Initialize variables
$student = null;
$errors = [];
$success_message = '';

// Check if student_id is set in the URL
if (isset($_GET['id'])) {
    $student_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Fetch student data from the database
    try {
        $stmt = $pdo->prepare("SELECT * FROM student WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            throw new Exception("Student not found.");
        }
    } catch (Exception $e) {
        $errors[] = "Error fetching student data: " . $e->getMessage();
    }
}

// Validation functions
function validateRequired($field, $fieldName) {
    global $errors;
    if (empty($_POST[$field])) {
        $errors[] = "$fieldName is required.";
    }
}

function validatePhone($field) {
    global $errors;
    if (!preg_match("/^98\d{8}$/", $_POST[$field])) {
        $errors[] = "Phone number must start with 98 and have 10 digits.";
    }
}

// Handle form submission for updating student details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($student_id)) {
    // Validate form data
    validateRequired('full_name', 'Full Name');
    validateRequired('class_id', 'Class');
    validateRequired('section_id', 'Section');
    validateRequired('address', 'Address');
    validateRequired('street', 'Street');
    validateRequired('phone_number', 'Phone Number');
    validatePhone('phone_number');

    if (empty($errors)) {
        try {
            // Include admin_id in the update query
            $stmt = $pdo->prepare("UPDATE student SET 
                full_name = :full_name, 
                phone_number = :phone_number, 
                class_id = :class_id, 
                section_id = :section_id, 
                address = :address, 
                street = :street, 
                words_from_class_teacher = :words_from_class_teacher, 
                words_from_principal = :words_from_principal, 
                education_status = :education_status, 
                remarks = :remarks,
                admin_id = :admin_id  -- Track the admin updating the record
                WHERE student_id = :student_id");

            $stmt->execute([
                'full_name' => $_POST['full_name'],
                'phone_number' => $_POST['phone_number'],
                'class_id' => $_POST['class_id'],
                'section_id' => $_POST['section_id'],
                'address' => $_POST['address'],
                'street' => $_POST['street'],
                'words_from_class_teacher' => $_POST['words_from_class_teacher'],
                'words_from_principal' => $_POST['words_from_principal'],
                'education_status' => $_POST['education_status'],
                'remarks' => $_POST['remarks'],
                'admin_id' => $user_id, // Track the admin ID
                'student_id' => $student_id
            ]);

            $success_message = "Student information updated successfully.";

            // Refresh student data
            $stmt = $pdo->prepare("SELECT * FROM student WHERE student_id = :student_id");
            $stmt->execute(['student_id' => $student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $errors[] = "Error updating student data: " . $e->getMessage();
        }
    }
}

// Include header after all potential redirects
require_once 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .user-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
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
        .rounded-preview {
            display: block;
            margin-top: 15px;
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 form-container">
            <h2>Edit Student Details</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($student): ?>
                <!-- Edit Student Form -->
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" pattern="^98\d{8}$" value="<?php echo htmlspecialchars($student['phone_number']); ?>" required>
                            <small class="form-text text-muted">Start with 98, 10 digits</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="class_id" class="form-label">Class</label>
                            <select class="form-select" id="class_id" name="class_id" required>
                                <option value="">Select a class</option>
                                <option value="1" <?php echo ($student['class_id'] == 1) ? 'selected' : ''; ?>>PLAY</option>
                                <option value="2" <?php echo ($student['class_id'] == 2) ? 'selected' : ''; ?>>Nursery</option>
                                <option value="3" <?php echo ($student['class_id'] == 3) ? 'selected' : ''; ?>>KG</option>
                                <option value="4" <?php echo ($student['class_id'] == 4) ? 'selected' : ''; ?>>LKG</option>
                                <?php for ($i = 1; $i <= 10; $i++) { echo "<option value='" . ($i + 4) . "' " . (($student['class_id'] == ($i + 4)) ? 'selected' : '') . ">Class $i</option>"; } ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="section_id" class="form-label">Section</label>
                            <select class="form-select" id="section_id" name="section_id" required>
                                <option value="">Select a section</option>
                                <option value="1" <?php echo ($student['section_id'] == 1) ? 'selected' : ''; ?>>A</option>
                                <option value="2" <?php echo ($student['section_id'] == 2) ? 'selected' : ''; ?>>B</option>
                                <option value="3" <?php echo ($student['section_id'] == 3) ? 'selected' : ''; ?>>C</option>
                                <option value="4" <?php echo ($student['section_id'] == 4) ? 'selected' : ''; ?>>D</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($student['address']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="street" class="form-label">Street</label>
                        <input type="text" class="form-control" id="street" name="street" value="<?php echo htmlspecialchars($student['street']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="words_from_class_teacher" class="form-label">Words from Class Teacher</label>
                        <textarea class="form-control" id="words_from_class_teacher" name="words_from_class_teacher" rows="3"><?php echo htmlspecialchars($student['words_from_class_teacher']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="words_from_principal" class="form-label">Words from Principal</label>
                        <textarea class="form-control" id="words_from_principal" name="words_from_principal" rows="3"><?php echo htmlspecialchars($student['words_from_principal']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="education_status" class="form-label">Education Status</label>
                        <select class="form-select" id="education_status" name="education_status" required>
                            <option value="enrolled" <?php echo ($student['education_status'] == 'enrolled') ? 'selected' : ''; ?>>Enrolled</option>
                            <option value="completed" <?php echo ($student['education_status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php echo htmlspecialchars($student['remarks']); ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Update Student</button>
                </form>
            <?php else: ?>
                <p class="alert alert-danger">Student not found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
