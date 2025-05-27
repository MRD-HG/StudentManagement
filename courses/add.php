<?php
include '../config/db.php'; // Database connection
$page_title = "Add New Course";
$error_message = '';
$form_data = ['course_name' => '', 'credits' => '']; // For repopulating form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['course_name'] = trim($_POST['course_name'] ?? '');
    $form_data['credits'] = trim($_POST['credits'] ?? '');

    if (empty($form_data['course_name'])) {
        $error_message = "Course Name is required.";
    } elseif (!is_numeric($form_data['credits']) || $form_data['credits'] < 0) {
        $error_message = "Credits must be a non-negative number.";
    } else {
        try {
            // Check if course name already exists
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE course_name = ?");
            $check_stmt->execute([$form_data['course_name']]);
            if ($check_stmt->fetchColumn() > 0) {
                $error_message = "A course with this name already exists.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO courses (course_name, credits) VALUES (?, ?)");
                $stmt->execute([$form_data['course_name'], $form_data['credits']]);
                header("Location: list.php?status=added");
                exit;
            }
        } catch (PDOException $e) {
            $error_message = "Database error: Could not add course. " . $e->getMessage();
            // Log error $e->getMessage() for debugging
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
    <div class="d-flex justify-content-between align-items-center mb-3">
         <h2><?= htmlspecialchars($page_title) ?></h2>
        <a href="list.php" class="btn btn-light">Back to Courses List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form method="post" action="add.php">
                <div class="form-group">
                    <label for="course_name">Course Name:</label>
                    <input type="text" id="course_name" name="course_name" class="form-control" value="<?= htmlspecialchars($form_data['course_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="credits">Credits:</label>
                    <input type="number" id="credits" name="credits" class="form-control" value="<?= htmlspecialchars($form_data['credits']) ?>" required min="0" step="0.5">
                </div>
                
                <button type="submit" class="btn btn-primary">Add Course</button>
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
