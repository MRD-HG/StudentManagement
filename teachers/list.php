<?php
include '../config/db.php';
$teachers = $pdo->query("SELECT * FROM teachers")->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Teachers List</h2>
<a href="add.php">Add New Teacher</a>
<table border="1">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Department</th><th>Actions</th></tr>
<?php foreach ($teachers as $teacher): ?>
<tr>
    <td><?= $teacher['teacher_id'] ?></td>
    <td><?= $teacher['first_name'] . ' ' . $teacher['last_name'] ?></td>
    <td><?= $teacher['email'] ?></td>
    <td><?= $teacher['department'] ?></td>
    <td>
        <a href="edit.php?id=<?= $teacher['teacher_id'] ?>">Edit</a> |
        <a href="delete.php?id=<?= $teacher['teacher_id'] ?>" onclick="return confirm('Delete?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>