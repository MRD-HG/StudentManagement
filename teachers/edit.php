<?php
include '../config/db.php';
$teacher_id = $_GET['id'] ?? null;

if (!$teacher_id || !filter_var($teacher_id, FILTER_VALIDATE_INT)) {
    header("Location: list.php?status=error"); 
    exit;
}

// Handle form submission for updating teacher
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs (basic example)
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['department'])) {
        $error = "All fields are required.";
        // Fetch the teacher's current data again to repopulate the form
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
        $stmt->execute([$teacher_id]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$teacher) { // Should not happen if ID was valid initially
            header("Location: list.php?status=notfound");
            exit;
        }
    } else {
        $stmt = $pdo->prepare("UPDATE teachers SET first_name = ?, last_name = ?, email = ?, department = ? WHERE teacher_id = ?");
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['department'], $teacher_id]);
        header("Location: list.php?status=updated"); 
        exit;
    }
} else {
    // Fetch the teacher's current data for form pre-filling
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$teacher) {
        header("Location: list.php?status=notfound");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Teacher</title>
    </head>
<body>
    <h2>Edit Teacher: <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></h2>
    
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        First Name: <input type="text" name="first_name" value="<?= htmlspecialchars($teacher['first_name']) ?>" required><br>
        Last Name: <input type="text" name="last_name" value="<?= htmlspecialchars($teacher['last_name']) ?>" required><br>
        Email: <input type="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" required><br>
        Department: <input type="text" name="department" value="<?= htmlspecialchars($teacher['department']) ?>" required><br>
        <button type="submit">Update Teacher</button>
    </form>
    <br>
    <a href="list.php">Cancel and Back to Teachers List</a>
    <br>
    <a href="../index.php">Back to Main Menu</a>
</body>
</html>