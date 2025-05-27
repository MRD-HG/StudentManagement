<?php
include '../config/db.php'; //
$page_title = "Add New Student";
$error_message = '';
$form_data = ['first_name' => '', 'last_name' => '', 'email' => '']; // For repopulating form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['first_name'] = $_POST['first_name'] ?? '';
    $form_data['last_name'] = $_POST['last_name'] ?? '';
    $form_data['email'] = $_POST['email'] ?? '';

    if (empty($form_data['first_name']) || empty($form_data['last_name']) || empty($form_data['email'])) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email) VALUES (?, ?, ?)");
            $stmt->execute([$form_data['first_name'], $form_data['last_name'], $form_data['email']]);
            header("Location: list.php?status=added");
            exit;
        } catch (PDOException $e) {
            // Check for duplicate email if you have a unique constraint on the email column
            if ($e->errorInfo[1] == 1062) { // Error code for duplicate entry
                $error_message = "This email address is already registered.";
            } else {
                $error_message = "Database error: Could not add student. " . $e->getMessage();
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
    <title><?= htmlspecialchars($page_title) ?> - Student Management</title>
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
                
                <button type="submit" class="btn btn-primary">Add Student</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
    <div class="mt-3">
        <a href="list.php" class="btn btn-light">Back to Students List</a>
        <a href="../index.php" class="btn btn-light">Back to Main Menu</a>
    </div>
</div>
</body>
</html>