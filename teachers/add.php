<?php
include '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO teachers (first_name, last_name, email, department) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['department']]);
    header("Location: list.php");
}
?>
<form method="post">
    First Name: <input name="first_name"><br>
    Last Name: <input name="last_name"><br>
    Email: <input name="email"><br>
    Department: <input name="department"><br>
    <button type="submit">Add Teacher</button>
</form>