<?php
include '../config/db.php'; // Database connection
$page_title = "Edit Course";
$error_message = '';
$success_message = ''; // For general success messages if needed
$course_id = $_GET['id'] ?? null;
$course = null;

if (!$course_id || !filter_var($course_id, FILTER_VALIDATE_INT)) {
    header("Location: list.php?status=error_invalid_id");
    exit;
}

// Fetch course data
try {
    $stmt_course = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt_course->execute([$course_id]);
    $course = $stmt_course->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log error $e->getMessage()
    header("Location: list.php?status=error_db_fetch");
    exit;
}

if (!$course) {
    header("Location: list.php?status=error_notfound");
    exit;
}

// Fetch all teachers for assignment
try {
    $all_teachers_stmt = $pdo->query("SELECT teacher_id, first_name, last_name FROM teachers ORDER BY first_name, last_name");
    $all_teachers = $all_teachers_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_teachers = [];
    $error_message .= " Could not load teachers list. " . $e->getMessage();
    // Log error $e->getMessage()
}

// Fetch current teacher assignments for this course
$assigned_teacher_ids = [];
try {
    $assigned_teachers_stmt = $pdo->prepare("SELECT teacher_id FROM course_teacher_assignments WHERE course_id = ?");
    $assigned_teachers_stmt->execute([$course_id]);
    $assigned_teacher_ids = $assigned_teachers_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $error_message .= " Could not load assigned teachers. " . $e->getMessage();
    // Log error $e->getMessage()
}


$form_data = [
    'course_name' => $course['course_name'], 
    'credits' => $course['credits']
];
$page_title = "Edit Course: " . htmlspecialchars($course['course_name']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['course_name'] = trim($_POST['course_name'] ?? '');
    $form_data['credits'] = trim($_POST['credits'] ?? '');
    $selected_teacher_ids_form = $_POST['teacher_ids'] ?? [];

    // Validate course details
    if (empty($form_data['course_name'])) {
        $error_message = "Course Name is required.";
    } elseif (!is_numeric($form_data['credits']) || $form_data['credits'] < 0) {
        $error_message = "Credits must be a non-negative number.";
    } else {
        $pdo->beginTransaction(); // Start transaction
        try {
            // Check if course name already exists (and it's not the current course)
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE course_name = ? AND course_id != ?");
            $check_stmt->execute([$form_data['course_name'], $course_id]);
            if ($check_stmt->fetchColumn() > 0) {
                $error_message = "Another course with this name already exists.";
                 $pdo->rollBack();
            } else {
                // Update course details
                $update_course_stmt = $pdo->prepare("UPDATE courses SET course_name = ?, credits = ? WHERE course_id = ?");
                $update_course_stmt->execute([$form_data['course_name'], $form_data['credits'], $course_id]);

                // Update teacher assignments
                // 1. Remove existing assignments for this course
                $delete_assignments_stmt = $pdo->prepare("DELETE FROM course_teacher_assignments WHERE course_id = ?");
                $delete_assignments_stmt->execute([$course_id]);

                // 2. Add new assignments
                if (!empty($selected_teacher_ids_form)) {
                    $assign_stmt = $pdo->prepare("INSERT INTO course_teacher_assignments (course_id, teacher_id) VALUES (?, ?)");
                    foreach ($selected_teacher_ids_form as $tid) {
                        if (filter_var($tid, FILTER_VALIDATE_INT)) {
                            $assign_stmt->execute([$course_id, $tid]);
                        }
                    }
                }
                $pdo->commit(); // Commit transaction
                header("Location: list.php?status=updated&id=" . $course_id); // Redirect to list or stay on page with success
                exit;
            }
        } catch (PDOException $e) {
            $pdo->rollBack(); // Rollback on error
            $error_message = "Database error: Could not update course. " . $e->getMessage();
            // Log error $e->getMessage()
        }
    }
    // If validation fails, $assigned_teacher_ids should reflect the submitted values for repopulation
    $assigned_teacher_ids = array_map('intval', $selected_teacher_ids_form);
}
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
        <a href="list.php" class="btn btn-light">Back to Courses List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['status']) && $_GET['status'] === 'updated_success'): ?>
                <div class="alert alert-success">Course and assignments updated successfully!</div>
            <?php endif; ?>


            <form method="post" action="edit.php?id=<?= htmlspecialchars($course_id) ?>">
                <div class="form-group">
                    <label for="course_name">Course Name:</label>
                    <input type="text" id="course_name" name="course_name" class="form-control" value="<?= htmlspecialchars($form_data['course_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="credits">Credits:</label>
                    <input type="number" id="credits" name="credits" class="form-control" value="<?= htmlspecialchars($form_data['credits']) ?>" required min="0" step="0.5">
                </div>
                
                <hr>
                <h4>Assign Teachers</h4>
                <div class="form-group">
                    <?php if (empty($all_teachers)): ?>
                        <p>No teachers available to assign. <a href="../teachers/add.php" class="btn-link">Add a teacher first.</a></p>
                    <?php else: ?>
                        <div class="teacher-assignment-list" style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; padding: 10px; border-radius: var(--border-radius);">
                        <?php foreach ($all_teachers as $teacher_option): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="teacher_ids[]" value="<?= $teacher_option['teacher_id'] ?>" id="teacher_<?= $teacher_option['teacher_id'] ?>"
                                    <?= in_array($teacher_option['teacher_id'], $assigned_teacher_ids) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="teacher_<?= $teacher_option['teacher_id'] ?>">
                                    <?= htmlspecialchars($teacher_option['first_name'] . ' ' . $teacher_option['last_name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Course & Assignments</button>
                <a href="list.php" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
    <div class="mt-3 text-center">
        <a href="../index.php" class="btn btn-light">Back to Main Menu</a>
    </div>
</div>
</body>
</html>
