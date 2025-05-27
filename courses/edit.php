<?php
include '../config/db.php'; //
$page_title = "Edit Course";
$error_message = '';
$course_id = $_GET['id'] ?? null;
$course = null;

if (!$course_id || !filter_var($course_id, FILTER_VALIDATE_INT)) {
    header("Location: list.php?status=error_invalid_id");
    exit;
}

// Fetch course data
try {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header("Location: list.php?status=error_db_fetch");
    exit;
}

if (!$course) {
    header("Location: list.php?status=error_notfound");
    exit;
}

$form_data = [
    'course_name' => $course['course_name'], 
    'credits' => $course['credits']
];
$page_title = "Edit Course: " . htmlspecialchars($course['course_name']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['course_name'] = $_POST['course_name'] ?? '';
    $form_data['credits'] = $_POST['credits'] ?? '';

    if (empty($form_data['course_name']) || !isset($form_data['credits']) || !is_numeric($form_data['credits'])) {
        $error_message = "Course Name is required and Credits must be a valid number.";
    } elseif ($form_data['credits'] < 0) {
        $error_message = "Credits cannot be negative.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE courses SET course_name = ?, credits = ? WHERE course_id = ?");
            $stmt->execute([$form_data['course_name'], $form_data['credits'], $course_id]);
            header("Location: list.php?status=updated");
            exit;
        } catch (PDOException $e) {
             if ($e->errorInfo[1] == 1062) { // Assuming course_name is unique
                $error_message = "A course with this name already exists.";
            } else {
                $error_message = "Database error: Could not update course.";
            }
        }
    }
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
    <div class="card">
        <div class="card-header">
            <h2><?= htmlspecialchars($page_title) ?></h2>
        </div>
        <div class="card-body">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form method="post" action="edit.php?id=<?= htmlspecialchars($course_id) ?>">
                <div class="form-group">
                    <label for="course_name">Course Name:</label>
                    <input type="text" id="course_name" name="course_name" class="form-control" value="<?= htmlspecialchars($form_data['course_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="credits">Credits:</label>
                    <input type="number" id="credits" name="credits" class="form-control" value="<?= htmlspecialchars($form_data['credits']) ?>" required min="0">
                </div>
                
                <button type="submit" class="btn btn-primary">Update Course</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
    <div class="mt-3">
        <a href="list.php" class="btn btn-light">Back to Courses List</a>
        <a href="../index.php" class="btn btn-light">Back to Main Menu</a>
    </div>
</div>
</body>
</html>