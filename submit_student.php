<?php
// Include the database connection file
include 'db.php';
session_start(); // Start the session to access user data

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $full_name = $_POST['full_name'];
    $class_id = $_POST['class_id'];
    $section_id = $_POST['section_id'];
    $address = $_POST['address'];
    $street = $_POST['street'];
    $phone_number = $_POST['phone_number'];
    $words_from_class_teacher = $_POST['words_from_class_teacher'];
    $words_from_principal = $_POST['words_from_principal'];
    $education_status = $_POST['education_status'];
    $remarks = $_POST['remarks'];
    
    // Get the admin ID from the session
    $admin_id = $_SESSION['user_id'];

    // Handle profile image upload
    $profile_image = '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file;
        } else {
            echo "Error uploading the profile image.";
        }
    }

    // Prepare SQL statement to insert the student data
    $sql = "INSERT INTO student 
                (full_name, class_id, section_id, profile_image, address, street, phone_number, words_from_class_teacher, words_from_principal, education_status, remarks, admin_id) 
            VALUES 
                (:full_name, :class_id, :section_id, :profile_image, :address, :street, :phone_number, :words_from_class_teacher, :words_from_principal, :education_status, :remarks, :admin_id)";
    
    // Execute the prepared statement
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':full_name' => $full_name,
            ':class_id' => $class_id,
            ':section_id' => $section_id,
            ':profile_image' => $profile_image,
            ':address' => $address,
            ':street' => $street,
            ':phone_number' => $phone_number,
            ':words_from_class_teacher' => $words_from_class_teacher,
            ':words_from_principal' => $words_from_principal,
            ':education_status' => $education_status,
            ':remarks' => $remarks,
            ':admin_id' => $admin_id // Include admin_id in the insert
        ]);
        
        // After successful form submission, redirect to the signup page
        header("Location: addStudent.php?success=1");
        exit(); // Terminate script after redirection
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}
?>
