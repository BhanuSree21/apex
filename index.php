<?php
// Start the session at the very top of the page
session_start();

// Include the database connection file
include 'db.php';

// Check if a user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// --- Implement Search & Pagination Logic ---

// Pagination variables
$posts_per_page = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $posts_per_page;

// Search variables
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_query = "WHERE title LIKE :search_term OR content LIKE :search_term";
}

// Get the total number of posts with the current search query
$total_posts_stmt = $conn->prepare("SELECT COUNT(*) FROM posts $search_query");
if (isset($search_term)) {
    $total_posts_stmt->bindParam(':search_term', $search_term);
}
$total_posts_stmt->execute();
$total_posts = $total_posts_stmt->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);

// Fetch posts for the current page, applying both search and pagination
$stmt = $conn->prepare("SELECT * FROM posts $search_query ORDER BY created_at DESC LIMIT $posts_per_page OFFSET $offset");
if (isset($search_term)) {
    $stmt->bindParam(':search_term', $search_term);
}
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Blog Posts</h1>
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search posts..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit">Search</button>
            </form>
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

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php
                    // Build the URL, keeping the search term if one exists
                    $link = "index.php?page=$i";
                    if (isset($_GET['search'])) {
                        $link .= "&search=" . urlencode($_GET['search']);
                    }
                    ?>
                    <a href="<?= $link ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
