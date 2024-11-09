<?php include "header.php";
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection configuration
try {
    require_once 'db.php';
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to validate file upload
function validateImageUpload($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if ($file['size'] > $maxSize) {
        return "File is too large. Maximum size is 5MB.";
    }
    
    if (!in_array($file['type'], $allowedTypes)) {
        return "Invalid file type. Only JPG, PNG and GIF are allowed.";
    }
    
    return true;
}

// Function to sanitize file name
function sanitizeFileName($fileName) {
    $fileName = preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
    return time() . '_' . $fileName;
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success_message = '';

try {
    // Fetch user details
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception("User not found!");
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $old_password = trim($_POST['old_password'] ?? '');
        $new_password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        
        // Validate old password
        if (!password_verify($old_password, $user['password'])) {
            $errors[] = "The old password is incorrect.";
        }

        // Validate new password if provided
        if (!empty($new_password)) {
            if ($new_password !== $confirm_password) {
                $errors[] = "New password and confirmation do not match.";
            }
            
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_password)) {
                $errors[] = "Password must be at least 8 characters long, and include uppercase, lowercase, number, and special character.";
            }
        }

        // Handle image upload
        $targetFilePath = $user['image']; // Default to current image
        
        if (!empty($_FILES['image']['name'])) {
            $imageValidation = validateImageUpload($_FILES['image']);
            
            if ($imageValidation === true) {
                $targetDir = "uploads/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                $fileName = sanitizeFileName($_FILES['image']['name']);
                $targetFilePath = $targetDir . $fileName;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    $errors[] = "Error uploading file.";
                }
            } else {
                $errors[] = $imageValidation;
            }
        }

        // Update profile if no errors
        if (empty($errors)) {
            $pdo->beginTransaction();
            try {
                if (!empty($new_password)) {
                    // Update both password and image
                    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE admin SET password = ?, image = ? WHERE id = ?");
                    $stmt->execute([$hashedPassword, $targetFilePath, $user_id]);
                } else {
                    // Update only image
                    $stmt = $pdo->prepare("UPDATE admin SET image = ? WHERE id = ?");
                    $stmt->execute([$targetFilePath, $user_id]);
                }
                
                $pdo->commit();
                $success_message = "Profile updated successfully!";
                
                // Refresh user data
                $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = "Error updating profile: " . $e->getMessage();
            }
        }
    }
} catch (Exception $e) {
    $errors[] = "System error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Profile</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .change-profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .circular-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid #007bff;
            object-fit: cover;
            display: block;
            margin: 20px auto;
        }
        .custom-file-upload {
            display: inline-block;
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid #007bff;
            border-radius: 4px;
            background-color: #ffffff;
            color: #007bff;
            transition: all 0.3s ease;
        }
        .custom-file-upload:hover {
            background-color: #007bff;
            color: white;
        }
        input[type="file"] {
            display: none;
        }
        .password-requirements {
            font-size: 0.85em;
            color: #6c757d;
            margin-top: 5px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .logout-button2{
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
            width:8%;;
        }
        .logout-button2:hover {
            background-color: #e63946;
            transform: translateY(-2px);
        }
        .logout-button2:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 56, 92, 0.5);
        }
      
    </style>
</head>
<body>
    <!-- Right-aligned and styled "Add User" button -->
<div class="text-right">
    <a href="signup.php" class="logout-button2">Add User</a>
</div>
    <div class="change-profile-container">
        <h2 class="text-center mb-4">Change Profile</h2>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="old_password">Current Password:</label>
                <input type="password" id="old_password" name="old_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" class="form-control">
                <div class="password-requirements">
                    Password must contain at least 8 characters, including uppercase, lowercase, number, and special character.
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
            </div>

            <div class="form-group text-center">
                <img id="imagePreview" class="circular-image" 
                     src="<?php echo htmlspecialchars($user['image'] ?: 'default-profile.png'); ?>" 
                     alt="Profile Image Preview">
                     
                <label for="image" class="d-block mt-3 custom-file-upload">
                    Choose Profile Picture
                </label>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const imagePreview = document.getElementById('imagePreview');
                imagePreview.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        // Validate confirm password
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
