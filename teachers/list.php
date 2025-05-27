<?php
include '../config/db.php'; //

// Handle teacher deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    // Basic validation: ensure id is a number
    if (filter_var($delete_id, FILTER_VALIDATE_INT)) {
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE teacher_id = ?");
        $stmt->execute([$delete_id]);
        header("Location: list.php?status=deleted");
        exit;
    } else {
        header("Location: list.php?status=error");
        exit;
    }
}

$teachers = $pdo->query("SELECT * FROM teachers ORDER BY teacher_id DESC")->fetchAll(PDO::FETCH_ASSOC); //
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teachers List</title>
    </head>
<body>
    <h2>Teachers List</h2>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'deleted'): ?>
            <p style="color:green;">Teacher deleted successfully!</p>
        <?php elseif ($_GET['status'] === 'added'): ?>
            <p style="color:green;">Teacher added successfully!</p>
        <?php elseif ($_GET['status'] === 'updated'): ?>
            <p style="color:green;">Teacher updated successfully!</p>
        <?php elseif ($_GET['status'] === 'error'): ?>
            <p style="color:red;">An error occurred.</p>
        <?php endif; ?>
    <?php endif; ?>

    <a href="add.php">Add New Teacher</a> <?php if (empty($teachers)): ?>
        <p>No teachers found. <a href="add.php">Add the first teacher!</a></p>
    <?php else: ?>
        <table border="1">
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Department</th><th>Actions</th></tr>
            <?php foreach ($teachers as $teacher): ?>
            <tr>
                <td><?= htmlspecialchars($teacher['teacher_id']) ?></td> <td><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></td> <td><?= htmlspecialchars($teacher['email']) ?></td> <td><?= htmlspecialchars($teacher['department']) ?></td> <td>
                    <a href="edit.php?id=<?= $teacher['teacher_id'] ?>">Edit</a> | <a href="list.php?action=delete&id=<?= $teacher['teacher_id'] ?>" onclick="return confirm('Are you sure you want to delete this teacher? This action cannot be undone.')">Delete</a> </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <br>
    <a href="../index.php">Back to Main Menu</a>
</body>
</html>