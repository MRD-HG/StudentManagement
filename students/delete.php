<?php
/*
// This file is an ALTERNATIVE way to handle deletion.
// The current implementation handles deletion directly in list.php for better UX.

include '../config/db.php'; // Includes the database connection
$student_id = $_GET['id'] ?? null;

if (!$student_id) {
    header("Location: list.php"); // Redirect if no ID
    exit;
}

// Optional: Add a confirmation step here if not handled by JavaScript.
// For example, display a form with "Are you sure?" and a confirm button.

$stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
$stmt->execute([$student_id]);

header("Location: list.php?status=deleted"); // Redirect after delete
exit;
*/
?>