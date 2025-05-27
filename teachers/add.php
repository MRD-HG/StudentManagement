<?php
include '../config/db.php'; //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs (basic example)
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['department'])) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO teachers (first_name, last_name, email, department) VALUES (?, ?, ?, ?)"); //
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['department']]); //
        header("Location: list.php?status=added"); //
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Teacher</title>
    </head>
<body>
    <h2>Add New Teacher</h2>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
        First Name: <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required><br>
        Last Name: <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required><br>
        Email: <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required><br>
        Department: <input type="text" name="department" value="<?= htmlspecialchars($_POST['department'] ?? '') ?>" required><br>
        <button type="submit">Add Teacher</button>
    </form>
    <br>
    <a href="list.php">Back to Teachers List</a>
    <br>
    <a href="../index.php">Back to Main Menu</a>
</body>
</html>