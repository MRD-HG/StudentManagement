<?php
include '../config/db.php';
$keyword = $_GET['keyword'] ?? '';
$query = $pdo->prepare("SELECT * FROM students WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?");
$query->execute(["%$keyword%", "%$keyword%", "%$keyword%"]);
$results = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<form method="get">
    Search: <input name="keyword" value="<?= htmlspecialchars($keyword) ?>">
    <button type="submit">Search</button>
</form>
<?php if ($keyword): ?>
<h3>Search Results for '<?= htmlspecialchars($keyword) ?>'</h3>
<table border="1">
<tr><th>ID</th><th>Name</th><th>Email</th></tr>
<?php foreach ($results as $student): ?>
<tr>
    <td><?= $student['student_id'] ?></td>
    <td><?= $student['first_name'] . ' ' . $student['last_name'] ?></td>
    <td><?= $student['email'] ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>