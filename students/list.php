<?php
include '../config/db.php'; // Includes the database connection

// Handle student deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    // It's crucial to confirm before deleting sensitive data.
    // The onclick confirm in the link provides client-side confirmation.
    // Adding a server-side check or a separate confirmation step is even better for critical operations.
    $delete_id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->execute([$delete_id]);
    // Redirect to remove the action and id from URL and prevent re-deletion on refresh
    header("Location: list.php?status=deleted");
    exit;
}

$keyword = $_GET['keyword'] ?? ''; // Get search keyword, if any
$search_sql_condition = "";
$params = [];

if (!empty($keyword)) {
    $search_sql_condition = " WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?";
    $params = ["%$keyword%", "%$keyword%", "%$keyword%"];
}

// Fetch students from the database
$stmt = $pdo->prepare("SELECT * FROM students" . $search_sql_condition . " ORDER BY student_id DESC");
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Students List</h2>

<form method="get" action="list.php">
    Search: <input name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Enter name or email">
    <button type="submit">Search</button>
    <?php if ($keyword): ?>
        <a href="list.php">Clear Search</a>
    <?php endif; ?>
</form>
<br>

<a href="add.php">Add New Student</a>

<?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
    <p style="color:green;">Student deleted successfully!</p>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
    <p style="color:green;">Student updated successfully!</p>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'added'): ?>
    <p style="color:green;">Student added successfully! Redirected from add.php (though actual redirect is to list.php)</p>
<?php endif; ?>


<?php if ($keyword && empty($students)): ?>
    <h3>No results found for '<?= htmlspecialchars($keyword) ?>'.</h3>
<?php elseif (empty($students) && empty($keyword)): ?>
    <p>No students found. <a href="add.php">Add the first student!</a></p>
<?php else: ?>
    <?php if ($keyword): ?>
        <h3>Search Results for '<?= htmlspecialchars($keyword) ?>'</h3>
    <?php endif; ?>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($students as $student): ?>
        <tr>
            <td><?= htmlspecialchars($student['student_id']) ?></td>
            <td><?= htmlspecialchars($student['first_name']) ?></td>
            <td><?= htmlspecialchars($student['last_name']) ?></td>
            <td><?= htmlspecialchars($student['email']) ?></td>
            <td>
                <a href="edit.php?id=<?= $student['student_id'] ?>">Edit</a> |
                <a href="list.php?action=delete&id=<?= $student['student_id'] ?>" onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<br>
<a href="../index.php">Back to Main Menu</a>