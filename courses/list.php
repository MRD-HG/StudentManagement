<?php
include '../config/db.php'; //

// Handle course deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    if (filter_var($delete_id, FILTER_VALIDATE_INT)) {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = ?");
        $stmt->execute([$delete_id]);
        header("Location: list.php?status=deleted");
        exit;
    } else {
        header("Location: list.php?status=error");
        exit;
    }
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY course_id DESC")->fetchAll(PDO::FETCH_ASSOC); //
?>
<!DOCTYPE html>
<html>
<head>
    <title>Courses List</title>
    </head>
<body>
    <h2>Courses List</h2>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'deleted'): ?>
            <p style="color:green;">Course deleted successfully!</p>
        <?php elseif ($_GET['status'] === 'added'): ?>
            <p style="color:green;">Course added successfully!</p>
        <?php elseif ($_GET['status'] === 'updated'): ?>
            <p style="color:green;">Course updated successfully!</p>
        <?php elseif ($_GET['status'] === 'error'): ?>
            <p style="color:red;">An error occurred.</p>
        <?php endif; ?>
    <?php endif; ?>

    <a href="add.php">Add New Course</a> <?php if (empty($courses)): ?>
        <p>No courses found. <a href="add.php">Add the first course!</a></p>
    <?php else: ?>
        <table border="1">
            <tr><th>ID</th><th>Name</th><th>Credits</th><th>Actions</th></tr>
            <?php foreach ($courses as $course): ?>
            <tr>
                <td><?= htmlspecialchars($course['course_id']) ?></td> <td><?= htmlspecialchars($course['course_name']) ?></td> <td><?= htmlspecialchars($course['credits']) ?></td> <td>
                    <a href="edit.php?id=<?= $course['course_id'] ?>">Edit</a> | <a href="list.php?action=delete&id=<?= $course['course_id'] ?>" onclick="return confirm('Are you sure you want to delete this course? This action cannot be undone.')">Delete</a> </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <br>
    <a href="../index.php">Back to Main Menu</a>
</body>
</html>