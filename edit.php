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

// Get the post ID from the URL and fetch the post
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission for updating the post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $stmt = $conn->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <!-- Link to the stylesheet for improved UI -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Post</h1>
        <form method="POST">
            <label>Title:</label><br>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required><br><br>
            <label>Content:</label><br>
            <textarea name="content" required><?= htmlspecialchars($post['content']) ?></textarea><br><br>
            <button type="submit">Update Post</button>
        </form>
    </div>
</body>
</html>
