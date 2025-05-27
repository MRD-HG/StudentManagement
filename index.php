<?php 
$page_title = "Welcome"; //
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Student Management System</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container text-center"> {/* Added text-center for overall centering of content */}
        <header class="mt-3 mb-3">
            {/* You can add a logo here if you have one */}
            {/* <img src="path/to/your/logo.png" alt="System Logo" style="max-width: 150px; margin-bottom: 1rem;"> */}
            <h1>Student Management System</h1>
        </header>
        
        <nav class="main-nav mb-3">
            <ul>
                <li><a href="students/list.php" class="btn btn-lg btn-primary">Manage Students</a></li>
<li><a href="courses/list.php" class="btn btn-lg btn-info">Manage Courses</a></li>
<li><a href="teachers/list.php" class="btn btn-lg btn-success">Manage Teachers</a></li>
            </ul>
        </nav>

        <div class="card" style="max-width: 600px; margin: 2rem auto;">
            <div class="card-body">
                <p class="lead">Welcome to the Student Management System.</p>
                <p>Please select a module from the navigation above to begin managing students, courses, or teachers. This system allows for easy creation, viewing, updating, and deletion of records.</p>
            </div>
        </div>

        <footer class="mt-3 pt-3 border-top text-muted">
            <p>&copy; <?= date('Y') ?> Student Management System. All rights reserved.</p>
        </footer>

    </div> </body>
</html>