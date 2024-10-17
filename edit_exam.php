<?php
require 'header.php';
require_once 'db.php';
ob_start(); // Start output buffering
if (isset($_GET['id'])) {
    $examId = $_GET['id'];

    // Fetch exam data
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
    $stmt->execute([$examId]);
    $exam = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Handle form submission
        $gpa = $_POST['gpa'];
        $failed_subjects = $_POST['failed_subjects'];
        $passed_subjects = $_POST['passed_subjects'];
        $remarks = $_POST['remarks'];
        $parents_view = $_POST['parents_view'];
        
        // Update exam details
        $stmtUpdate = $pdo->prepare("UPDATE exams SET GPA = ?, failed_subjects = ?, passed_subjects = ?, remarks = ?, parents_view = ? WHERE id = ?");
        $stmtUpdate->execute([$gpa, $failed_subjects, $passed_subjects, $remarks, $parents_view, $examId]);

        header("Location: student_profile.php?query=" . urlencode($exam['student_id'])); // Redirect after update
        exit;
    }
} else {
    // Handle case where no ID is provided
    echo "No exam ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin: 40px auto;
            max-width: 600px;
        }
        .form-container h2 {
            color: #ff385c;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .btn-update {
            width: 100%;
            margin-top: 20px;
            background-color: #ff385c;
            border: none;
            color: white;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn-update:hover {
            background-color: #e0304e;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="form-container">
            <h2>Edit Exam Record</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="number" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($exam['student_id']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="exam_date" class="form-label">Exam Date</label>
                    <input type="date" class="form-control" id="exam_date" name="exam_date" value="<?php echo htmlspecialchars($exam['exam_date']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="gpa" class="form-label">GPA</label>
                    <input type="text" class="form-control" id="gpa" name="gpa" value="<?php echo htmlspecialchars($exam['GPA']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="failed_subjects" class="form-label">Failed Subjects</label>
                    <textarea class="form-control" id="failed_subjects" name="failed_subjects"><?php echo htmlspecialchars($exam['failed_subjects']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="passed_subjects" class="form-label">Passed Subjects</label>
                    <textarea class="form-control" id="passed_subjects" name="passed_subjects"><?php echo htmlspecialchars($exam['passed_subjects']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" required><?php echo htmlspecialchars($exam['remarks']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="parents_view" class="form-label">Parents' View</label>
                    <textarea class="form-control" id="parents_view" name="parents_view"><?php echo htmlspecialchars($exam['parents_view']); ?></textarea>
                </div>
                <button type="submit" class="btn-update">Update</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php include 'footer.php'; ?>
