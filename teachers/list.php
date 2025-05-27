<?php
include '../config/db.php'; //

// Handle teacher deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    if (filter_var($delete_id, FILTER_VALIDATE_INT)) {
        try {
            $stmt = $pdo->prepare("DELETE FROM teachers WHERE teacher_id = ?");
            $stmt->execute([$delete_id]);
            header("Location: list.php?status=deleted");
            exit;
        } catch (PDOException $e) {
            header("Location: list.php?status=error_delete");
            exit;
        }
    } else {
        header("Location: list.php?status=error_invalid_id");
        exit;
    }
}

try {
    $teachers = $pdo->query("SELECT * FROM teachers ORDER BY teacher_id DESC")->fetchAll(PDO::FETCH_ASSOC); //
} catch (PDOException $e) {
    $teachers = [];
    $db_error_message = "Database error: Could not fetch teachers.";
}

$page_title = "Manage Teachers";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Teacher Management</title>
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
            <div class="alert alert-success">Teacher deleted successfully!</div>
        <?php elseif ($_GET['status'] === 'updated'): ?>
            <div class="alert alert-success">Teacher updated successfully!</div>
        <?php elseif ($_GET['status'] === 'added'): ?>
            <div class="alert alert-success">Teacher added successfully!</div>
        <?php elseif (str_starts_with($_GET['status'], 'error')): ?>
            <div class="alert alert-danger">An error occurred. (<?= htmlspecialchars($_GET['status']) ?>)</div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (isset($db_error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($db_error_message) ?></div>
    <?php endif; ?>

    <div class="card">
         <div class="card-header">
            <div class="page-actions justify-content-end"> {/* Only Add button here, no search for teachers for now */}
                <a href="add.php" class="btn btn-primary">Add New Teacher</a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($teachers) && !isset($db_error_message)): ?>
                <div class="alert alert-info mt-3">No teachers found. <a href="add.php" class="btn-link">Add the first teacher!</a></div>
            <?php elseif (!empty($teachers)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td data-label="ID"><?= htmlspecialchars($teacher['teacher_id']) ?></td>
                                <td data-label="Name"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($teacher['email']) ?></td>
                                <td data-label="Department"><?= htmlspecialchars($teacher['department']) ?></td>
                                <td data-label="Actions" class="actions">
                                    <a href="edit.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="list.php?action=delete&id=<?= $teacher['teacher_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this teacher?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
             <?php elseif (!isset($db_error_message)): ?>
                 <div class="alert alert-info mt-3">No teachers to display.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>