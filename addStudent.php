<?php include 'header.php';?>
<?php include 'db.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Form</title>
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
            color: #007bff;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-label {
            font-weight: 600;
        }
        .btn-submit {
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 form-container">
            <h2>Student Registration</h2>
            <?php
            // PHP code for form processing and validation (same as before)
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $errors = [];

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

                validateRequired('full_name', 'Full Name');
                validateRequired('class_id', 'Class');
                validateRequired('section_id', 'Section');
                validateRequired('address', 'Address');
                validateRequired('street', 'Street');
                validateRequired('phone_number', 'Phone Number');
                validatePhone('phone_number');

                if (empty($errors)) {
                    echo '<div class="alert alert-success">Form submitted successfully!</div>';
                } else {
                    echo '<div class="alert alert-danger"><ul>';
                    foreach ($errors as $error) {
                        echo "<li>$error</li>";
                    }
                    echo '</ul></div>';
                }
            }
            ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" pattern="^98\d{8}$" required>
                        <small class="form-text text-muted">Start with 98, 10 digits</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select class="form-select" id="class_id" name="class_id" required>
                            <option value="">Select a class</option>
                            <option value="1">PLAY</option>
                            <option value="2">Nursery</option>
                            <option value="3">KG</option>
                            <option value="4">LKG</option>
                            <?php
                            for ($i = 1; $i <= 10; $i++) {
                                echo "<option value='" . ($i + 4) . "'>Class $i</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="section_id" class="form-label">Section</label>
                        <select class="form-select" id="section_id" name="section_id" required>
                            <option value="">Select a section</option>
                            <option value="1">Rose</option>
                            <option value="2">Lily</option>
                            <option value="3">Daisy</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="profile_image" class="form-label">Profile Image URL</label>
                    <input type="url" class="form-control" id="profile_image" name="profile_image">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="street" class="form-label">Street</label>
                        <input type="text" class="form-control" id="street" name="street" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="words_from_class_teacher" class="form-label">Words from Class Teacher</label>
                    <textarea class="form-control" id="words_from_class_teacher" name="words_from_class_teacher" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label for="words_from_principal" class="form-label">Words from Principal</label>
                    <textarea class="form-control" id="words_from_principal" name="words_from_principal" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label for="education_status" class="form-label">Education Status</label>
                    <input type="text" class="form-control" id="education_status" name="education_status">
                </div>
                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-submit">Submit</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>