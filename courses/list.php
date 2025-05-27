<?php
include '../config/db.php';
$courses = $pdo->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Courses List</h2>
<a href="add.php">Add New Course</a>
<table border="1">
<tr><th>ID</th><th>Name</th><th>Credits</th><th>Actions</th></tr>
<?php foreach ($courses as $course): ?>
<tr>
    <td><?= $course['course_id'] ?></td>
    <td><?= $course['course_name'] ?></td>
    <td><?= $course['credits'] ?></td>
    <td>
        <a href="edit.php?id=<?= $course['course_id'] ?>">Edit</a> |
        <a href="delete.php?id=<?= $course['course_id'] ?>" onclick="return confirm('Delete?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>