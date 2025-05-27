<?php
include '../config/db.php'; //

// Handle student deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    if (filter_var($delete_id, FILTER_VALIDATE_INT)) {
        try {
            $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
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

$keyword = $_GET['keyword'] ?? ''; //
$search_sql_condition = "";
$params = [];

if (!empty($keyword)) {
    $search_sql_condition = " WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?"; //
    $params = ["%$keyword%", "%$keyword%", "%$keyword%"]; //
}

try {
    $stmt = $pdo->prepare("SELECT * FROM students" . $search_sql_condition . " ORDER BY student_id DESC");
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC); //
} catch (PDOException $e) {
    $students = []; // Prevent errors if DB query fails
    $db_error_message = "Database error: Could not fetch students.";
}


$page_title = "Manage Students";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Student Management</title>
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
            <div class="alert alert-success">Student deleted successfully!</div>
        <?php elseif ($_GET['status'] === 'updated'): ?>
            <div class="alert alert-success">Student updated successfully!</div>
        <?php elseif ($_GET['status'] === 'added'): ?>
            <div class="alert alert-success">Student added successfully!</div>
        <?php elseif (str_starts_with($_GET['status'], 'error')): ?>
            <div class="alert alert-danger">An error occurred. (<?= htmlspecialchars($_GET['status']) ?>)</div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (isset($db_error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($db_error_message) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div class="page-actions">
                <form method="get" action="list.php" class="search-form">
                    <input type="text" name="keyword" class="form-control" value="<?= htmlspecialchars($keyword) ?>" placeholder="Search students...">
                    <button type="submit" class="btn btn-info">Search</button>
                    <?php if ($keyword): ?>
                        <a href="list.php" class="btn btn-secondary btn-sm">Clear</a>
                    <?php endif; ?>
                </form>
                <a href="add.php" class="btn btn-primary">Add New Student</a>
            </div>
        </div>
        <div class="card-body">
            <?php if ($keyword && empty($students) && !isset($db_error_message)): ?>
                <div class="alert alert-warning mt-3">No results found for '<?= htmlspecialchars($keyword) ?>'.</div>
            <?php elseif (empty($students) && empty($keyword) && !isset($db_error_message)): ?>
                <div class="alert alert-info mt-3">No students found. <a href="add.php" class="btn-link">Add the first student!</a></div>
            <?php elseif (!empty($students)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td data-label="ID"><?= htmlspecialchars($student['student_id']) ?></td>
                                <td data-label="First Name"><?= htmlspecialchars($student['first_name']) ?></td>
                                <td data-label="Last Name"><?= htmlspecialchars($student['last_name']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($student['email']) ?></td>
                                <td data-label="Actions" class="actions">
                                    <a href="edit.php?id=<?= $student['student_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="list.php?action=delete&id=<?= $student['student_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif (!isset($db_error_message)): ?>
                 <div class="alert alert-info mt-3">No students to display.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>