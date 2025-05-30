<?php
include '../config/db.php'; // Database connection

// Handle course deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    if (filter_var($delete_id, FILTER_VALIDATE_INT)) {
        try {
            // Deletion from 'courses' will cascade to 'course_teacher_assignments'
            // if FOREIGN KEY ... ON DELETE CASCADE is set.
            $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = ?");
            $stmt->execute([$delete_id]);
            header("Location: list.php?status=deleted");
            exit;
        } catch (PDOException $e) {
            // Log error $e->getMessage()
            header("Location: list.php?status=error_delete");
            exit;
        }
    } else {
        header("Location: list.php?status=error_invalid_id");
        exit;
    }
}

// Fetch courses with assigned teachers
$courses = [];
$db_error_message = null;
try {
    $courses_stmt = $pdo->query("
        SELECT c.course_id, c.course_name, c.credits, 
               GROUP_CONCAT(DISTINCT CONCAT(t.first_name, ' ', t.last_name) ORDER BY t.first_name SEPARATOR ', ') AS assigned_teachers_list
        FROM courses c
        LEFT JOIN course_teacher_assignments cta ON c.course_id = cta.course_id
        LEFT JOIN teachers t ON cta.teacher_id = t.teacher_id
        GROUP BY c.course_id, c.course_name, c.credits
        ORDER BY c.course_name ASC
    ");
    $courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_error_message = "Database error: Could not fetch courses. " . $e->getMessage();
    // Log error $e->getMessage()
}

$page_title = "Manage Courses";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Course Management</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><?= htmlspecialchars($page_title) ?></h2>
        <a href="../index.php" class="btn btn-light">Back to Main Menu</a>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'deleted'): ?>
            <div class="alert alert-success">Course deleted successfully!</div>
        <?php elseif ($_GET['status'] === 'updated'): ?>
            <div class="alert alert-success">Course updated successfully! (Refresh to see if teacher list changed if you were just on edit page)</div>
        <?php elseif ($_GET['status'] === 'added'): ?>
            <div class="alert alert-success">Course added successfully!</div>
        <?php elseif (str_starts_with($_GET['status'], 'error')): ?>
            <div class="alert alert-danger">An error occurred: <?= htmlspecialchars(str_replace('_', ' ', substr($_GET['status'], 6))) ?>.</div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($db_error_message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($db_error_message) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div class="page-actions justify-content-end">
                <a href="add.php" class="btn btn-primary">Add New Course</a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($courses) && !$db_error_message): ?>
                <div class="alert alert-info mt-3">No courses found. <a href="add.php" class="btn-link">Add the first course!</a></div>
            <?php elseif (!empty($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Credits</th>
                                <th>Assigned Teachers</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                            <tr>
                                <td data-label="Course Name"><?= htmlspecialchars($course['course_name']) ?></td>
                                <td data-label="Credits"><?= htmlspecialchars($course['credits']) ?></td>
                                <td data-label="Assigned Teachers">
                                    <?= !empty($course['assigned_teachers_list']) ? htmlspecialchars($course['assigned_teachers_list']) : '<em>None assigned</em>' ?>
                                </td>
                                <td data-label="Actions" class="actions">
                                    <a href="edit.php?id=<?= $course['course_id'] ?>" class="btn btn-warning btn-sm">Edit / Assign</a>
                                    <a href="list.php?action=delete&id=<?= $course['course_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course and all its teacher assignments? This action cannot be undone.')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif (!$db_error_message): ?>
                 <div class="alert alert-info mt-3">No courses to display.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
