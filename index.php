<?php
// Start the session at the very top of the page
session_start();

// Include the database connection file
include 'db.php';

// Check if a user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Fetch all posts from the database
$stmt = $conn->prepare("SELECT * FROM posts ORDER BY created_at DESC");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h1 {
            margin: 0;
            color: #333;
        }
        .auth-links a {
            margin-left: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        .post {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .post:last-child {
            border-bottom: none;
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
        .post-actions a {
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .post-actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Blog Posts</h1>
            <div class="auth-links">
                <?php if ($is_logged_in): ?>
                    <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
                    <a href="create.php">Create New Post</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h2><?= htmlspecialchars($post['title']) ?></h2>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <?php if ($is_logged_in): ?>
                    <div class="post-actions">
                        <a href="edit.php?id=<?= $post['id'] ?>">Edit</a>
                        <a href="delete.php?id=<?= $post['id'] ?>">Delete</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>