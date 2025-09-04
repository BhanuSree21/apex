<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and trim the user input
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    // Server-side validation check
    if (empty($title) || empty($content)) {
        echo "<p style='color:red;'>Title and content cannot be empty.</p>";
    } else {
        // If validation passes, run your database query
        $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (:title, :content)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->execute();
        header('Location: index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Create New Post</h1>
        <form method="POST">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br><br>
            <label for="content">Content:</label>
            <textarea id="content" name="content" required></textarea><br><br>
            <button type="submit">Save Post</button>
        </form>
    </div>
</body>
</html>