<?php
include '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO courses (course_name, credits) VALUES (?, ?)");
    $stmt->execute([$_POST['course_name'], $_POST['credits']]);
    header("Location: list.php");
}
?>
<form method="post">
    Course Name: <input name="course_name"><br>
    Credits: <input type="number" name="credits"><br>
    <button type="submit">Add Course</button>
</form>