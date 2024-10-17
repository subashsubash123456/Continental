<?php
// Include the database connection file and header
include 'db.php';
include 'header.php';

// Initialize variables
$student = null;
$achievements = [];
$exams = [];
$reports = [];

// Check if a search term is provided
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $query = $_GET['query'];

    // Fetch student data from the database
    try {
        // Fetch student details with joins for class, section, and admin
        $stmt = $pdo->prepare("
            SELECT s.*, c.class_name, sec.section_name, a.name AS admin_name,
                   s.words_from_class_teacher, s.words_from_principal,
                   s.education_status, s.remarks, s.street
            FROM student s
            JOIN class c ON s.class_id = c.class_id
            JOIN section sec ON s.section_id = sec.section_id
            LEFT JOIN admin a ON s.admin_id = a.id
            WHERE s.full_name LIKE ?
        ");
        $searchTerm = "%" . $query . "%";
        $stmt->execute([$searchTerm]);
        $student = $stmt->fetch();

        if ($student) {
            // Fetch achievements
            $stmtAchievements = $pdo->prepare("SELECT * FROM achievement WHERE student_id = ?");
            $stmtAchievements->execute([$student['student_id']]);
            $achievements = $stmtAchievements->fetchAll();

            // Updated exam query to include admin information
            $stmtExams = $pdo->prepare("
                SELECT e.*, a.name AS admin_name, e.id AS exam_id
                FROM exams e 
                LEFT JOIN admin a ON e.admin_id = a.id 
                WHERE e.student_id = ?
                ORDER BY e.exam_date DESC
            ");
            $stmtExams->execute([$student['student_id']]);
            $exams = $stmtExams->fetchAll();

            // Fetch student reports
            $stmtReports = $pdo->prepare("SELECT * FROM student_reports WHERE student_id = ?");
            $stmtReports->execute([$student['student_id']]);
            $reports = $stmtReports->fetchAll();
        }
    } catch (PDOException $e) {
        echo "Error fetching student data: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-header {
            background-color: #ff385c;
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px;
            position: relative;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border: none;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #2c3e50;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .info-label {
            font-weight: 600;
            color: #7f8c8d;
        }
        .scrollable-wrapper {
            max-height: 300px;
            overflow-y: auto;
            padding: 15px;
        }
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            background-color: #fff;
            color: #ff385c;
        }
        .btn-edit {
            background-color: #ff385c;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        .btn-edit:hover {
            background-color: #e6324f;
            color: white;
        }
        .badge {
            padding: 0.5em 1em;
            font-weight: 500;
        }
        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }
        .card-header {
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        .student-id {
            position: absolute;
            top: 10px;
            right: 20px;
            background-color: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <?php if ($student): ?>
        <!-- Main Profile Card -->
        <div class="card mb-4">
            <div class="profile-header">
                <div class="student-id">
                    ID: <?php echo htmlspecialchars($student['student_id']); ?>
                </div>
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="<?php echo htmlspecialchars($student['profile_image'] ?: 'uploads/default.png'); ?>" 
                             alt="Profile Image" class="profile-img">
                    </div>
                    <div class="col">
                        <h2 class="mb-2"><?php echo htmlspecialchars($student['full_name']); ?></h2>
                        <p class="mb-1">
                            <i class="bi bi-telephone-fill me-2"></i>
                            <?php echo htmlspecialchars($student['phone_number']); ?>
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-book-fill me-2"></i>
                            <?php echo htmlspecialchars($student['class_name'] . ' - ' . $student['section_name']); ?>
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-geo-alt-fill me-2"></i>
                            <?php echo htmlspecialchars($student['address']); ?>
                            <?php if ($student['street']): ?>
                                <br><small class="ms-4"><?php echo htmlspecialchars($student['street']); ?></small>
                            <?php endif; ?>
                        </p>
                        <span class="status-badge">
                            <i class="bi bi-mortarboard-fill me-2"></i>
                            <?php echo htmlspecialchars($student['education_status']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information Section -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="section-title mb-0">
                    <i class="bi bi-journal-text me-2"></i>Academic Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Teacher's Words -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="info-label">Class Teacher's Words</h6>
                                <p><?php echo nl2br(htmlspecialchars($student['words_from_class_teacher'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- Principal's Words -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="info-label">Principal's Words</h6>
                                <p><?php echo nl2br(htmlspecialchars($student['words_from_principal'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Updated Exams Section -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="section-title mb-0">
                    <i class="bi bi-pencil-square me-2"></i>Examination Records
                </h5>
            </div>
            <div class="scrollable-wrapper">
                <?php if (!empty($exams)): ?>
                    <div class="row">
                        <?php foreach ($exams as $exam): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="bi bi-calendar-event me-2"></i>
                                                <?php echo date('F d, Y', strtotime($exam['exam_date'])); ?>
                                            </h6>
                                            <span class="badge bg-success">
                                                EID: <?php echo number_format($exam['exam_id'], 2); ?>
                                            </span>
                                            <span class="badge bg-primary">
                                                GPA: <?php echo number_format($exam['GPA'], 2); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <!-- Subjects Performance -->
                                            <div class="col-12">
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <div class="p-2 bg-danger bg-opacity-10 rounded">
                                                            <small class="text-danger d-block fw-bold mb-1">Failed Subjects</small>
                                                            <p class="mb-0 small">
                                                                <?php echo htmlspecialchars($exam['failed_subjects'] ?: 'None'); ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="p-2 bg-success bg-opacity-10 rounded">
                                                            <small class="text-success d-block fw-bold mb-1">Passed Subjects</small>
                                                            <p class="mb-0 small">
                                                                <?php echo htmlspecialchars($exam['passed_subjects']); ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Remarks -->
                                            <div class="col-12">
                                                <div class="p-2 bg-light rounded">
                                                    <small class="text-muted d-block fw-bold mb-1">Remarks</small>
                                                    <p class="mb-0 small">
                                                        <?php echo nl2br(htmlspecialchars($exam['remarks'])); ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Parents' View -->
                                            <?php if (!empty($exam['parents_view'])): ?>
                                            <div class="col-12">
                                                <div class="p-2 bg-info bg-opacity-10 rounded">
                                                    <small class="text-info d-block fw-bold mb-1">Parents' Feedback</small>
                                                    <p class="mb-0 small">
                                                        <?php echo nl2br(htmlspecialchars($exam['parents_view'])); ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <!-- Admin Information -->
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person-badge me-1"></i>
                                                        Recorded by: <?php echo htmlspecialchars($exam['admin_name'] ?: 'N/A'); ?>
                                                    </small>
                                                    <a href="edit_exam.php?id=<?php echo $exam['id']; ?>" 
                                                       class="btn btn-sm btn-edit">
                                                        <i class="bi bi-pencil-fill me-1"></i>Edit
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        No examination records found for this student.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Achievements Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-trophy me-2"></i>Achievements
                        </h5>
                    </div>
                    <div class="scrollable-wrapper">
                        <?php if (!empty($achievements)): ?>
                            <?php foreach ($achievements as $achievement): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6><?php echo htmlspecialchars($achievement['title']); ?></h6>
                                        <p class="mb-2"><strong>Awarded by:</strong> <?php echo htmlspecialchars($achievement['awarded_by']); ?></p>
                                        <p class="mb-3"><strong>Date:</strong> <?php echo htmlspecialchars($achievement['date_of_achievement']); ?></p>
                                        <a href="edit_achievement.php?id=<?php echo $achievement['id']; ?>" class="btn btn-edit">Edit</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="m-3">No achievements recorded.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Reports Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-file-text me-2"></i>Reports
                        </h5>
                    </div>
                    <div class="scrollable-wrapper">
                        <?php if (!empty($reports)): ?>
                            <?php foreach ($reports as $report): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">
                                                <i class="bi bi-calendar3 me-2"></i>
                                                <?php echo date('F d, Y', strtotime($report['report_date'])); ?>
                                            </h6>
                                        </div>
                                        <div class="p-2 bg-light rounded mb-3">
                                            <small class="text-muted d-block fw-bold mb-1">Admin Notes</small>
                                            <p class="mb-0 small">
                                                <?php echo nl2br(htmlspecialchars($report['admin_notes'])); ?>
                                            </p>
                                        </div>
                                        <div class="p-2 bg-info bg-opacity-10 rounded mb-3">
                                            <small class="text-info d-block fw-bold mb-1">Visit Report</small>
                                            <p class="mb-0 small">
                                                <?php echo nl2br(htmlspecialchars($report['visit_report'])); ?>
                                            </p>
                                        </div>
                                        <a href="edit_report.php?id=<?php echo $report['report_id']; ?>" 
                                           class="btn btn-sm btn-edit">
                                            <i class="bi bi-pencil-fill me-1"></i>Edit Report
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="m-3">No reports available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Remarks Section -->
        <?php if (!empty($student['remarks'])): ?>
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h5 class="section-title mb-0">
                    <i class="bi bi-chat-square-text me-2"></i>Additional Remarks
                </h5>
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($student['remarks'])); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            No student found matching the query. Please try a different search term.
        </div>
    <?php endif; ?>
</div>

</body>
</html>

<?php include 'footer.php'; ?>