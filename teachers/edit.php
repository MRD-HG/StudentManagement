<?php
include '../config/db.php'; //
$page_title = "Edit Teacher";
$error_message = '';
$teacher_id = $_GET['id'] ?? null;
$teacher = null;

if (!$teacher_id || !filter_var($teacher_id, FILTER_VALIDATE_INT)) {
    header("Location: list.php?status=error_invalid_id");
    exit;
}

// Fetch teacher data
try {
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header("Location: list.php?status=error_db_fetch");
    exit;
}


if (!$teacher) {
    header("Location: list.php?status=error_notfound");
    exit;
}

$form_data = [
    'first_name' => $teacher['first_name'], 
    'last_name' => $teacher['last_name'], 
    'email' => $teacher['email'],
    'department' => $teacher['department']
];
$page_title = "Edit Teacher: " . htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['first_name'] = $_POST['first_name'] ?? '';
    $form_data['last_name'] = $_POST['last_name'] ?? '';
    $form_data['email'] = $_POST['email'] ?? '';
    $form_data['department'] = $_POST['department'] ?? '';

    if (empty($form_data['first_name']) || empty($form_data['last_name']) || empty($form_data['email']) || empty($form_data['department'])) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE teachers SET first_name = ?, last_name = ?, email = ?, department = ? WHERE teacher_id = ?");
            $stmt->execute([$form_data['first_name'], $form_data['last_name'], $form_data['email'], $form_data['department'], $teacher_id]);
            header("Location: list.php?status=updated");
            exit;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $error_message = "This email address is already registered by another teacher.";
            } else {
                $error_message = "Database error: Could not update teacher.";
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
    <title><?= htmlspecialchars($page_title) ?> - Teacher Management</title>
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

            <form method="post" action="edit.php?id=<?= htmlspecialchars($teacher_id) ?>">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?= htmlspecialchars($form_data['first_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="<?= htmlspecialchars($form_data['last_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($form_data['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="department">Department:</label>
                    <input type="text" id="department" name="department" class="form-control" value="<?= htmlspecialchars($form_data['department']) ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Teacher</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
    <div class="mt-3">
        <a href="list.php" class="btn btn-light">Back to Teachers List</a>
        <a href="../index.php" class="btn btn-light">Back to Main Menu</a>
    </div>
</div>
</body>
</html>