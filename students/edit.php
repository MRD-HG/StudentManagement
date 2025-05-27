<?php
include '../config/db.php'; // Includes the database connection
$student_id = $_GET['id'] ?? null;

if (!$student_id) {
    header("Location: list.php"); // Redirect if no ID is provided
    exit;
}

// Handle form submission for updating student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, email = ? WHERE student_id = ?");
    $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $student_id]);
    header("Location: list.php?status=updated"); // Redirect after update
    exit;
}

// Fetch the student's current data
$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "Student not found.";
    echo '<br><a href="list.php">Back to Students List</a>';
    exit;
}
?>

<h2>Edit Student: <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h2>
<form method="post">
    First Name: <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required><br>
    Last Name: <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" required><br>
    Email: <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required><br>
    <button type="submit">Update Student</button>
</form>
<br>
<a href="list.php">Cancel and Back to Students List</a>