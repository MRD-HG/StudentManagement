<?php
include '../config/db.php';
$course_id = $_GET['id'] ?? null;

if (!$course_id || !filter_var($course_id, FILTER_VALIDATE_INT)) {
    header("Location: list.php?status=error");
    exit;
}

// Handle form submission for updating course
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['course_name']) || !isset($_POST['credits']) || !is_numeric($_POST['credits'])) {
        $error = "Course Name is required and Credits must be a number.";
        // Fetch the course's current data again to repopulate the form
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$course) {
            header("Location: list.php?status=notfound");
            exit;
        }
    } else {
        $stmt = $pdo->prepare("UPDATE courses SET course_name = ?, credits = ? WHERE course_id = ?");
        $stmt->execute([$_POST['course_name'], $_POST['credits'], $course_id]);
        header("Location: list.php?status=updated");
        exit;
    }
} else {
    // Fetch the course's current data
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        header("Location: list.php?status=notfound");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Course</title>
    </head>
<body>
    <h2>Edit Course: <?= htmlspecialchars($course['course_name']) ?></h2>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        Course Name: <input type="text" name="course_name" value="<?= htmlspecialchars($course['course_name']) ?>" required><br>
        Credits: <input type="number" name="credits" value="<?= htmlspecialchars($course['credits']) ?>" required><br>
        <button type="submit">Update Course</button>
    </form>
    <br>
    <a href="list.php">Cancel and Back to Courses List</a>
    <br>
    <a href="../index.php">Back to Main Menu</a>
</body>
</html>