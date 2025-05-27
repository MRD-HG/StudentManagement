<?php
include '../config/db.php'; //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['course_name']) || !isset($_POST['credits']) || !is_numeric($_POST['credits'])) {
        $error = "Course Name is required and Credits must be a number.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO courses (course_name, credits) VALUES (?, ?)"); //
        $stmt->execute([$_POST['course_name'], $_POST['credits']]); //
        header("Location: list.php?status=added"); //
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Course</title>
    </head>
<body>
    <h2>Add New Course</h2>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
        Course Name: <input type="text" name="course_name" value="<?= htmlspecialchars($_POST['course_name'] ?? '') ?>" required><br>
        Credits: <input type="number" name="credits" value="<?= htmlspecialchars($_POST['credits'] ?? '') ?>" required><br>
        <button type="submit">Add Course</button>
    </form>
    <br>
    <a href="list.php">Back to Courses List</a>
    <br>
    <a href="../index.php">Back to Main Menu</a>
</body>
</html>