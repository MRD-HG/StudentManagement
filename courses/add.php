<?php
include '../config/db.php'; //
$page_title = "Add New Course";
$error_message = '';
$form_data = ['course_name' => '', 'credits' => '']; // For repopulating

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //
    $form_data['course_name'] = $_POST['course_name'] ?? ''; //
    $form_data['credits'] = $_POST['credits'] ?? ''; //

    if (empty($form_data['course_name']) || !isset($form_data['credits']) || !is_numeric($form_data['credits'])) {
        $error_message = "Course Name is required and Credits must be a valid number.";
    } elseif ($form_data['credits'] < 0) {
        $error_message = "Credits cannot be negative.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (course_name, credits) VALUES (?, ?)"); //
            $stmt->execute([$form_data['course_name'], $form_data['credits']]); //
            header("Location: list.php?status=added"); //
            exit;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) { // Assuming course_name is unique
                $error_message = "A course with this name already exists.";
            } else {
                $error_message = "Database error: Could not add course.";
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

            <form method="post" action="add.php">
                <div class="form-group">
                    <label for="course_name">Course Name:</label>
                    <input type="text" id="course_name" name="course_name" class="form-control" value="<?= htmlspecialchars($form_data['course_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="credits">Credits:</label>
                    <input type="number" id="credits" name="credits" class="form-control" value="<?= htmlspecialchars($form_data['credits']) ?>" required min="0">
                </div>
                
                <button type="submit" class="btn btn-primary">Add Course</button>
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