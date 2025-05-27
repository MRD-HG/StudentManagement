<?php
include '../config/db.php'; // Includes the database connection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare SQL statement to prevent SQL injection
    $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email) VALUES (?, ?, ?)");
    // Execute the statement with data from the form
    $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email']]);
    // Redirect to the students list page
    header("Location: list.php");
    exit; // It's a good practice to call exit after a redirect
}
?>

<h2>Add New Student</h2>
<form method="post">
    First Name: <input type="text" name="first_name" required><br>
    Last Name: <input type="text" name="last_name" required><br>
    Email: <input type="email" name="email" required><br>
    <button type="submit">Add Student</button>
</form>
<br>
<a href="list.php">Back to Students List</a>