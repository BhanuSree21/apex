<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// The rest of your existing PHP code for creating a post follows here...
// For example:
// include 'db.php';
// if ($_SERVER['REQUEST_METHOD'] == 'POST') { ... }
?>

<!-- The rest of your HTML form follows here... -->
<?php
include 'db.php';
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);

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
</head>
<body>
    <h1>Edit Post</h1>
    <form method="POST">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required><br><br>
        <label>Content:</label><br>
        <textarea name="content" required><?= htmlspecialchars($post['content']) ?></textarea><br><br>
        <button type="submit">Update Post</button>
    </form>
</body>
</html>